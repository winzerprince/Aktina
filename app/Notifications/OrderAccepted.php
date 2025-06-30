<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderAccepted extends Notification
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
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
        $url = url("/orders/{$this->order->id}");

        return (new MailMessage)
                    ->subject('Order Accepted - #' . $this->order->id)
                    ->greeting('Hello!')
                    ->line('Your order has been accepted and is being processed.')
                    ->line('Order ID: ' . $this->order->id)
                    ->line('Total Items: ' . $this->order->total_items)
                    ->line('Total Price: $' . number_format($this->order->price, 2))
                    ->action('View Order', $url)
                    ->line('You will be notified once your order is complete and ready for pickup or delivery.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'message' => 'Your order has been accepted and is being processed.',
            'price' => $this->order->price,
            'seller_name' => $this->order->seller->name,
            'updated_at' => $this->order->updated_at,
        ];
    }
}
