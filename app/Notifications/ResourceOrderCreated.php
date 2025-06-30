<?php

namespace App\Notifications;

use App\Models\ResourceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResourceOrderCreated extends Notification
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(ResourceOrder $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url("/resource-orders/{$this->order->id}");

        return (new MailMessage)
                    ->subject('New Resource Order Received - #' . $this->order->id)
                    ->greeting('Hello!')
                    ->line('A new resource order has been placed by Aktina Technologies and is pending your review.')
                    ->line('Order ID: ' . $this->order->id)
                    ->line('Total Items: ' . $this->order->total_items)
                    ->line('Total Price: $' . number_format($this->order->price, 2))
                    ->action('View Resource Order', $url)
                    ->line('Please review and process this resource order as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'resource_order_id' => $this->order->id,
            'message' => 'A new resource order has been placed and is pending your review.',
            'price' => $this->order->price,
            'buyer_name' => $this->order->buyer->name,
            'created_at' => $this->order->created_at,
        ];
    }
}
