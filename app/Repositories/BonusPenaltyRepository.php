<?php

namespace App\Repositories;

use App\Enums\BonusPenaltyStatus;
use App\Models\BonusPenalty;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BonusPenaltyRepository extends BaseRepository
{
    public function __construct(BonusPenalty $model)
    {
        $this->model = $model;
    }

    protected function model(): string
    {
        return BonusPenalty::class;
    }

    public bool $pagination = true;

    public int $perPage = 15;

    protected function execute(Builder $query): Collection|LengthAwarePaginator
    {
        return $this->pagination ? $query->paginate($this->perPage) : $query->get();
    }

    /**
     * Get bonus/penalties list (optimized with eager loading)
     */
    public function index($user_id = null)
    {
        $query = $this->model
            ->select(['id', 'user_id', 'type', 'points', 'reason', 'status', 'created_by', 'approved_by', 'created_at', 'updated_at'])
            ->with([
                'user:id,name,membership_code',
                // 'user.media',
                'creator:id,name',
            ])
            ->when(isset($user_id), fn ($q) => $q->where('user_id', $user_id))
            ->latest('created_at');

        return $this->execute($query);
    }

    /**
     * Get applied bonus/penalties with filters (for index page)
     */
    public function getApplied(array $filters = [])
    {
        $query = $this->model
            ->select(['id', 'user_id', 'type', 'points', 'reason', 'status', 'created_by', 'approved_by', 'created_at', 'updated_at'])
            ->with([
                'user:id,name,membership_code',
                'user.media' => function ($query) {
                    $query->select('id', 'model_id', 'model_type', 'file_name', 'collection_name');
                },
                'creator:id,name',
                'approver:id,name',
            ])
            ->where('status', BonusPenaltyStatus::APPLIED);

        // Apply filters
        $this->applyFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }

    /**
     * Get pending bonus/penalties (for approval page)
     */
    public function getPending(array $filters = [])
    {
        $query = $this->model
            ->select(['id', 'user_id', 'type', 'points', 'reason', 'status', 'created_by', 'created_at', 'updated_at'])
            ->with([
                'user:id,name,membership_code',
                'user.media' => function ($query) {
                    $query->select('id', 'model_id', 'model_type', 'file_name', 'collection_name');
                },
                'creator:id,name',
            ])
            ->where('status', BonusPenaltyStatus::PENDING_APPROVAL);

        // Apply filters
        $this->applyFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }

    /**
     * Apply common filters to query
     */
    protected function applyFilters(Builder $query, array $filters): void
    {
        // Search by user name or membership_code
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('membership_code', 'like', "%{$search}%");
            });
        }

        // Filter by creator
        if (! empty($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        // Filter by user_id
        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Filter by type
        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Filter by date range
        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
    }

    /**
     * Find by ID with all relations
     */
    public function findByIdWithRelations($id)
    {
        return $this->model
            ->with(['user', 'creator', 'approver'])
            ->findOrFail($id);
    }

    /**
     * Store new bonus/penalty
     */
    public function store($data)
    {
        return $this->model->create($data);
    }

    /**
     * Bulk create bonus/penalties (optimized for multiple records)
     */
    public function bulkCreate(array $records)
    {
        return $this->model->insert($records);
    }
}
