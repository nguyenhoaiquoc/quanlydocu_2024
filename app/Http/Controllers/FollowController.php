<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    /**
     * Toggle theo dõi / bỏ theo dõi.
     */
    public function toggleFollow(Request $request, $id)
    {
        $userToFollow = User::findOrFail($id);
        $currentUser  = Auth::user();

        if ($currentUser->id === $userToFollow->id) {
            return back()->with('error', 'Bạn không thể theo dõi chính mình');
        }

        // Đã theo dõi? -> Bỏ
        if ($currentUser->followings()->where('following_id', $id)->exists()) {
            $currentUser->followings()->detach($id);
            return back()->with('success', 'Đã bỏ theo dõi.');
        }

        // Chưa theo dõi -> Theo
        $currentUser->followings()->attach($id, [
            'created_at' => Carbon::now('Asia/Ho_Chi_Minh'),
            'is_read'    => false,
        ]);

        // === Ghi thông báo vào bảng "notifications" (2 chiều) ===

        // 1) Cho người BỊ theo dõi (B): "A đã theo dõi bạn"
        Notification::create([
            'user_id'      => $userToFollow->id,   // người nhận thông báo
            'actor_id'     => $currentUser->id,    // người thực hiện hành động
            'category'     => 'follow',
            'type'         => 'in',                // giống logic cũ ('in' | 'out')
            'related_id'   => $currentUser->id,    // tùy chọn: có thể để id actor
            'related_type' => 'user',
            'data'         => [
                'actor_name' => $currentUser->name,
            ],
            'is_read'      => false,
        ]);

        // 2) Cho CHÍNH A: "Bạn đã theo dõi B"
        Notification::create([
            'user_id'      => $currentUser->id,
            'actor_id'     => $userToFollow->id,
            'category'     => 'follow',
            'type'         => 'out',
            'related_id'   => $userToFollow->id,
            'related_type' => 'user',
            'data'         => [
                'actor_name' => $userToFollow->name,
            ],
            'is_read'      => false,
        ]);

        return back()->with('success', 'Đã theo dõi.');
    }

    /**
     * Trang quản lý theo dõi (cũ) – giữ nguyên.
     */
    public function index()
    {
        $user = Auth::user();
        $following = $user->followings()->withCount(['followers', 'followings'])->get();
        $followers = $user->followers()->withCount(['followers', 'followings'])->get();

        return view('follows.index', compact('following', 'followers', 'user'));
    }

    /**
     * Bỏ theo dõi từ trang quản lý – không xoá thông báo.
     */
    public function unfollow($id)
    {
        $user = Auth::user();
        $user->followings()->detach($id);

        return back()->with('success', 'Đã bỏ theo dõi người dùng.');
    }

    /**
     * API trả về Thông báo Follow cho user đang đăng nhập (dùng trong panel).
     * (Trước gọi FollowNotification::..., nay đọc từ Notification)
     */
    public function followNotifications()
    {
        $user = Auth::user();
        $defaultAvatar = asset('images/default_avatar.png');

        $rows = Notification::with('actor:id,name,image')
            ->where('user_id', $user->id)
            ->where('category', 'follow')
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
                    'avatar'      => $actor && $actor->image
                        ? asset('images/' . ltrim($actor->image, '/'))
                        : $defaultAvatar,
                    // Tùy app bạn dùng slug hay id:
                    'profile_url' => route('users.show', ['name' => $actor?->name]),
                    'time_ago'    => $created ? $created->diffForHumans() : '',
                    'datetime'    => $created ? $created->format('d/m/Y H:i') : '',
                    'is_read'     => (bool) $n->is_read,
                    'created_ts'  => $created ? $created->timestamp : 0,
                ];
            });

        return response()->json([
            'data'   => $rows->values(),
            'unread' => $rows->where('is_read', false)->count(),
        ]);
    }

    /**
     * Đánh dấu 1 thông báo follow đã đọc.
     */
    public function markFollowRead(Request $request)
    {
        $id  = $request->input('key');

        Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('category', 'follow')
            ->update(['is_read' => true]);

        return response()->json(['status' => 'success']);
    }

    /**
     * Đánh dấu tất cả thông báo Follow là đã đọc.
     */
    public function markAllFollowRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('category', 'follow')
            ->update(['is_read' => true]);

        return response()->json(['status' => 'success']);
    }
}
