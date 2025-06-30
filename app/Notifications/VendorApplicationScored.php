<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorApplicationScored extends Notification implements ShouldQueue
{
    use Queueable;

    public $application;

    /**
     * Create a new notification instance.
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Application Scored - ' . $this->application->application_reference)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your vendor application has been processed and scored by our automated system.')
            ->line('Application Reference: ' . $this->application->application_reference)
            ->line('Score: ' . $this->application->score . '/100')
            ->line('Processing Date: ' . $this->application->processing_date->format('F j, Y'))
            ->line('Your application is now under review by our team. You may be contacted to schedule a meeting to discuss your application further.')
            ->action('View Application Status', url('/verification/vendor'))
            ->line('Thank you for your patience during the review process.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'application_scored',
            'application_id' => $this->application->id,
            'application_reference' => $this->application->application_reference,
            'score' => $this->application->score,
            'message' => 'Your vendor application has been scored: ' . $this->application->score . '/100',
            'action_url' => url('/verification/vendor'),
        ];
    }
}
