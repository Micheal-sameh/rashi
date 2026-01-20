<?php

namespace App\Repositories;

use App\Enums\CompetitionStatus;
use App\Events\CompetitionStatusUpdated;
use App\Models\Competition;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class CompetitionRepository extends BaseRepository
{
    public function __construct(Competition $model)
    {
        $this->model = $model;
    }

    protected function model(): string
    {
        return Competition::class;
    }

    public bool $pagination = true;

    public int $perPage = 10;

    protected function execute(Builder $query): Collection|LengthAwarePaginator
    {
        return $this->pagination ? $query->paginate($this->perPage) : $query->get();
    }

    public function index()
    {
        $query = $this->model->query()->with(['media'])
            ->where('status', '!=', CompetitionStatus::CANCELLED)
            ->when(request()->is('api/*') && auth()->check(), function ($query) {
                $user = Cache::get('auth_user_'.auth()->id()) ?? auth()->user();
                $userGroupIds = $user->groups->pluck('id') ?? [];

                if ($userGroupIds->isNotEmpty()) {
                    $query->whereHas('groups', fn ($q) => $q->whereIn('groups.id', $userGroupIds));
                }
                $query->where('status', '!=', CompetitionStatus::FINISHED)->orderBy('start_at', 'asc');
            })
            ->when(request()->is('api/*'), fn ($q) => $q->orderByRaw('
                CASE
                    WHEN status = ? THEN 1
                    WHEN status = ? THEN 2
                    WHEN status = ? THEN 3
                    ELSE 4
                END
            ', [
                CompetitionStatus::ACTIVE,
                CompetitionStatus::PENDING,
                CompetitionStatus::FINISHED,
            ]));

        return $this->execute($query);
    }

    public function show($id)
    {
        return $this->findById($id);
    }

    public function store($input, $image)
    {
        $competition = $this->model->create([
            'name' => $input->name,
            'start_at' => Carbon::parse($input->start_at),
            'end_at' => Carbon::parse($input->end_at),
            'status' => $input->start_at > today() ? CompetitionStatus::PENDING : CompetitionStatus::ACTIVE,
        ]);
        $competition->addMedia($image)->toMediaCollection('competitions_images');
        $competition->groups()->sync($input->groups);

        return $competition;
    }

    public function update($id, $input, $image)
    {
        $competition = $this->findById($id);
        $competition->update([
            'name' => $input->name,
            'start_at' => Carbon::parse($input->start_at),
            'end_at' => Carbon::parse($input->end_at),
        ]);
        if ($image) {
            $competition->clearMediaCollection('competitions_images');
            $competition->addMedia($image)->toMediaCollection('competitions_images');
        }

        $competition->groups()->sync($input->groups);

        return $competition;
    }

    public function cancel($id)
    {
        $competition = $this->findById($id);
        $competition->update(['status' => CompetitionStatus::CANCELLED]);
    }

    public function dropdown()
    {
        return $this->model
            ->where('status', '!=', CompetitionStatus::CANCELLED)->get();
    }

    public function changeStatus($id)
    {
        $competition = $this->findById($id);
        $oldStatus = $competition->status;
        $nextStatus = match ($competition->status) {
            CompetitionStatus::PENDING => CompetitionStatus::ACTIVE,
            CompetitionStatus::ACTIVE => CompetitionStatus::FINISHED,
            default => $competition->status,
        };

        $competition->status = $nextStatus;
        $competition->save();

        // Fire event for status update
        event(new CompetitionStatusUpdated($competition, $oldStatus, $nextStatus));

        $statusClass = match ($nextStatus) {
            CompetitionStatus::PENDING => 'btn-primary',
            CompetitionStatus::ACTIVE => 'btn-warning',
            CompetitionStatus::FINISHED => 'btn-purple',
            CompetitionStatus::CANCELLED => 'btn-danger',
            default => 'btn-secondary',
        };
        $status = CompetitionStatus::getStringValue($competition->status);

        return compact('status', 'statusClass');
    }

    public function setStatus($id, $status)
    {
        $competition = $this->findById($id);
        $oldStatus = $competition->status;
        $competition->update(['status' => $status]);

        // Fire event for status update if set to active
        if ($status == CompetitionStatus::ACTIVE) {
            event(new CompetitionStatusUpdated($competition, $oldStatus, $status));
        }

        return $competition;
    }

    public function checkCompetition()
    {
        $today = now()->startOfDay();

        $toActivate = $this->model
            ->where('status', CompetitionStatus::PENDING)
            ->whereDate('start_at', '<=', $today)
            ->whereDate('end_at', '>=', $today)
            ->get();

        foreach ($toActivate as $competition) {
            $oldStatus = $competition->status;
            $competition->update(['status' => CompetitionStatus::ACTIVE]);
            event(new CompetitionStatusUpdated($competition, $oldStatus, $competition->status));
        }

        $this->model
            ->where('status', CompetitionStatus::ACTIVE)
            ->whereDate('end_at', '<', $today)
            ->update(['status' => CompetitionStatus::FINISHED]);
    }

    public function getUsersForCompetition($competition)
    {
        return $competition->quizzes()
            ->join('quiz_questions', 'quizzes.id', '=', 'quiz_questions.quiz_id')
            ->join('user_answers', 'quiz_questions.id', '=', 'user_answers.quiz_question_id')
            ->join('users', 'user_answers.user_id', '=', 'users.id')
            ->select('users.id', 'users.name')
            ->distinct()
            ->orderBy('users.name')
            ->get();
    }
}
