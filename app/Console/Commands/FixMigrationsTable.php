<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixMigrationsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:fix 
                            {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix migrations table by checking existing tables and marking corresponding migrations as run';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will mark migrations as run based on existing tables. Continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Analyzing database and migration files...');
        $this->newLine();

        $migrationsPath = database_path('migrations');
        $files = glob($migrationsPath . '/*.php');
        
        $marked = 0;
        $alreadyRun = 0;
        $notFound = 0;
        $batch = $this->getNextBatchNumber();

        foreach ($files as $file) {
            $migrationName = basename($file, '.php');
            
            // Skip if already marked as run
            if (DB::table('migrations')->where('migration', $migrationName)->exists()) {
                $alreadyRun++;
                continue;
            }

            // Check if the migration's target exists in database
            if ($this->migrationTargetExists($file)) {
                DB::table('migrations')->insert([
                    'migration' => $migrationName,
                    'batch' => $batch,
                ]);
                $this->info("✓ Marked: {$migrationName}");
                $marked++;
            } else {
                $this->warn("  Not found in DB: {$migrationName}");
                $notFound++;
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->info("  ✓ Marked as run: {$marked}");
        $this->info("  - Already run: {$alreadyRun}");
        $this->info("  ⚠ Not found in DB: {$notFound}");

        if ($marked > 0) {
            $this->newLine();
            $this->info("✓ Migrations table has been updated. You can now run 'php artisan migrate' safely.");
        }

        return 0;
    }

    /**
     * Check if the migration's target (table/columns) exists in database
     */
    protected function migrationTargetExists($file)
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
            
            // For alter migrations, if table exists, assume migration was run
            // (More complex checks can be added for specific columns)
            return true;
        }

        // For raw SQL migrations, we can't easily check, so return true
        // User can manually verify these
        if (strpos($content, 'DB::statement') !== false || strpos($content, 'DB::unprepared') !== false) {
            return true;
        }

        return false;
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

