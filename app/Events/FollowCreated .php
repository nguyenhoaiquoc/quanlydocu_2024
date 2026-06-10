<?php 
namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class FollowCreated implements ShouldBroadcastNow
{
    use SerializesModels;

    public function __construct(
        public int $receiverUserId,   // người sẽ nhận notif (user_id)
        public array $data            // payload đã map sẵn cho FE
    ) {}

    public function broadcastOn() { return new PrivateChannel("user.{$this->receiverUserId}"); }
    public function broadcastAs() { return 'follow.created'; } // tên sự kiện FE sẽ listen
    public function broadcastWith() { return $this->data; }    // truyền thẳng data
}
