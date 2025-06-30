<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorApplicationRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public $application;
    public $rejectionReason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Application $application, string $rejectionReason = null)
    {
        $this->application = $application;
        $this->rejectionReason = $rejectionReason ?? 'Application did not meet our current partnership requirements.';
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
            ->subject('Application Update - ' . $this->application->application_reference)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Thank you for your interest in becoming a vendor partner with Aktina Technologies.')
            ->line('After careful review of your application, we regret to inform you that we cannot proceed with your vendor application at this time.')
            ->line('Application Reference: ' . $this->application->application_reference)
            ->line('Reason: ' . $this->rejectionReason)
            ->line('We encourage you to reapply in the future as our partnership requirements may change.')
            ->line('Thank you for considering Aktina Technologies as a potential partner.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'application_rejected',
            'application_id' => $this->application->id,
            'application_reference' => $this->application->application_reference,
            'rejection_reason' => $this->rejectionReason,
            'message' => 'Your vendor application has been declined.',
            'action_url' => null,
        ];
    }
}
