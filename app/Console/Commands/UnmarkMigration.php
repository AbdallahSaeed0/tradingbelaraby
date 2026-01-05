<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UnmarkMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:unmark {migration : The migration file name to unmark}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove a migration from the migrations table so it can be run again';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $migrationName = $this->argument('migration');
        
        // Remove .php extension if provided
        $migrationName = str_replace('.php', '', $migrationName);
        
        // Check if migration exists in table
        if (!DB::table('migrations')->where('migration', $migrationName)->exists()) {
            $this->warn("Migration '{$migrationName}' is not marked as run.");
            return 0;
        }

        // Remove from migrations table
        DB::table('migrations')->where('migration', $migrationName)->delete();

        $this->info("✓ Unmarked '{$migrationName}'. You can now run 'php artisan migrate' to execute it.");
        return 0;
    }
}




