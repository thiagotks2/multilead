<?php

namespace App\Console\Commands\Boost;

use Illuminate\Console\Command;
use Laravel\Boost\Mcp\Tools\SearchDocs;
use Laravel\Mcp\Request;

class SearchDocsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boost:search-docs {queries*} {--packages=*} {--token-limit=3000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search documentation using Laravel Boost MCP tool';

    /**
     * Execute the console command.
     */
    public function handle(SearchDocs $tool): int
    {
        $queries = $this->argument('queries');
        $packages = $this->option('packages');
        $tokenLimit = (int) $this->option('token-limit');

        $request = new Request([
            'queries' => $queries,
            'packages' => $packages ?: null,
            'token_limit' => $tokenLimit,
        ]);

        $this->info("Searching documentation for: " . implode(', ', $queries));

        $response = $tool->handle($request);

        $content = $response->content();

        if ($response->isError()) {
            $this->error('Documentation search failed.');
            $this->line((string) $content);

            return self::FAILURE;
        }

        $this->line((string) $content);

        return self::SUCCESS;
    }
}
