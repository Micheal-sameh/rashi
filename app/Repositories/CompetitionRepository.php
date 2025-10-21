<?php

namespace App\Repositories;

use App\Enums\CompetitionStatus;
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
        $query = $this->model
            ->where('status', '!=', CompetitionStatus::CANCELLED)
            ->when(request()->is('api/*') && auth()->check(), function ($query) {
                $user = Cache::get('auth_user_'.auth()->id()) ?? auth()->user();
                $userGroupIds = $user->groups->pluck('id') ?? [];

                if ($userGroupIds->isNotEmpty()) {
                    $query->whereHas('groups', fn ($q) => $q->whereIn('groups.id', $userGroupIds));
                }
            });

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
        $nextStatus = match ($competition->status) {
            CompetitionStatus::PENDING => CompetitionStatus::ACTIVE,
            CompetitionStatus::ACTIVE => CompetitionStatus::FINISHED,
            default => $competition->status,
        };

        $competition->status = $nextStatus;
        $competition->save();

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

    public function checkCompetition()
    {
        $competitions = $this->model->whereIn('status', [CompetitionStatus::PENDING, CompetitionStatus::ACTIVE])->get();
        $competitions->each(function ($competition) {
            if ($competition->start_at >= today() && $competition->end_at < today() && $competition->status == CompetitionStatus::PENDING) {
                $competition->update([
                    'status' => CompetitionStatus::ACTIVE,
                ]);
            } elseif ($competition->end_at < today() && $competition->status == CompetitionStatus::ACTIVE) {
                $competition->update([
                    'status' => CompetitionStatus::FINISHED,
                ]);
            }
        });
    }
}
