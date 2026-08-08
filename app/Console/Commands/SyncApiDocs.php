<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncApiDocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docs:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate the Swagger/OpenAPI docs and publish the JSON to public/docs/api.json';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('l5-swagger:generate');

        $source = storage_path('api-docs/api-docs.json');
        $destination = public_path('docs/api.json');

        if (! File::exists($source)) {
            $this->error("Generated docs not found at {$source}");

            return 1;
        }

        File::ensureDirectoryExists(dirname($destination));
        File::copy($source, $destination);

        $this->info("Published API docs to {$destination}");

        return 0;
    }
}
