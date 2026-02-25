<?php

namespace App\Notifications;

use App\Support\NotificationPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ManualCampaignNotification extends Notification
{
    use Queueable;

    public const KEY = 'MANUAL_CAMPAIGN';

    public function __construct(
        public string $titleEn,
        public string $titleAr,
        public string $bodyEn,
        public string $bodyAr,
        public array $action = ['type' => 'none', 'value' => '', 'meta' => []],
        public ?array $entity = null,
        public string $priority = NotificationPayload::PRIORITY_NORMAL
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return NotificationPayload::build(
            self::KEY,
            $this->titleEn,
            $this->titleAr,
            $this->bodyEn,
            $this->bodyAr,
            $this->action,
            $this->entity,
            $this->priority,
            NotificationPayload::CREATED_BY_ADMIN,
            NotificationPayload::AUDIENCE_SEGMENT,
            []
        );
    }
}
