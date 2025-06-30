<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification
{
    use Queueable;

    protected $order;
    protected $lowStockItems;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->lowStockItems = $this->getLowStockItems();
    }

    /**
     * Get low stock items from the order
     */
    protected function getLowStockItems(): array
    {
        $items = $this->order->getItemsAsArray();
        $lowStockItems = [];

        foreach ($items as $item) {
            $product = \App\Models\Product::find($item['product_id'] ?? null);
            if ($product) {
                // For a real system, this would check against inventory
                // This is just a placeholder logic
                $lowStockItems[] = [
                    'product_name' => $product->name,
                    'product_id' => $product->id,
                    'quantity_requested' => $item['quantity'] ?? 0,
                ];
            }
        }

        return $lowStockItems;
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
        $url = url("/production");

        $mail = (new MailMessage)
                    ->subject('Low Stock Alert - Order #' . $this->order->id)
                    ->greeting('Attention Required!')
                    ->line('An order contains items with low inventory levels:')
                    ->action('Go To Production', $url);

        foreach ($this->lowStockItems as $item) {
            $mail->line("- {$item['product_name']}: {$item['quantity_requested']} units requested");
        }

        return $mail->line('Please consider initiating production to replenish inventory.');
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
            'message' => 'Low stock alert for items in an order. Production needed.',
            'low_stock_items' => $this->lowStockItems,
        ];
    }
}
