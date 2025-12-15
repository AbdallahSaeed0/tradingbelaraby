<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseEnrollmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $course;

    /**
     * Create a new notification instance.
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $siteName = \App\Models\MainContentSettings::getActive()?->site_name ?? 'Our Platform';
        
        return (new MailMessage)
                    ->subject('New Course Added to Your Account - ' . $this->course->localized_name)
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line('Great news! You have been successfully enrolled in a new course.')
                    ->line('**Course:** ' . $this->course->localized_name)
                    ->line('You can now start learning and access all course materials.')
                    ->action('View Course', route('courses.show', $this->course))
                    ->line('Thank you for using ' . $siteName . '!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'course_id' => $this->course->id,
            'course_name' => $this->course->localized_name,
        ];
    }
}
