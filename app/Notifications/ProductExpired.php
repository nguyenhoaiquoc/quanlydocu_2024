<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ProductExpired extends Notification
{
    public $product;

    public function __construct($product)
    {
        $this->product = $product;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return new DatabaseMessage([
            'type' => 'product',
            'message' => "Sản phẩm '{$this->product->name}' của bạn đã hết hạn vào {$this->product->updated_at->addDays(7)->format('d/m/Y H:i')}.",
            'product_id' => $this->product->id,
            'read_at' => null,
        ]);
    }
}