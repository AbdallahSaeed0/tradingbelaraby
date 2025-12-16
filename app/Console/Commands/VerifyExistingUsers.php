<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VerifyExistingUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:verify-existing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark all existing users (without email_verified_at) as verified by setting email_verified_at to their created_at date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verifying existing users...');

        $unverifiedUsers = User::whereNull('email_verified_at')->get();
        
        if ($unverifiedUsers->isEmpty()) {
            $this->info('No unverified users found.');
            return 0;
        }

        $count = 0;
        foreach ($unverifiedUsers as $user) {
            $user->email_verified_at = $user->created_at;
            $user->save();
            $count++;
        }

        $this->info("Successfully verified {$count} existing user(s).");
        return 0;
    }
}
