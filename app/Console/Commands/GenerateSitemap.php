<?php

namespace App\Console\Commands;

use App\Http\Controllers\SitemapController;
use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sitemap.xml and save to public/sitemap.xml (for cron/schedule)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $sitemap = SitemapController::buildSitemap();
            $path = public_path('sitemap.xml');
            $sitemap->writeToFile($path);
            $this->info('Sitemap written to ' . $path);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Error: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
