<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\ProductExpired;

class NotificationController extends Controller
{
    /**
     * Lấy danh sách thông báo sản phẩm hết hạn
     */
    public function product()
    {
        $notifications = auth()->user()->notifications()
            ->where('type', ProductExpired::class)
            ->latest()
            ->get();

        $unreadCount = auth()->user()->unreadNotifications()
            ->where('type', ProductExpired::class)
            ->count();

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'data' => $notification->data,
                    'created_at' => $notification->created_at->toDateTimeString(),
                    'read_at' => $notification->read_at ? $notification->read_at->toDateTimeString() : null,
                ];
            }),
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Đánh dấu một thông báo sản phẩm đã đọc
     */
    public function productRead($id)
    {
        $notification = auth()->user()->notifications()
            ->where('type', ProductExpired::class)
            ->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Đánh dấu tất cả thông báo sản phẩm đã đọc
     */
    public function productReadAll()
    {
        auth()->user()->unreadNotifications()
            ->where('type', ProductExpired::class)
            ->get()
            ->each->markAsRead();

        return response()->json(['success' => true]);
    }

    // Placeholder cho các tab khác
    public function message()
    {
        // TODO: Thêm logic lấy thông báo tin nhắn
        return response()->json(['notifications' => [], 'unread_count' => 0]);
    }

    public function messageRead($id)
    {
        // TODO: Thêm logic đánh dấu tin nhắn đã đọc
        return response()->json(['success' => true]);
    }

    public function messageReadAll()
    {
        // TODO: Thêm logic đánh dấu tất cả tin nhắn đã đọc
        return response()->json(['success' => true]);
    }

    public function follow()
    {
        // TODO: Thêm logic lấy thông báo follow
        return response()->json(['notifications' => [], 'unread_count' => 0]);
    }

    public function followRead($id)
    {
        // TODO: Thêm logic đánh dấu follow đã đọc
        return response()->json(['success' => true]);
    }

    public function followReadAll()
    {
        // TODO: Thêm logic đánh dấu tất cả follow đã đọc
        return response()->json(['success' => true]);
    }

    public function comment()
    {
        // TODO: Thêm logic lấy thông báo bình luận
        return response()->json(['notifications' => [], 'unread_count' => 0]);
    }

    public function commentRead($id)
    {
        // TODO: Thêm logic đánh dấu bình luận đã đọc
        return response()->json(['success' => true]);
    }

    public function commentReadAll()
    {
        // TODO: Thêm logic đánh dấu tất cả bình luận đã đọc
        return response()->json(['success' => true]);
    }
}