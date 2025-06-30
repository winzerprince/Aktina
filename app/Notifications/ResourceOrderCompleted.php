<?php

namespace App\Notifications;

use App\Models\ResourceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResourceOrderCompleted extends Notification
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
        $isBuyer = $notifiable->id === $this->order->buyer_id;

        $message = (new MailMessage)
                    ->subject('Resource Order Completed - #' . $this->order->id)
                    ->greeting('Hello!')
                    ->action('View Resource Order', $url);

        if ($isBuyer) {
            $message->line('Your resource order has been marked as completed.')
                    ->line('The resources have been added to your inventory.');
        } else {
            $message->line('Resource order has been marked as completed by Aktina Technologies.')
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
            ? 'Your resource order has been marked as completed. Resources have been added to inventory.'
            : 'Resource order has been marked as completed by Aktina Technologies. The transaction is now finalized.';

        return [
            'resource_order_id' => $this->order->id,
            'message' => $message,
            'price' => $this->order->price,
            'completed_at' => $this->order->updated_at,
        ];
    }
}
