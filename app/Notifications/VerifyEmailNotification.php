<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    public $language;

    /**
     * Create a new notification instance.
     */
    public function __construct(?string $language = null)
    {
        $this->language = $language ?? 'en';
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $siteName = \App\Models\MainContentSettings::getActive()?->site_name ?? 'Our Platform';
        $lang = $this->getUserLanguage();

        $texts = $this->getTexts($lang);

        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name', $siteName);

        return (new MailMessage)
            ->from($fromAddress, $fromName)
            ->replyTo($fromAddress, $fromName)
            ->subject($texts['subject'] . ' - ' . $siteName)
            ->greeting($texts['greeting'] . ' ' . $notifiable->name . '!')
            ->line($texts['line1'])
            ->action($texts['action'], $verificationUrl)
            ->line($texts['line2'])
            ->line($texts['line3'])
            ->line($texts['thank_you'] . ' ' . $siteName . '!')
            ->salutation($texts['regards'] . "\n" . config('app.name'));
    }

    /**
     * Get user language preference
     */
    protected function getUserLanguage(): string
    {
        return in_array($this->language, ['ar', 'en']) ? $this->language : 'en';
    }

    /**
     * Get localized texts
     */
    protected function getTexts(string $lang): array
    {
        $texts = [
            'en' => [
                'subject' => 'Verify Your Email Address',
                'greeting' => 'Hello',
                'line1' => 'Please click the button below to verify your email address.',
                'action' => 'Verify Email Address',
                'line2' => 'If you did not create an account, no further action is required.',
                'line3' => 'This verification link will expire in 60 minutes.',
                'thank_you' => 'Thank you for using',
                'regards' => 'Regards,',
            ],
            'ar' => [
                'subject' => 'تحقق من عنوان بريدك الإلكتروني',
                'greeting' => 'مرحباً',
                'line1' => 'يرجى النقر على الزر أدناه للتحقق من عنوان بريدك الإلكتروني.',
                'action' => 'تحقق من البريد الإلكتروني',
                'line2' => 'إذا لم تقم بإنشاء حساب، فلا حاجة لاتخاذ أي إجراء إضافي.',
                'line3' => 'ستنتهي صلاحية رابط التحقق هذا خلال 60 دقيقة.',
                'thank_you' => 'شكراً لاستخدامك',
                'regards' => 'مع التحية،',
            ],
        ];

        return $texts[$lang] ?? $texts['en'];
    }
}

