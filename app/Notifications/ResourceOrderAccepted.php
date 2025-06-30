<?php

namespace App\Notifications;

use App\Models\ResourceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResourceOrderAccepted extends Notification
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
                    ->subject('Resource Order Accepted - #' . $this->order->id)
                    ->greeting('Hello!')
                    ->line('Your resource order has been accepted by the supplier and is being processed.')
                    ->line('Order ID: ' . $this->order->id)
                    ->line('Total Items: ' . $this->order->total_items)
                    ->line('Total Price: $' . number_format($this->order->price, 2))
                    ->action('View Resource Order', $url)
                    ->line('You will be notified once your resource order is complete and ready for delivery.');
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
            'message' => 'Your resource order has been accepted by the supplier and is being processed.',
            'price' => $this->order->price,
            'seller_name' => $this->order->seller->name,
            'updated_at' => $this->order->updated_at,
        ];
    }
}
