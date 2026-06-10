<?php 
namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // <— đúng
use Illuminate\Queue\SerializesModels;

class ProductNotified implements ShouldBroadcastNow
{
    use SerializesModels;

    public function __construct(public int $receiverUserId, public array $data) {}

    public function broadcastOn() { return new PrivateChannel("user.{$this->receiverUserId}"); }
    public function broadcastAs() { return 'product.created'; } // hoặc 'product.notified'
    public function broadcastWith() { return $this->data; }
}
