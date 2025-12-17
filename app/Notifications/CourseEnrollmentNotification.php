<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseEnrollmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $course;
    public $order;
    public $language;

    /**
     * Create a new notification instance.
     */
    public function __construct(Course $course, ?Order $order = null, ?string $language = null)
    {
        $this->course = $course;
        $this->order = $order;
        $this->language = $language ?? 'en';
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
     * Get user language preference
     */
    protected function getUserLanguage(): string
    {
        return in_array($this->language, ['ar', 'en']) ? $this->language : 'en';
    }

    /**
     * Get localized text
     */
    protected function getText(string $key): string
    {
        $lang = $this->getUserLanguage();
        
        $texts = [
            'en' => [
                'subject' => 'New Course Added to Your Account',
                'greeting' => 'Hello',
                'enrolled_success' => 'Great news! You have been successfully enrolled in a new course.',
                'course_label' => 'Course',
                'start_learning' => 'You can now start learning and access all course materials.',
                'view_course' => 'View Course',
                'thank_you' => 'Thank you for using',
                'invoice_details' => 'Invoice Details',
                'order_number' => 'Order Number',
                'order_date' => 'Order Date',
                'payment_method' => 'Payment Method',
                'course_price' => 'Course Price',
                'discount' => 'Discount',
                'total' => 'Total',
                'free' => 'Free',
                'visa' => 'Credit Card',
                'tabby' => 'Tabby',
            ],
            'ar' => [
                'subject' => 'تمت إضافة دورة جديدة إلى حسابك',
                'greeting' => 'مرحباً',
                'enrolled_success' => 'أخبار رائعة! تم تسجيلك بنجاح في دورة جديدة.',
                'course_label' => 'الدورة',
                'start_learning' => 'يمكنك الآن البدء في التعلم والوصول إلى جميع مواد الدورة.',
                'view_course' => 'عرض الدورة',
                'thank_you' => 'شكراً لاستخدامك',
                'invoice_details' => 'تفاصيل الفاتورة',
                'order_number' => 'رقم الطلب',
                'order_date' => 'تاريخ الطلب',
                'payment_method' => 'طريقة الدفع',
                'course_price' => 'سعر الدورة',
                'discount' => 'الخصم',
                'total' => 'الإجمالي',
                'free' => 'مجاني',
                'visa' => 'بطاقة ائتمان',
                'tabby' => 'تابي',
            ],
        ];

        return $texts[$lang][$key] ?? $texts['en'][$key];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $siteName = \App\Models\MainContentSettings::getActive()?->site_name ?? 'Our Platform';
        $lang = $this->getUserLanguage();
        $isRTL = $lang === 'ar';
        
        // Get localized course name
        $courseName = $lang === 'ar' && $this->course->name_ar 
            ? $this->course->name_ar 
            : $this->course->name;

        $mailMessage = (new MailMessage)
            ->subject($this->getText('subject') . ' - ' . $courseName)
            ->greeting($this->getText('greeting') . ' ' . $notifiable->name . '!')
            ->line($this->getText('enrolled_success'))
            ->line('**' . $this->getText('course_label') . ':** ' . $courseName);

        // Add invoice details if order exists
        if ($this->order) {
            $orderItem = $this->order->orderItems()
                ->where('course_id', $this->course->id)
                ->first();
            
            if ($orderItem) {
                $mailMessage->line('---')
                    ->line('**' . $this->getText('invoice_details') . '**')
                    ->line($this->getText('order_number') . ': ' . $this->order->order_number)
                    ->line($this->getText('order_date') . ': ' . $this->order->created_at->format('Y-m-d H:i'))
                    ->line($this->getText('payment_method') . ': ' . $this->getPaymentMethodText($this->order->payment_method))
                    ->line($this->getText('course_price') . ': $' . number_format($orderItem->price, 2));
                
                if ($this->order->discount_amount > 0) {
                    $mailMessage->line($this->getText('discount') . ': -$' . number_format($this->order->discount_amount, 2));
                }
                
                $mailMessage->line('**' . $this->getText('total') . ': $' . number_format($this->order->total, 2) . '**')
                    ->line('---');
            }
        } else {
            // For free courses without order
            $mailMessage->line($this->getText('course_price') . ': ' . $this->getText('free'));
        }

        $mailMessage->line($this->getText('start_learning'))
            ->action($this->getText('view_course'), route('courses.show', $this->course))
            ->line($this->getText('thank_you') . ' ' . $siteName . '!');

        return $mailMessage;
    }

    /**
     * Get payment method text
     */
    protected function getPaymentMethodText(string $method): string
    {
        $lang = $this->getUserLanguage();
        
        $methods = [
            'en' => [
                'free' => 'Free',
                'visa' => 'Credit Card',
                'tabby' => 'Tabby',
            ],
            'ar' => [
                'free' => 'مجاني',
                'visa' => 'بطاقة ائتمان',
                'tabby' => 'تابي',
            ],
        ];

        return $methods[$lang][$method] ?? $methods['en'][$method] ?? $method;
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
            'order_id' => $this->order?->id,
        ];
    }
}
