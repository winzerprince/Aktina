<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorApplicationApproved extends Notification implements ShouldQueue
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
            ->subject('Application Approved - Welcome to Aktina Technologies!')
            ->greeting('Congratulations ' . $notifiable->name . '!')
            ->line('We are pleased to inform you that your vendor application has been approved!')
            ->line('Application Reference: ' . $this->application->application_reference)
            ->line('Approval Date: ' . now()->format('F j, Y'))
            ->line('You now have full access to the Aktina SCM system. You can start managing your products, orders, and participate in our supply chain network.')
            ->action('Access Your Dashboard', url('/dashboard'))
            ->line('Welcome to the Aktina Technologies partner network!')
            ->line('Our team will be in touch soon with next steps and onboarding materials.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'application_approved',
            'application_id' => $this->application->id,
            'application_reference' => $this->application->application_reference,
            'message' => 'Congratulations! Your vendor application has been approved.',
            'action_url' => url('/dashboard'),
        ];
    }
}
