<?php 
namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Events\FollowCreated;

class FollowNotificationService
{
    // A theo dõi B: B nhận 'in', A nhận 'out'
    public static function followed(User $follower, User $followed): void
    {
        // B (bị theo dõi) nhận 'in'
        $in = Notification::create([
            'user_id'      => $followed->id,
            'actor_id'     => $follower->id,
            'category'     => 'follow',
            'type'         => 'in',
            'related_id'   => $follower->id,
            'related_type' => 'user',
            'data'         => [],
            'is_read'      => false,
        ]);
        event(new FollowCreated($in));

        // A (đang theo dõi) nhận 'out'
        $out = Notification::create([
            'user_id'      => $follower->id,
            'actor_id'     => $followed->id,
            'category'     => 'follow',
            'type'         => 'out',
            'related_id'   => $followed->id,
            'related_type' => 'user',
            'data'         => [],
            'is_read'      => false,
        ]);
        event(new FollowCreated($out));
    }
}
