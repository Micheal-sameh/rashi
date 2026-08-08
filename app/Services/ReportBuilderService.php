<?php

namespace App\Services;

use App\DTOs\BuildReportDTO;
use App\DTOs\ReportFilterDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ReportBuilderService
{
    public function run(BuildReportDTO $dto): LengthAwarePaginator
    {
        $tables = config('report_builder.tables', []);

        if (! array_key_exists($dto->tableName, $tables)) {
            abort(403, "Table [{$dto->tableName}] is not reportable.");
        }

        $tableConfig = $tables[$dto->tableName];
        $allowedColumns = array_keys($tableConfig['columns']);

        $columns = array_values(array_intersect($dto->columns, $allowedColumns));

        if (empty($columns)) {
            $columns = $allowedColumns;
        }

        $query = DB::table($dto->tableName)->select($columns);

        foreach ($dto->filters as $filter) {
            $this->applyFilter($query, $filter, $tableConfig);
        }

        if ($dto->sortColumn && in_array($dto->sortColumn, $allowedColumns, true)) {
            $query->orderBy($dto->sortColumn, $dto->sortDirection->value);
        }

        $perPage = min($dto->perPage, config('report_builder.max_per_page', 100));

        return $query->paginate($perPage, ['*'], 'page', $dto->page);
    }

    protected function applyFilter(Builder $query, ReportFilterDTO $filter, array $tableConfig): void
    {
        $columnConfig = $tableConfig['columns'][$filter->column] ?? null;

        if (! $columnConfig) {
            // Silently skip filters on non-whitelisted columns.
            return;
        }

        $allowedOperators = config("report_builder.operators.{$columnConfig['type']}", []);

        if (! in_array($filter->operator, $allowedOperators, true)) {
            return;
        }

        $operator = strtoupper($filter->operator);

        if ($operator === 'LIKE' || $operator === 'NOT LIKE') {
            $query->where($filter->column, $operator, '%'.$filter->value.'%');

            return;
        }

        $query->where($filter->column, $filter->operator, $filter->value);
    }
}
