<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MarkMigrationsAsRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:mark-run 
                            {migration? : The specific migration file name to mark as run}
                            {--all : Mark all pending migrations as run}
                            {--sync : Sync migrations table with existing database tables}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark migrations as run without executing them. Useful when tables already exist in production.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('sync')) {
            return $this->syncMigrationsWithDatabase();
        }

        if ($this->option('all')) {
            return $this->markAllPendingAsRun();
        }

        if ($migration = $this->argument('migration')) {
            return $this->markSpecificMigrationAsRun($migration);
        }

        $this->error('Please specify a migration name, use --all to mark all pending, or --sync to sync with database.');
        return 1;
    }

    /**
     * Mark all pending migrations as run
     */
    protected function markAllPendingAsRun()
    {
        $migrationsPath = database_path('migrations');
        $files = glob($migrationsPath . '/*.php');
        
        $marked = 0;
        $batch = $this->getNextBatchNumber();

        foreach ($files as $file) {
            $migrationName = basename($file, '.php');
            
            // Check if already run
            if (DB::table('migrations')->where('migration', $migrationName)->exists()) {
                continue;
            }

            // Check if table exists (for create table migrations)
            if ($this->shouldMarkAsRun($migrationName, $file)) {
                DB::table('migrations')->insert([
                    'migration' => $migrationName,
                    'batch' => $batch,
                ]);
                $this->info("Marked as run: {$migrationName}");
                $marked++;
            } else {
                $this->warn("Skipped (table doesn't exist): {$migrationName}");
            }
        }

        if ($marked > 0) {
            $this->info("\n✓ Marked {$marked} migration(s) as run in batch {$batch}");
        } else {
            $this->info("\n✓ No pending migrations to mark.");
        }

        return 0;
    }

    /**
     * Mark a specific migration as run
     */
    protected function markSpecificMigrationAsRun($migrationName)
    {
        // Remove .php extension if provided
        $migrationName = str_replace('.php', '', $migrationName);
        
        // Check if already run
        if (DB::table('migrations')->where('migration', $migrationName)->exists()) {
            $this->warn("Migration '{$migrationName}' is already marked as run.");
            return 0;
        }

        $batch = $this->getNextBatchNumber();
        
        DB::table('migrations')->insert([
            'migration' => $migrationName,
            'batch' => $batch,
        ]);

        $this->info("✓ Marked '{$migrationName}' as run in batch {$batch}");
        return 0;
    }

    /**
     * Sync migrations table with existing database tables
     */
    protected function syncMigrationsWithDatabase()
    {
        $this->info('Syncing migrations table with existing database tables...');
        $this->newLine();

        $migrationsPath = database_path('migrations');
        $files = glob($migrationsPath . '/*.php');
        
        $marked = 0;
        $skipped = 0;
        $batch = $this->getNextBatchNumber();

        foreach ($files as $file) {
            $migrationName = basename($file, '.php');
            
            // Skip if already in migrations table
            if (DB::table('migrations')->where('migration', $migrationName)->exists()) {
                $skipped++;
                continue;
            }

            // Check if the migration's table/columns exist
            if ($this->shouldMarkAsRun($migrationName, $file)) {
                DB::table('migrations')->insert([
                    'migration' => $migrationName,
                    'batch' => $batch,
                ]);
                $this->info("✓ Marked: {$migrationName}");
                $marked++;
            } else {
                $this->line("  Skipped: {$migrationName} (not found in database)");
            }
        }

        $this->newLine();
        $this->info("✓ Marked {$marked} migration(s) as run");
        $this->info("  Skipped {$skipped} migration(s) (already marked)");
        
        return 0;
    }

    /**
     * Determine if a migration should be marked as run based on database state
     */
    protected function shouldMarkAsRun($migrationName, $file)
    {
        $content = file_get_contents($file);
        
        // Check for table creation
        if (preg_match("/Schema::create\(['\"]([^'\"]+)['\"]/", $content, $matches)) {
            $tableName = $matches[1];
            return Schema::hasTable($tableName);
        }
        
        // Check for table modification
        if (preg_match("/Schema::table\(['\"]([^'\"]+)['\"]/", $content, $matches)) {
            $tableName = $matches[1];
            if (!Schema::hasTable($tableName)) {
                return false;
            }
            
            // Check for column additions
            if (preg_match_all("/\$table->(string|integer|text|decimal|boolean|enum|foreignId|timestamp|date|time)\(['\"]([^'\"]+)['\"]/", $content, $columnMatches)) {
                foreach ($columnMatches[2] as $columnName) {
                    if (!Schema::hasColumn($tableName, $columnName)) {
                        return false;
                    }
                }
            }
            
            return true;
        }

        // For other migrations (like DB::statement), assume they should be run
        // You can manually mark these if needed
        return true;
    }

    /**
     * Get the next batch number
     */
    protected function getNextBatchNumber()
    {
        $lastBatch = DB::table('migrations')->max('batch');
        return $lastBatch ? $lastBatch + 1 : 1;
    }
}

