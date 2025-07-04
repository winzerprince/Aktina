<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderApprovalRequest extends Notification implements ShouldQueue
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $approvalUrl = route('orders.approval', [
            'order' => $this->order->id,
            'token' => sha1($this->order->id . $notifiable->id . config('app.key'))
        ]);

        return (new MailMessage)
            ->subject('Order Approval Request #' . $this->order->id)
            ->greeting('Hello ' . $notifiable->name)
            ->line('A new order requires your approval.')
            ->line('Order #' . $this->order->id . ' for $' . number_format($this->order->total_amount, 2))
            ->line('Submitted by: ' . ($this->order->buyer->name ?? 'Unknown'))
            ->action('Review Order', $approvalUrl)
            ->line('Please review and approve or reject this order at your earliest convenience.')
            ->line('Thank you for using Aktina SCM!');
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
            'buyer_id' => $this->order->buyer_id,
            'buyer_name' => $this->order->buyer->name ?? 'Unknown',
            'total_amount' => $this->order->total_amount,
            'items_count' => $this->order->items_count,
            'notification_type' => 'order_approval',
            'message' => 'Order #' . $this->order->id . ' requires your approval',
            'created_at' => now()->toISOString()
        ];
    }
}
