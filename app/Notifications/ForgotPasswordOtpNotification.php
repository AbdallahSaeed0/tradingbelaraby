<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ForgotPasswordOtpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $otp,
        public string $language = 'en'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $siteName = \App\Models\MainContentSettings::getActive()?->site_name ?? 'Our Platform';
        $texts = $this->getTexts($this->language);

        return (new MailMessage)
            ->subject($texts['subject'] . ' - ' . $siteName)
            ->greeting($texts['greeting'] . ' ' . $notifiable->name . '!')
            ->line($texts['line1'])
            ->line('**' . $this->otp . '**')
            ->line($texts['line2'])
            ->line($texts['line3']);
    }

    protected function getTexts(string $lang): array
    {
        $texts = [
            'en' => [
                'subject' => 'Reset Your Password',
                'greeting' => 'Hello',
                'line1' => 'Your password reset OTP code is:',
                'line2' => 'This code will expire in 10 minutes.',
                'line3' => 'If you did not request a password reset, please ignore this email.',
            ],
            'ar' => [
                'subject' => 'إعادة تعيين كلمة المرور',
                'greeting' => 'مرحباً',
                'line1' => 'رمز التحقق لإعادة تعيين كلمة المرور هو:',
                'line2' => 'سينتهي صلاحية هذا الرمز خلال 10 دقائق.',
                'line3' => 'إذا لم تطلب إعادة تعيين كلمة المرور، يرجى تجاهل هذا البريد الإلكتروني.',
            ],
        ];

        return $texts[$lang] ?? $texts['en'];
    }
}
