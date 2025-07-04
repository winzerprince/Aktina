<?php

namespace App\Notifications;

use App\Models\SystemPerformance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemPerformanceAlert extends Notification implements ShouldQueue
{
    use Queueable;

    protected $performance;
    protected $alerts;

    /**
     * Create a new notification instance.
     */
    public function __construct(SystemPerformance $performance, array $alerts)
    {
        $this->performance = $performance;
        $this->alerts = $alerts;
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
        $mail = (new MailMessage)
            ->subject('System Performance Alert')
            ->greeting('Hello ' . $notifiable->name)
            ->line('The following system performance alerts have been triggered:');
        
        foreach ($this->alerts as $alert) {
            $mail->line('- ' . $alert);
        }
        
        $mail->action('View System Dashboard', route('admin.system.performance'))
            ->line('Please check the system and take necessary actions.')
            ->line('Thank you for using Aktina SCM!');
            
        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'performance_id' => $this->performance->id,
            'cpu_usage' => $this->performance->cpu_usage,
            'memory_usage' => $this->performance->memory_usage,
            'disk_usage' => $this->performance->disk_usage,
            'response_time' => $this->performance->response_time,
            'alerts' => $this->alerts,
            'notification_type' => 'system_performance',
            'message' => count($this->alerts) . ' system performance alerts detected',
            'created_at' => now()->toISOString()
        ];
    }
}
