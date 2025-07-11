<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\InventoryAlert as InventoryAlertModel;

class InventoryAlert extends Notification
{
    use Queueable;

    public $alert;

    /**
     * Create a new notification instance.
     */
    public function __construct(InventoryAlertModel $alert)
    {
        $this->alert = $alert;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'alert_id' => $this->alert->id,
            'resource_name' => $this->alert->resource->name,
            'alert_type' => $this->alert->alert_type,
            'current_value' => $this->alert->current_value,
            'threshold_value' => $this->alert->threshold_value,
            'title' => ucfirst(str_replace('_', ' ', $this->alert->alert_type)) . ' Alert',
            'message' => "Resource '{$this->alert->resource->name}' has {$this->alert->alert_type}. Current: {$this->alert->current_value}, Threshold: {$this->alert->threshold_value}"
        ];
    }
}
