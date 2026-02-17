<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SyncFrontTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:sync-front';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync front-end translations from data file to database and clear cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Syncing front-end translations...');
        
        try {
            // Run the front page translations seeder
            Artisan::call('db:seed', [
                '--class' => 'FrontPageTranslationsSeeder',
                '--force' => true
            ]);
            
            $this->info('✓ Translations seeded successfully');
            
            // Clear the cache
            Cache::flush();
            $this->info('✓ Cache cleared');
            
            $this->newLine();
            $this->info('Front-end translations synced successfully!');
            $this->info('All translation keys (including Recent Posts, Summary, Share, etc.) are now available in Arabic and English.');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to sync translations: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
