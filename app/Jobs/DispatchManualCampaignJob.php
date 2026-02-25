<?php

namespace App\Jobs;

use App\Models\ManualNotificationCampaign;
use App\Models\User;
use App\Notifications\ManualCampaignNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class DispatchManualCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ManualNotificationCampaign $campaign)
    {}

    public function handle(): void
    {
        $action = $this->campaign->action_json ?? ['type' => 'none', 'value' => '', 'meta' => []];
        $entity = $this->campaign->entity_json ?? null;

        $notification = new ManualCampaignNotification(
            $this->campaign->title_en,
            $this->campaign->title_ar,
            $this->campaign->body_en,
            $this->campaign->body_ar,
            $action,
            $entity,
            $this->campaign->priority ?? 'normal'
        );

        $query = $this->resolveAudienceQuery();
        $query->chunk(100, function ($users) use ($notification) {
            foreach ($users as $user) {
                $user->notify($notification);
            }
        });

        $this->campaign->update([
            'sent_at' => now(),
            'status' => 'sent',
        ]);
    }

    private function resolveAudienceQuery()
    {
        $filter = $this->campaign->audience_filter ?? [];
        return match ($this->campaign->audience_type) {
            'single' => User::query()->where(function ($q) use ($filter) {
                if (!empty($filter['user_id'])) {
                    $q->orWhere('id', $filter['user_id']);
                }
                if (!empty($filter['email'])) {
                    $q->orWhere('email', $filter['email']);
                }
                if (empty($filter['user_id']) && empty($filter['email'])) {
                    $q->whereRaw('1 = 0');
                }
            }),
            'segment' => $this->resolveSegmentQuery($filter),
            'broadcast' => User::query(),
            default => User::query()->whereRaw('1 = 0'),
        };
    }

    private function resolveSegmentQuery(array $filter)
    {
        $query = User::query();
        if (!empty($filter['enrolled_in_course_id'])) {
            $query->whereHas('enrollments', function ($q) use ($filter) {
                $q->where('course_id', $filter['enrolled_in_course_id']);
            });
        }
        if (!empty($filter['language'])) {
            $query->where('default_language', $filter['language']);
        }
        return $query;
    }
}
