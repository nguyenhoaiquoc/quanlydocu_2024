<?php

namespace App\Http\Controllers;

use App\Models\Notification;              // ⬅️ đổi sang bảng unified
use App\Models\Comment;                   // để lấy content/targetUser nếu cần
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CommentNotificationController extends Controller
{
    /**
     * GET /notifications/comment
     */
    public function index()
    {
        $user = Auth::user();
        $defaultAvatar = asset('images/default_avatar.png');

        // Lấy 100 notif comment gần nhất
        $rows = Notification::with(['actor:id,name,image'])
            ->where('user_id', $user->id)
            ->where('category', 'comment')                  // ⬅️ lọc theo category
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();

        // Batch-load comments theo related_id để tránh N+1
        $commentMap = collect();
        $commentIds = $rows->pluck('related_id')->filter()->unique()->values();
        if ($commentIds->isNotEmpty()) {
            $commentMap = Comment::with(['targetUser:id,name'])
                ->whereIn('id', $commentIds)
                ->get()
                ->keyBy('id');
        }

        $data = $rows->map(function ($n) use ($defaultAvatar, $commentMap) {
            $actor   = $n->actor;
            $comment = $n->related_id ? ($commentMap[$n->related_id] ?? null) : null;
            $created = $n->created_at;

            // DB: user_comment | reply_comment → UI: profile | reply
            $typeUi = $n->type === 'reply_comment' ? 'reply' : 'profile';

            // Link đến trang người dùng có anchor comment
            $profileUrl = route('users.show', ['name' => $actor?->name]);
            if ($comment && $comment->target_user_id) {
                $profileUrl = route('users.show', [
                    'name' => $comment->targetUser?->name ?? $actor?->name
                ]);
            }

            return [
                'key'         => $n->id,
                'type'        => $typeUi,
                'user_name'   => $actor?->name ?? 'User',
                'avatar'      => $actor && $actor->image
                    ? asset('images/' . ltrim($actor->image, '/'))
                    : $defaultAvatar,
                'profile_url' => $profileUrl . '#comment-' . ($comment?->id ?? $n->id),
                // ưu tiên content trong data[], fallback sang bảng comments
                'snippet'     => isset($n->data['content'])
                    ? Str::limit((string)$n->data['content'], 50)
                    : ($comment ? Str::limit($comment->content, 50) : ''),
                'time_ago'    => $created?->diffForHumans(),
                'datetime'    => $created?->format('d/m/Y H:i'),
                'is_read'     => (bool) $n->is_read,
            ];
        });

        return response()->json([
            'data'   => $data->values(),
            'unread' => $data->where('is_read', false)->count(),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * POST /notifications/comment/read
     */
    public function markRead(Request $request)
    {
        $id = $request->input('key');

        Notification::where('user_id', Auth::id())
            ->where('category', 'comment')                  // ⬅️ đảm bảo chỉ ảnh hưởng tab comment
            ->where('id', $id)
            ->update(['is_read' => true]);

        return response()->json(['status' => 'success']);
    }

    /**
     * POST /notifications/comment/read-all
     */
    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('category', 'comment')                  // ⬅️ chỉ tab comment
            ->update(['is_read' => true]);

        return response()->json(['status' => 'success']);
    }
}
