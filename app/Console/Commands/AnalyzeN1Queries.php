<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class AnalyzeN1Queries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analyze:n1
                            {route? : Specific route to analyze}
                            {--all : Analyze all routes}
                            {--threshold=10 : Query count threshold}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze routes for potential N+1 query issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $route = $this->argument('route');
        $all = $this->option('all');
        $threshold = (int) $this->option('threshold');

        if ($route) {
            $this->analyzeRoute($route, $threshold);
        } elseif ($all) {
            $this->analyzeAllRoutes($threshold);
        } else {
            $this->error('Please specify a route or use --all flag');
            $this->info('Example: php artisan analyze:n1 /api/orders');
            $this->info('Example: php artisan analyze:n1 --all');
        }

        return 0;
    }

    /**
     * Analyze a specific route
     */
    protected function analyzeRoute(string $routeUri, int $threshold)
    {
        $this->info("Analyzing route: {$routeUri}");

        try {
            DB::enableQueryLog();
            $startTime = microtime(true);
            $startMemory = memory_get_usage();

            // Make a test request
            $response = $this->call('GET', $routeUri);

            $endTime = microtime(true);
            $endMemory = memory_get_usage();
            $queries = DB::getQueryLog();

            DB::disableQueryLog();

            $queryCount = count($queries);
            $executionTime = round(($endTime - $startTime) * 1000, 2);
            $memoryUsed = round(($endMemory - $startMemory) / 1024 / 1024, 2);

            // Display results
            $this->newLine();
            $this->line('===========================================');
            $this->line("Route: {$routeUri}");
            $this->line('===========================================');

            if ($queryCount > $threshold) {
                $this->error("⚠️  Query Count: {$queryCount} (Exceeds threshold of {$threshold})");
            } else {
                $this->info("✅ Query Count: {$queryCount}");
            }

            $this->info("⏱️  Execution Time: {$executionTime}ms");
            $this->info("💾 Memory Used: {$memoryUsed}MB");

            if ($queryCount > $threshold) {
                $this->newLine();
                $this->warn('Potential N+1 detected! Query details:');
                $this->displayQueryAnalysis($queries);
            }

        } catch (\Exception $e) {
            $this->error("Error analyzing route: {$e->getMessage()}");
        }
    }

    /**
     * Analyze all routes
     */
    protected function analyzeAllRoutes(int $threshold)
    {
        $this->info('Analyzing all routes...');
        $this->newLine();

        $routes = Route::getRoutes();
        $issues = [];

        $progressBar = $this->output->createProgressBar(count($routes));
        $progressBar->start();

        foreach ($routes as $route) {
            $methods = $route->methods();

            // Only test GET routes
            if (! in_array('GET', $methods)) {
                $progressBar->advance();

                continue;
            }

            $uri = $route->uri();

            // Skip routes with parameters
            if (str_contains($uri, '{')) {
                $progressBar->advance();

                continue;
            }

            try {
                DB::enableQueryLog();
                $this->call('GET', '/'.$uri);
                $queries = DB::getQueryLog();
                DB::disableQueryLog();

                $queryCount = count($queries);

                if ($queryCount > $threshold) {
                    $issues[] = [
                        'route' => $uri,
                        'query_count' => $queryCount,
                    ];
                }

            } catch (\Exception $e) {
                // Skip routes that throw errors
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Display results
        if (empty($issues)) {
            $this->info('✅ No N+1 issues detected!');
        } else {
            $this->warn("⚠️  Found {count($issues)} routes with potential N+1 issues:");
            $this->newLine();

            $this->table(
                ['Route', 'Query Count'],
                array_map(fn ($issue) => [$issue['route'], $issue['query_count']], $issues)
            );

            $this->newLine();
            $this->info('Recommendations:');
            $this->line('1. Add eager loading in repositories');
            $this->line('2. Use query scopes (withApiRelations)');
            $this->line('3. Review service layer for redundant loads');
        }
    }

    /**
     * Display query analysis
     */
    protected function displayQueryAnalysis(array $queries)
    {
        // Group queries by type
        $selects = array_filter($queries, fn ($q) => str_starts_with(strtoupper($q['query']), 'SELECT'));
        $inserts = array_filter($queries, fn ($q) => str_starts_with(strtoupper($q['query']), 'INSERT'));
        $updates = array_filter($queries, fn ($q) => str_starts_with(strtoupper($q['query']), 'UPDATE'));
        $deletes = array_filter($queries, fn ($q) => str_starts_with(strtoupper($q['query']), 'DELETE'));

        $this->line('SELECT queries: '.count($selects));
        $this->line('INSERT queries: '.count($inserts));
        $this->line('UPDATE queries: '.count($updates));
        $this->line('DELETE queries: '.count($deletes));

        // Find duplicate query patterns
        $patterns = [];
        foreach ($queries as $query) {
            // Remove parameter values to find patterns
            $pattern = preg_replace('/\d+/', '?', $query['query']);
            $patterns[$pattern] = ($patterns[$pattern] ?? 0) + 1;
        }

        // Find queries executed multiple times
        $duplicates = array_filter($patterns, fn ($count) => $count > 3);

        if (! empty($duplicates)) {
            $this->newLine();
            $this->error('🔍 Repeated query patterns detected (likely N+1):');
            foreach ($duplicates as $pattern => $count) {
                $this->line("  [{$count}x] ".substr($pattern, 0, 100).'...');
            }
        }

        // Show slowest queries
        $this->newLine();
        $sorted = collect($queries)->sortByDesc('time')->take(3);

        $this->warn('⏱️  Top 3 slowest queries:');
        foreach ($sorted as $query) {
            $time = round($query['time'], 2);
            $sql = substr($query['query'], 0, 80);
            $this->line("  [{$time}ms] {$sql}...");
        }
    }
}
