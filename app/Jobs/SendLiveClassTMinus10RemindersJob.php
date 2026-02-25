<?php

namespace App\Jobs;

use App\Models\LiveClass;
use App\Models\NotificationSend;
use App\Notifications\LiveClassReminderNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendLiveClassTMinus10RemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $windowStart = Carbon::now()->addMinutes(10)->startOfMinute();
        $windowEnd = $windowStart->copy()->addMinute();
        $classes = LiveClass::where('status', 'scheduled')
            ->whereBetween('scheduled_at', [$windowStart, $windowEnd])
            ->with('registrations.user')
            ->get();

        foreach ($classes as $liveClass) {
            foreach ($liveClass->registrations as $reg) {
                if (!$reg->user) {
                    continue;
                }
                $sent = NotificationSend::recordSend(
                    $reg->user_id,
                    LiveClassReminderNotification::KEY_T_MINUS_10,
                    $liveClass->id,
                    $liveClass->scheduled_at
                );
                if ($sent) {
                    $reg->user->notify(new LiveClassReminderNotification($liveClass, LiveClassReminderNotification::KEY_T_MINUS_10));
                }
            }
        }
    }
}
