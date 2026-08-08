<?php

namespace App\DTOs;

use App\Enums\ReportSortDirection;

final readonly class BuildReportDTO
{
    /**
     * @param  array<int, string>  $columns
     * @param  array<int, ReportFilterDTO>  $filters
     */
    public function __construct(
        public string $tableName,
        public array $columns,
        public array $filters = [],
        public ?string $sortColumn = null,
        public ReportSortDirection $sortDirection = ReportSortDirection::ASC,
        public int $perPage = 25,
        public int $page = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
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
            perPage: (int) ($data['per_page'] ?? 25),
            page: (int) ($data['page'] ?? 1),
        );
    }
}
