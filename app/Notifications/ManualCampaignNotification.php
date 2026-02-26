<?php

namespace App\Notifications;

use App\Support\NotificationPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ManualCampaignNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public const KEY = 'MANUAL_CAMPAIGN';

    /** @var string notification|email|both */
    public string $deliveryChannel = 'notification';

    public function __construct(
        public string $titleEn,
        public string $titleAr,
        public string $bodyEn,
        public string $bodyAr,
        public array $action = ['type' => 'none', 'value' => '', 'meta' => []],
        public ?array $entity = null,
        public string $priority = NotificationPayload::PRIORITY_NORMAL,
        string $deliveryChannel = 'notification'
    ) {
        $this->deliveryChannel = $deliveryChannel;
    }

    public function via(object $notifiable): array
    {
        $channels = [];
        if (in_array($this->deliveryChannel, ['notification', 'both'], true)) {
            $channels[] = 'database';
        }
        if (in_array($this->deliveryChannel, ['email', 'both'], true)) {
            $channels[] = 'mail';
        }
        return $channels ?: ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = $notifiable->preferred_locale ?? config('app.locale', 'en');
        $locale = str_starts_with((string) $locale, 'ar') ? 'ar' : 'en';
        \Illuminate\Support\Facades\App::setLocale($locale);

        $greeting = $locale === 'ar' ? 'مرحباً ' : 'Hello ';
        $actionText = $locale === 'ar' ? 'عرض' : 'View';
        $title = $locale === 'ar' ? $this->titleAr : $this->titleEn;
        $body = $locale === 'ar' ? $this->bodyAr : $this->bodyEn;

        $link = ($this->action['value'] ?? null) ?: url('/');
        return (new MailMessage)
            ->subject($title)
            ->greeting($greeting . $notifiable->name . ',')
            ->line($body)
            ->action($actionText, $link);
    }

    public function toArray(object $notifiable): array
    {
        $action = $this->action;
        if (($action['type'] ?? '') === 'none') {
            $action = ['type' => 'none', 'value' => '', 'meta' => []];
        } else {
            $action['type'] = 'url';
        }
        return NotificationPayload::build(
            self::KEY,
            $this->titleEn,
            $this->titleAr,
            $this->bodyEn,
            $this->bodyAr,
            $action,
            $this->entity,
            $this->priority,
            NotificationPayload::CREATED_BY_ADMIN,
            NotificationPayload::AUDIENCE_SEGMENT,
            []
        );
    }
}
