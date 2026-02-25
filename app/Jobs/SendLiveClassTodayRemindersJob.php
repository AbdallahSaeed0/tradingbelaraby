<?php

namespace App\Jobs;

use App\Models\LiveClass;
use App\Models\LiveClassRegistration;
use App\Models\NotificationSend;
use App\Notifications\LiveClassReminderNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendLiveClassTodayRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $todayStart = Carbon::today();
        $todayEnd = Carbon::today()->endOfDay();
        $classes = LiveClass::where('status', 'scheduled')
            ->whereBetween('scheduled_at', [$todayStart, $todayEnd])
            ->with('registrations.user')
            ->get();

        foreach ($classes as $liveClass) {
            $scheduledFor = $liveClass->scheduled_at->copy()->startOfDay();
            foreach ($liveClass->registrations as $reg) {
                if (!$reg->user) {
                    continue;
                }
                $sent = NotificationSend::recordSend(
                    $reg->user_id,
                    LiveClassReminderNotification::KEY_TODAY,
                    $liveClass->id,
                    $scheduledFor
                );
                if ($sent) {
                    $reg->user->notify(new LiveClassReminderNotification($liveClass, LiveClassReminderNotification::KEY_TODAY));
                }
            }
        }
    }
}
