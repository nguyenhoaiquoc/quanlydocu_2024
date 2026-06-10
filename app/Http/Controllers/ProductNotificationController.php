<?php

// app/Http/Controllers/ProductNotificationController.php
namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductNotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $defaultAvatar = asset('images/default_avatar.png');

        $rows = Notification::with(['actor:id,name,image'])
            ->where('user_id', $user->id)
            ->where('category', 'product')       // <-- đọc từ notifications
            ->orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->map(function ($n) use ($defaultAvatar) {
                $actor   = $n->actor;
                $created = $n->created_at;
                $type    = $n->type;
                $data    = $n->data ?? [];

                // build message giống FE cũ
                $msg = match ($type) {
                    'followed_user_new_product' => "<strong>{$actor?->name}</strong> vừa đăng sản phẩm <strong>".($data['product_name'] ?? '')."</strong>",
                    'product_approved'          => "Sản phẩm <strong>".($data['product_name'] ?? '')."</strong> đã được duyệt",
                    'product_favorited'         => "<strong>{$actor?->name}</strong> đã thích sản phẩm <strong>".($data['product_name'] ?? '')."</strong>",
                    'product_report_pending'    => "Bạn đã báo cáo sản phẩm <strong>".($data['product_name'] ?? '(đã xóa)')."</strong>, chờ xử lý.",
                    'product_report_reviewing'  => "Báo cáo sản phẩm <strong>".($data['product_name'] ?? '(đã xóa)')."</strong> đang được xem xét.",
                    'product_report_resolved'   => "Báo cáo sản phẩm <strong>".($data['product_name'] ?? '(đã xóa)')."</strong> đã được xử lý.",
                    'product_report_dismissed'  => "Báo cáo sản phẩm <strong>".($data['product_name'] ?? '(đã xóa)')."</strong> đã bị bỏ qua.",
                    'product_report_product_deleted' => "Sản phẩm bạn báo cáo đã bị xóa bởi admin.",
                    default => "Thông báo sản phẩm",
                };

                return [
                    'key'         => $n->id,
                    'type'        => $type,
                    'message'     => $msg,
                    'actor_name'  => $actor?->name,
                    'avatar'      => $actor && $actor->image ? asset('images/'.$actor->image) : $defaultAvatar,
                    'product_url' => $data['product_url'] ?? null,
                    'time_ago'    => $created?->diffForHumans(),
                    'datetime'    => $created?->format('d/m/Y H:i'),
                    'is_read'     => (bool)$n->is_read,
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
            ->where('category', 'product')
            ->update(['is_read' => true]);

        return response()->json(['status' => 'success']);
    }

    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('category', 'product')
            ->update(['is_read' => true]);

        return response()->json(['status' => 'success']);
    }
}
