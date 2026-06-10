<?php

namespace App\Services;

use App\Models\Message;
use App\Models\MessageNotification;
use Illuminate\Support\Facades\DB;

class MessageNotificationService
{
    /**
     * Tạo thông báo khi có tin nhắn mới gửi đến người dùng.
     * Không tạo nếu người dùng nhắn cho chính mình (phòng bug).
     */
    public static function notifyIncoming(Message $msg): void
    {
        if ($msg->sender_id === $msg->receiver_id) {
            return;
        }

        MessageNotification::create([
            'user_id'    => $msg->receiver_id,  // người nhận thông báo
            'actor_id'   => $msg->sender_id,    // người gửi
            'message_id' => $msg->id,
            'product_id' => $msg->product_id,
            'type'       => 'message',
            'is_read'    => false,
        ]);
    }

    /**
     * Nếu bạn muốn tránh spam: chỉ tạo khi là tin nhắn đầu tiên sau X phút trong cùng conversation.
     * Gọi hàm này thay notifyIncoming() nếu cần gom cụm.
     */
    public static function notifyConversationOnce(Message $msg, int $cooldownMinutes = 5): void
    {
        if ($msg->sender_id === $msg->receiver_id) {
            return;
        }

        $exists = MessageNotification::where('user_id', $msg->receiver_id)
            ->where('actor_id', $msg->sender_id)
            ->where('product_id', $msg->product_id)
            ->where('created_at', '>', now()->subMinutes($cooldownMinutes))
            ->exists();

        if ($exists) return;

        self::notifyIncoming($msg);
    }
}
