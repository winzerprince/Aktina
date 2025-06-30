<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewApplicationSubmitted extends Notification implements ShouldQueue
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
        $vendorName = $this->application->vendor?->user?->name ?? 'Unknown Vendor';

        return (new MailMessage)
            ->subject('New Vendor Application - ' . $this->application->application_reference)
            ->greeting('Hello Admin,')
            ->line('A new vendor application has been submitted and requires your attention.')
            ->line('Application Reference: ' . $this->application->application_reference)
            ->line('Vendor: ' . $vendorName)
            ->line('Submission Date: ' . $this->application->created_at->format('F j, Y \a\t g:i A'))
            ->line('Status: ' . ucfirst(str_replace('_', ' ', $this->application->status)))
            ->action('Review Application', url('/admin/applications/' . $this->application->id))
            ->line('Please review the application and take appropriate action.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_application_submitted',
            'application_id' => $this->application->id,
            'application_reference' => $this->application->application_reference,
            'vendor_name' => $this->application->vendor?->user?->name ?? 'Unknown Vendor',
            'message' => 'New vendor application submitted: ' . $this->application->application_reference,
            'action_url' => url('/admin/applications/' . $this->application->id),
        ];
    }
}
