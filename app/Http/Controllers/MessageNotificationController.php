<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageNotificationController extends Controller
{
    // GET /notifications/message
    public function index()
    {
        $userId = Auth::id();
        $defaultAvatar = asset('images/default_avatar.png');

        $rows = Notification::with('actor:id,name,image')
            ->where('user_id', $userId)
            ->where('category', 'message')              // dùng unified table
            ->orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->map(function ($n) use ($defaultAvatar) {
                $actor = $n->actor;
                $avatar = $actor && $actor->image
                    ? asset('images/' . ltrim($actor->image, '/'))
                    : $defaultAvatar;

                // FE dùng shape "mới": key/is_read/datetime/...
                return [
                    'key'       => $n->id,
                    'is_read'   => (bool)$n->is_read,
                    'datetime'  => optional($n->created_at)->format('d/m/Y H:i'),
                    // các field phẳng để FE map sang template:
                    'avatar'    => $n->data['avatar'] ?? $avatar,
                    'actor_name'=> $n->data['actor_name'] ?? ($actor?->name ?? 'User'),
                    'snippet'   => $n->data['snippet'] ?? $n->data['message'] ?? '',
                    'chat_url'  => $n->data['chat_url'] ?? (isset($n->data['chat_id']) ? '/chat/'.$n->data['chat_id'] : '/chat'),
                ];
            });

        return response()->json([
            'data'   => $rows->values(),
            'unread' => $rows->where('is_read', false)->count(),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    // POST /notifications/message/read
    public function markRead(Request $request)
    {
        $id = $request->input('key');

        Notification::where('user_id', Auth::id())
            ->where('category', 'message')
            ->where('id', $id)
            ->update(['is_read' => true]);

        return response()->json(['status' => 'success']);
    }

    // POST /notifications/message/read-all
    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('category', 'message')
            ->update(['is_read' => true]);

        return response()->json(['status' => 'success']);
    }
}
