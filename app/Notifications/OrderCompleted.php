<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCompleted extends Notification
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
        $isBuyer = $notifiable->id === $this->order->buyer_id;

        $message = (new MailMessage)
                    ->subject('Order Completed - #' . $this->order->id)
                    ->greeting('Hello!')
                    ->action('View Order', $url);

        if ($isBuyer) {
            $message->line('Your order has been marked as completed.')
                    ->line('Thank you for your business with Aktina!');
        } else {
            $message->line('Order has been marked as completed by the buyer.')
                    ->line('The transaction is now finalized.');
        }

        return $message->line('Order ID: ' . $this->order->id)
                      ->line('Total Items: ' . $this->order->total_items)
                      ->line('Total Price: $' . number_format($this->order->price, 2));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $isBuyer = $notifiable->id === $this->order->buyer_id;
        $message = $isBuyer
            ? 'Your order has been marked as completed. Thank you for your business!'
            : 'Order has been marked as completed by the buyer. The transaction is now finalized.';

        return [
            'order_id' => $this->order->id,
            'message' => $message,
            'price' => $this->order->price,
            'completed_at' => $this->order->updated_at,
        ];
    }
}
