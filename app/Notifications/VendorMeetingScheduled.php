<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorMeetingScheduled extends Notification implements ShouldQueue
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
            ->subject('Meeting Scheduled - ' . $this->application->application_reference)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Great news! We would like to schedule a meeting to discuss your vendor application.')
            ->line('Application Reference: ' . $this->application->application_reference)
            ->line('Meeting Date: ' . $this->application->meeting_schedule->format('F j, Y \a\t g:i A'))
            ->line('Please prepare any additional documents or questions you may have about our partnership program.')
            ->action('View Application Details', url('/verification/vendor'))
            ->line('We look forward to speaking with you!')
            ->line('If you need to reschedule, please contact us as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'meeting_scheduled',
            'application_id' => $this->application->id,
            'application_reference' => $this->application->application_reference,
            'meeting_schedule' => $this->application->meeting_schedule->toISOString(),
            'message' => 'A meeting has been scheduled for ' . $this->application->meeting_schedule->format('F j, Y \a\t g:i A'),
            'action_url' => url('/verification/vendor'),
        ];
    }
}
