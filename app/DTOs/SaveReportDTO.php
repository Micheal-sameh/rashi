<?php

namespace App\DTOs;

use App\Enums\ReportSortDirection;

final readonly class SaveReportDTO
{
    /**
     * @param  array<int, string>  $columns
     * @param  array<int, ReportFilterDTO>  $filters
     */
    public function __construct(
        public string $name,
        public ?string $description,
        public string $tableName,
        public array $columns,
        public array $filters,
        public ?string $sortColumn,
        public ReportSortDirection $sortDirection,
        public int $createdBy,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
            tableName: $data['table_name'],
            columns: $data['columns'] ?? [],
            filters: array_map(
                fn (array $filter) => ReportFilterDTO::fromArray($filter),
                $data['filters'] ?? []
            ),
            sortColumn: $data['sort_column'] ?? null,
            sortDirection: isset($data['sort_direction'])
                ? ReportSortDirection::from($data['sort_direction'])
                : ReportSortDirection::ASC,
            createdBy: (int) $data['created_by'],
        );
    }
}
