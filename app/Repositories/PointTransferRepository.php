<?php

namespace App\Repositories;

use App\Models\PointTransfer;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PointTransferRepository extends BaseRepository
{
    public function __construct(PointTransfer $model)
    {
        $this->model = $model;
    }

    protected function model(): string
    {
        return PointTransfer::class;
    }

    public bool $pagination = true;

    public int $perPage = 15;

    protected function execute(Builder $query): Collection|LengthAwarePaginator
    {
        return $this->pagination ? $query->paginate($this->perPage) : $query->get();
    }

    /**
     * Get all point transfers with filters
     */
    public function index(array $filters = [])
    {
        $query = $this->model
            ->select([
                'id',
                'sender_id',
                'receiver_id',
                'points',
                'family_code',
                'reason',
                'created_by',
                'created_at',
                'updated_at',
            ])
            ->with([
                'sender:id,name,membership_code',
                'receiver:id,name,membership_code',
                'creator:id,name',
            ]);

        $this->applyFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }

    /**
     * Get transfers for a specific user (sent or received)
     */
    public function getUserTransfers(int $userId, array $filters = [])
    {
        $query = $this->model
            ->select([
                'id',
                'sender_id',
                'receiver_id',
                'points',
                'family_code',
                'reason',
                'created_by',
                'created_at',
                'updated_at',
            ])
            ->with([
                'sender:id,name,membership_code',
                'receiver:id,name,membership_code',
                'creator:id,name',
            ])
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            });

        $this->applyFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }

    /**
     * Get transfers for a specific family
     */
    public function getFamilyTransfers(string $familyCode, array $filters = [])
    {
        $query = $this->model
            ->select([
                'id',
                'sender_id',
                'receiver_id',
                'points',
                'family_code',
                'reason',
                'created_by',
                'created_at',
                'updated_at',
            ])
            ->with([
                'sender:id,name,membership_code',
                'receiver:id,name,membership_code',
                'creator:id,name',
            ])
            ->where('family_code', $familyCode);

        $this->applyFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters(Builder $query, array $filters): void
    {
        // Search by user name or membership_code
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('sender', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('membership_code', 'like', "%{$search}%");
                })
                    ->orWhereHas('receiver', function ($rq) use ($search) {
                        $rq->where('name', 'like', "%{$search}%")
                            ->orWhere('membership_code', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by family code
        if (! empty($filters['family_code'])) {
            $query->where('family_code', $filters['family_code']);
        }

        // Filter by date range
        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Filter by sender
        if (! empty($filters['sender_id'])) {
            $query->where('sender_id', $filters['sender_id']);
        }

        // Filter by receiver
        if (! empty($filters['receiver_id'])) {
            $query->where('receiver_id', $filters['receiver_id']);
        }
    }

    /**
     * Create a point transfer
     */
    public function store(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Get family members by family code
     */
    public function getFamilyMembers(string $familyCode)
    {
        return User::select(['id', 'name', 'membership_code', 'points'])
            ->where('membership_code', 'like', "%{$familyCode}%")
            ->orderBy('name')
            ->get();
    }

    /**
     * Find transfer by ID with relations
     */
    public function findByIdWithRelations($id)
    {
        return $this->model
            ->with(['sender', 'receiver', 'creator'])
            ->findOrFail($id);
    }
}
