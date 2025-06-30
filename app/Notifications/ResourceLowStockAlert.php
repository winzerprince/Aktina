<?php

namespace App\Notifications;

use App\Models\ResourceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResourceLowStockAlert extends Notification
{
    use Queueable;

    protected $order;
    protected $lowStockItems;

    /**
     * Create a new notification instance.
     */
    public function __construct(ResourceOrder $order)
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
            $resource = \App\Models\Resource::find($item['resource_id'] ?? null);
            if ($resource && $resource->isLowStock()) {
                $lowStockItems[] = [
                    'resource_name' => $resource->name,
                    'resource_id' => $resource->id,
                    'quantity_requested' => $item['quantity'] ?? 0,
                    'current_stock' => $resource->units,
                    'reorder_level' => $resource->reorder_level,
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
        $url = url("/resources");

        $mail = (new MailMessage)
                    ->subject('Low Resource Stock Alert - Order #' . $this->order->id)
                    ->greeting('Attention Required!')
                    ->line('A resource order contains items with low inventory levels:')
                    ->action('Manage Resources', $url);

        foreach ($this->lowStockItems as $item) {
            $mail->line("- {$item['resource_name']}: {$item['current_stock']} units in stock (below reorder level of {$item['reorder_level']})");
        }

        return $mail->line('Please consider ordering more resources from suppliers.');
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
            'message' => 'Low resource stock alert. Please reorder more resources.',
            'low_stock_items' => $this->lowStockItems,
        ];
    }
}
