<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorApplicationReceived extends Notification implements ShouldQueue
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
            ->subject('Application Received - ' . $this->application->application_reference)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We have successfully received your vendor application.')
            ->line('Application Reference: ' . $this->application->application_reference)
            ->line('Submission Date: ' . $this->application->created_at->format('F j, Y'))
            ->line('Your application is now being reviewed by our team. You will receive an email notification once it has been processed by our system.')
            ->action('View Application Status', url('/verification/vendor'))
            ->line('Thank you for your interest in partnering with Aktina Technologies!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'application_received',
            'application_id' => $this->application->id,
            'application_reference' => $this->application->application_reference,
            'message' => 'Your vendor application has been received and is being reviewed.',
            'action_url' => url('/verification/vendor'),
        ];
    }
}
