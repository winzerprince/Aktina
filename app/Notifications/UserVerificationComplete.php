<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserVerificationComplete extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
        $roleDisplayName = ucwords(str_replace('_', ' ', $this->user->role));

        return (new MailMessage)
            ->subject('Verification Complete - Welcome to Aktina SCM!')
            ->greeting('Welcome ' . $notifiable->name . '!')
            ->line('Your account verification is now complete!')
            ->line('Role: ' . $roleDisplayName)
            ->line('You now have full access to the Aktina Supply Chain Management system.')
            ->line('You can log in and start using all the features available for your role.')
            ->action('Access Your Dashboard', url('/dashboard'))
            ->line('If you have any questions, please contact our support team.')
            ->line('Thank you for joining Aktina Technologies!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'verification_complete',
            'user_id' => $this->user->id,
            'role' => $this->user->role,
            'message' => 'Your account verification is complete. Welcome to Aktina SCM!',
            'action_url' => url('/dashboard'),
        ];
    }
}
