<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // <— đúng

class MessageCreated implements ShouldBroadcastNow
{
    use SerializesModels;

    public $message;
    public $chatId;
    public $receiverId;

    public function __construct(\App\Models\Message $message, int $receiverId)
    {
        // tai toi thieu truong can dung tu sender
        $this->message = $message->load(['sender:id,name,image']); // đổi 'avatar' thành tên cột bạn đang dùng
        $this->chatId = $message->chat_id;
        $this->receiverId = $receiverId;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('chat.' . $this->chatId),
            new PrivateChannel('user.' . $this->receiverId),
        ];
    }

    public function broadcastAs()
    {
        return 'message.created';
    }

    public function broadcastWith(): array
    {
        $sender = $this->message->sender;

        return [
            'id'          => $this->message->id,
            'chat_id'     => $this->message->chat_id,
            'sender_id'   => $this->message->sender_id,
            'sender_name' => $sender?->name,
            'sender_avatar' => $this->avatarUrl($sender), // <— thêm
            'message'     => $this->message->message,
            'file_path'   => $this->message->file_path,
            'file_type'   => $this->message->file_type,
            'is_revoked'  => (bool) $this->message->is_revoked,
            'created_at'  => $this->message->created_at?->toISOString(),
            'file_url'    => $this->message->file_path ? asset($this->message->file_path) : null,
        ];
    }

  private function avatarUrl($user): string
{
    if (!$user) {
        return asset('images/default_avatar.png');
    }

    // Nếu image đã là URL đầy đủ thì trả luôn
    if (preg_match('#^https?://#', $user->image)) {
        return $user->image;
    }

    // Nếu chỉ lưu tên file hoặc đường dẫn con
    if ($user->image) {
        return asset('images/' . ltrim($user->image, '/'));
    }

    return asset('images/default_avatar.png');
}
}