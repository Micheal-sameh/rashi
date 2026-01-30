<?php

namespace App\Exports;

use App\DTOs\UsersFilterDTO;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct(UsersFilterDTO $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = User::query()->with(['groups' => function ($q) {
            $q->where('group_id', '!=', 1); // Exclude General group
        }]);

        // Apply filters
        if ($this->filters->name) {
            $query->where('name', 'like', '%'.$this->filters->name.'%');
        }

        if ($this->filters->group_id) {
            $query->whereHas('groups', function ($q) {
                $q->where('group_id', $this->filters->group_id);
            });
        }

        // Apply sorting
        if ($this->filters->sort_by) {
            $direction = $this->filters->direction ?: 'asc';
            $query->orderBy($this->filters->sort_by, $direction);
        } else {
            $query->orderBy('id', 'desc');
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Membership Code',
            'Email',
            'Phone',
            'Score',
            'Points',
            'Groups',
            'Created At',
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->membership_code,
            $user->email,
            $user->phone,
            $user->score,
            $user->points,
            $user->groups->pluck('name')->join(', '),
            $user->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
