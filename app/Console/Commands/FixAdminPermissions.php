<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use App\Models\AdminType;

class FixAdminPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:fix-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix admin permissions by assigning admins to admin types';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing admin permissions...');

        // Get or create admin type
        $adminType = AdminType::where('name', 'admin')->first();

        if (!$adminType) {
            $this->error('Admin type not found. Please run the AdminTypeSeeder first.');
            return 1;
        }

        $this->info("Found admin type: {$adminType->name} (ID: {$adminType->id})");

        // Get instructor type
        $instructorType = AdminType::where('name', 'instructor')->first();

        if ($instructorType) {
            $this->info("Found instructor type: {$instructorType->name} (ID: {$instructorType->id})");
            $this->info("Current instructor permissions: " . json_encode($instructorType->permissions));
        }

        // Get all admins
        $admins = Admin::all();

        if ($admins->isEmpty()) {
            $this->error('No admins found in the database.');
            return 1;
        }

        $this->info("Found {$admins->count()} admin(s)");

        // Update each admin based on their role
        foreach ($admins as $admin) {
            $this->info("Processing admin: {$admin->name} ({$admin->email})");

            // Assign instructor to instructor type
            if (str_contains(strtolower($admin->email), 'instructor')) {
                $this->info("Assigning {$admin->name} to instructor type");
                $admin->update([
                    'admin_type_id' => $instructorType->id,
                    'is_active' => true
                ]);
            } else {
                // Assign others to admin type
                $this->info("Assigning {$admin->name} to admin type");
                $admin->update([
                    'admin_type_id' => $adminType->id,
                    'is_active' => true
                ]);
            }
        }

        $this->info('Admin permissions have been fixed.');
        $this->info('Instructor accounts now have restricted permissions.');
        $this->info('Admin accounts have full permissions.');

        return 0;
    }
}
