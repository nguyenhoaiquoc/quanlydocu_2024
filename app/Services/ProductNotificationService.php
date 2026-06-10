<?php

namespace App\Services;

use App\Events\ProductNotified;
use App\Models\Notification;
use App\Models\Product;
use App\Models\ProductReport;
use App\Models\User;

class ProductNotificationService
{
    private static function avatarUrl(?User $u): string
    {
        if (!$u) return asset('images/default_avatar.png');
        if ($u->image && preg_match('#^https?://#', $u->image)) return $u->image;
        return $u->image ? asset('images/' . ltrim($u->image, '/')) : asset('images/default_avatar.png');
    }

    /** A đăng sản phẩm mới -> gửi cho toàn bộ follower của A */
    public static function notifyFollowersNewProduct(Product $product): void
    {
        $owner = $product->user;
        $followerIds = $owner->followers()->pluck('users.id'); // bảng pivot follows

        foreach ($followerIds as $uid) {
            $notif = Notification::create([
                'user_id'      => (int)$uid,        // người nhận
                'actor_id'     => $owner->id,       // chủ sản phẩm
                'category'     => 'product',
                'type'         => 'followed_user_new_product',
                'related_id'   => $product->id,
                'related_type' => 'product',
                'data'         => [
                    'actor_name'  => $owner->name,
                    'product_id'  => $product->id,
                    'product_name'=> $product->name,
                    'avatar'      => self::avatarUrl($owner),
                    'product_url' => route('products.user.show', $product->id),
                ],
                'is_read'      => false,
            ]);

            event(new ProductNotified($notif->user_id, [
                'key'        => $notif->id,
                'category'   => 'product',
                'type'       => 'followed_user_new_product',
                'message'    => "<strong>{$owner->name}</strong> vừa đăng sản phẩm <strong>{$product->name}</strong>",
                'avatar'     => self::avatarUrl($owner),
                'product_id' => $product->id,
                'product_url'=> route('products.user.show', $product->id),
                'is_read'    => false,
                'created_at' => $notif->created_at?->toISOString(),
            ]));
        }
    }

    /** Admin duyệt sản phẩm -> gửi cho chủ sản phẩm */
    public static function notifyProductApproved(Product $product, ?User $admin = null): void
    {
        $notif = Notification::create([
            'user_id'      => $product->user_id,
            'actor_id'     => $admin?->id,
            'category'     => 'product',
            'type'         => 'product_approved',
            'related_id'   => $product->id,
            'related_type' => 'product',
            'data'         => [
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'actor_name'   => $admin?->name,
                'avatar'       => self::avatarUrl($admin),
                'product_url'  => route('products.user.show', $product->id),
            ],
            'is_read'      => false,
        ]);

        event(new ProductNotified($notif->user_id, [
            'key'        => $notif->id,
            'category'   => 'product',
            'type'       => 'product_approved',
            'message'    => "Sản phẩm <strong>{$product->name}</strong> đã được duyệt",
            'avatar'     => self::avatarUrl($admin),
            'product_id' => $product->id,
            'product_url'=> route('products.user.show', $product->id),
            'is_read'    => false,
            'created_at' => $notif->created_at?->toISOString(),
        ]));
    }

    /** Ai đó thích sản phẩm -> gửi cho chủ sản phẩm */
    public static function notifyFavorited(Product $product, User $favoritedBy): void
    {
        if ($favoritedBy->id === $product->user_id) return;

        $notif = Notification::create([
            'user_id'      => $product->user_id,
            'actor_id'     => $favoritedBy->id,
            'category'     => 'product',
            'type'         => 'product_favorited',
            'related_id'   => $product->id,
            'related_type' => 'product',
            'data'         => [
                'actor_name'   => $favoritedBy->name,
                'avatar'       => self::avatarUrl($favoritedBy),
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'product_url'  => route('products.user.show', $product->id),
            ],
            'is_read'      => false,
        ]);

        event(new ProductNotified($notif->user_id, [
            'key'        => $notif->id,
            'category'   => 'product',
            'type'       => 'product_favorited',
            'message'    => "<strong>{$favoritedBy->name}</strong> đã thích sản phẩm <strong>{$product->name}</strong>",
            'avatar'     => self::avatarUrl($favoritedBy),
            'product_id' => $product->id,
            'product_url'=> route('products.user.show', $product->id),
            'is_read'    => false,
            'created_at' => $notif->created_at?->toISOString(),
        ]));
    }

    /** Trạng thái xử lý báo cáo sản phẩm -> gửi cho người báo cáo */
    public static function notifyReportStatus(ProductReport $report): void
    {
        $product = Product::withTrashed()->find($report->product_id);
        $type = $product ? ('product_report_' . $report->status) : 'product_report_product_deleted';

        $notif = Notification::create([
            'user_id'      => $report->reporter_id,
            'actor_id'     => $report->admin_id,
            'category'     => 'product',
            'type'         => $type,
            'related_id'   => $product?->id,
            'related_type' => 'product',
            'data'         => [
                'status'       => $report->status,
                'product_id'   => $product?->id,
                'product_name' => $product?->name,
                'product_url'  => $product ? route('products.user.show', $product->id) : null,
            ],
            'is_read'      => false,
        ]);

        $messageMap = [
            'product_report_pending'        => "Bạn đã báo cáo sản phẩm <strong>" . ($product?->name ?? '(đã xóa)') . "</strong>, chờ xử lý.",
            'product_report_reviewing'      => "Báo cáo sản phẩm <strong>" . ($product?->name ?? '(đã xóa)') . "</strong> đang được xem xét.",
            'product_report_resolved'       => "Báo cáo sản phẩm <strong>" . ($product?->name ?? '(đã xóa)') . "</strong> đã được xử lý.",
            'product_report_dismissed'      => "Báo cáo sản phẩm <strong>" . ($product?->name ?? '(đã xóa)') . "</strong> đã bị bỏ qua.",
            'product_report_product_deleted'=> "Sản phẩm bạn báo cáo đã bị xóa bởi admin.",
        ];

        event(new ProductNotified($notif->user_id, [
            'key'        => $notif->id,
            'category'   => 'product',
            'type'       => $type,
            'message'    => $messageMap[$type] ?? 'Thông báo sản phẩm',
            'product_id' => $product?->id,
            'product_url'=> $product ? route('products.user.show', $product->id) : null,
            'is_read'    => false,
            'created_at' => $notif->created_at?->toISOString(),
        ]));
    }
}
