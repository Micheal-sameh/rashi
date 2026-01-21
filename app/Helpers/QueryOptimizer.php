<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;

/**
 * QueryOptimizer - Helper class for preventing N+1 queries and optimizing database operations
 */
class QueryOptimizer
{
    /**
     * Common relationship patterns for eager loading
     */
    private const COMMON_RELATIONS = [
        'order' => ['reward.media', 'user.media', 'servant:id,name'],
        'competition' => ['media', 'groups'],
        'quiz' => ['competition:id,name', 'questions.answers'],
        'user' => ['media', 'groups', 'roles:id,name'],
        'reward' => ['media', 'group:id,name'],
    ];

    /**
     * Optimize a query builder by adding common eager loads
     */
    public static function optimizeQuery(Builder $query, string $entityType): Builder
    {
        $relations = self::COMMON_RELATIONS[strtolower($entityType)] ?? [];

        if (! empty($relations)) {
            $query->with($relations);
        }

        return $query;
    }

    /**
     * Check if a collection is causing N+1 issues
     * Useful for debugging in development
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @return array Missing relations
     */
    public static function checkN1Issues($collection, array $requiredRelations): array
    {
        if ($collection->isEmpty()) {
            return [];
        }

        $missing = [];
        $firstItem = $collection->first();

        foreach ($requiredRelations as $relation) {
            if (! $firstItem->relationLoaded($relation)) {
                $missing[] = $relation;
            }
        }

        return $missing;
    }

    /**
     * Eager load missing relationships on a collection
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @return \Illuminate\Support\Collection
     */
    public static function ensureLoaded($collection, array $relations)
    {
        if ($collection->isEmpty()) {
            return $collection;
        }

        $missing = self::checkN1Issues($collection, $relations);

        if (! empty($missing)) {
            $collection->load($missing);
        }

        return $collection;
    }

    /**
     * Get optimized select columns for a model
     * Helps reduce data transfer by selecting only needed columns
     */
    public static function getMinimalColumns(string $modelClass): array
    {
        // Common minimal columns for different models
        $minimalColumns = [
            'User' => ['id', 'name', 'email', 'membership_code', 'points', 'score'],
            'Order' => ['id', 'user_id', 'reward_id', 'quantity', 'points', 'status', 'created_at'],
            'Competition' => ['id', 'name', 'start_at', 'end_at', 'status'],
            'Quiz' => ['id', 'name', 'date', 'competition_id'],
            'Reward' => ['id', 'name', 'points', 'quantity', 'status', 'group_id'],
        ];

        $className = class_basename($modelClass);

        return $minimalColumns[$className] ?? ['*'];
    }

    /**
     * Optimize a query for dropdown/select options
     * Only loads essential columns
     */
    public static function forDropdown(Builder $query, string $labelColumn = 'name', string $valueColumn = 'id'): Builder
    {
        return $query->select([$valueColumn, $labelColumn])->orderBy($labelColumn);
    }

    /**
     * Add pagination with optimal settings
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function paginateOptimized(Builder $query, ?int $perPage = null)
    {
        $perPage = $perPage ?? config('app.pagination.default', 15);

        // Ensure max per page limit
        $maxPerPage = config('app.pagination.max', 100);
        $perPage = min($perPage, $maxPerPage);

        return $query->paginate($perPage);
    }

    /**
     * Chunk query for memory-efficient processing
     * Useful for large datasets
     */
    public static function processInChunks(Builder $query, callable $callback, int $chunkSize = 100): bool
    {
        return $query->chunk($chunkSize, $callback);
    }

    /**
     * Get query statistics for debugging
     */
    public static function analyzeQueries(callable $operation): array
    {
        \DB::enableQueryLog();

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $result = $operation();

        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        $queries = \DB::getQueryLog();

        \DB::disableQueryLog();

        return [
            'query_count' => count($queries),
            'execution_time_ms' => round(($endTime - $startTime) * 1000, 2),
            'memory_used_mb' => round(($endMemory - $startMemory) / 1024 / 1024, 2),
            'queries' => $queries,
            'result' => $result,
        ];
    }

    /**
     * Add common filters to a query
     */
    public static function applyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            // Handle different filter types
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } elseif (str_contains($field, '_like')) {
                $actualField = str_replace('_like', '', $field);
                $query->where($actualField, 'like', "%{$value}%");
            } elseif (str_contains($field, '_from')) {
                $actualField = str_replace('_from', '', $field);
                $query->where($actualField, '>=', $value);
            } elseif (str_contains($field, '_to')) {
                $actualField = str_replace('_to', '', $field);
                $query->where($actualField, '<=', $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query;
    }

    /**
     * Suggest optimal indexes based on query patterns
     */
    public static function suggestIndexes(string $modelClass, array $frequentFilters): array
    {
        $model = new $modelClass;
        $table = $model->getTable();

        $suggestions = [];

        // Single column indexes
        foreach ($frequentFilters as $column) {
            $suggestions[] = "CREATE INDEX idx_{$table}_{$column} ON {$table}({$column});";
        }

        // Composite indexes for common combinations
        if (count($frequentFilters) >= 2) {
            $columns = implode(',', $frequentFilters);
            $indexName = 'idx_'.$table.'_'.implode('_', $frequentFilters);
            $suggestions[] = "CREATE INDEX {$indexName} ON {$table}({$columns});";
        }

        return $suggestions;
    }
}
