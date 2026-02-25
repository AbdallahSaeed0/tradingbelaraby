<?php

namespace App\Jobs;

use App\Models\ManualNotificationCampaign;
use App\Models\User;
use App\Notifications\ManualCampaignNotification;
use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DispatchManualCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ManualNotificationCampaign $campaign)
    {}

    public function handle(): void
    {
        $action = $this->campaign->action_json ?? ['type' => 'none', 'value' => '', 'meta' => []];
        $entity = $this->campaign->entity_json ?? null;
        $deliveryChannel = $this->campaign->delivery_channel ?? 'notification';

        $notification = new ManualCampaignNotification(
            $this->campaign->title_en,
            $this->campaign->title_ar,
            $this->campaign->body_en,
            $this->campaign->body_ar,
            $action,
            $entity,
            $this->campaign->priority ?? 'normal',
            $deliveryChannel
        );

        $sendPush = in_array($deliveryChannel, ['notification', 'both'], true);

        $query = $this->resolveAudienceQuery();
        $query->chunk(100, function ($users) use ($notification, $sendPush) {
            foreach ($users as $user) {
                $user->notify($notification);
                if ($sendPush) {
                    FcmService::sendToUser($user, $this->campaign->title_en, $this->campaign->body_en, [
                        'url' => $this->campaign->action_json['value'] ?? '',
                    ]);
                }
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
            'single' => User::query()->where('email', $filter['email'] ?? ''),
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
        return $query;
    }
}
