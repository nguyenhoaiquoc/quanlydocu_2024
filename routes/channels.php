<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Chat;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/


Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    $chat = Chat::find($chatId);
    return $chat && in_array($user->id, [$chat->buyer_id, $chat->seller_id]);
});

Broadcast::channel('user.{userId}', fn($user, $userId) => (int)$user->id === (int)$userId);

// NEW: presence channel cho phòng chat
Broadcast::channel('presence-chat.{chat}', function ($user, Chat $chat) {
    if (!in_array($user->id, [$chat->buyer_id, $chat->seller_id])) {
        return false;
    }
    // presence cần return mảng info user
    return [
        'id'     => $user->id,
        'name'   => $user->name,
        'avatar' => $user->image,
    ];
});