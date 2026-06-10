<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class HeartbeatController extends Controller
{
    public function ping()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['ok' => false], 401);
        }

        // 1) Đặt cờ online trong cache 90s (ưu tiên Redis)
        Cache::put("user:online:{$user->id}", true, now()->addSeconds(90));

        // 2) Cập nhật last_login_at thưa (10 phút/lần) để dùng làm "Hoạt động x phút trước"
        if (!$user->last_login_at || $user->last_login_at->lt(now()->subMinutes(10))) {
            $user->forceFill(['last_login_at' => now()])->save();
        }

        return response()->json(['ok' => true]);
    }
}
