<?php

// app/Http/Controllers/FollowNotificationController.php
namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowNotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $defaultAvatar = asset('images/default_avatar.png');

        $rows = Notification::with('actor:id,name,image')
            ->where('user_id', $user->id)
            ->where('category', 'follow')        // <-- đọc từ notifications
            ->orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->map(function ($n) use ($defaultAvatar) {
                $actor   = $n->actor;
                $created = $n->created_at;

                return [
                    'key'         => $n->id,
                    'direction'   => $n->type, // 'in' | 'out'
                    'name'        => $actor?->name ?? 'User',
                    'avatar'      => $actor && $actor->image ? asset('images/'.$actor->image) : $defaultAvatar,
                    'profile_url' => route('users.show', ['name' => $actor?->name]),
                    'time_ago'    => $created?->diffForHumans(),
                    'datetime'    => $created?->format('d/m/Y H:i'),
                    'is_read'     => (bool) $n->is_read,
                ];
            });

        return response()->json([
            'data'   => $rows->values(),
            'unread' => $rows->where('is_read', false)->count(),
        ]);
    }

    public function markRead(Request $request)
    {
        $id = $request->input('key');
        Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('category', 'follow')
            ->update(['is_read' => true]);

        return response()->json(['status' => 'success']);
    }

    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('category', 'follow')
            ->update(['is_read' => true]);

        return response()->json(['status' => 'success']);
    }
}

