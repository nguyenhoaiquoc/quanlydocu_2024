<?php

// app/Services/CommentNotificationService.php
namespace App\Services;
use Illuminate\Support\Str;

use App\Models\Notification;
use App\Models\User;
use App\Models\Comment;
use App\Events\CommentCreated;

class CommentNotificationService
{
    public static function notifyUserComment(User $targetUser, Comment $comment): void
{
    if (!$targetUser || $targetUser->id === $comment->user_id) return;

    $notif = Notification::create([
        'user_id'      => $targetUser->id,
        'actor_id'     => $comment->user_id,
        'category'     => 'comment',
        'type'         => 'user_comment',
        'related_id'   => $comment->id,
        'related_type' => 'comment',
        'data'         => ['content' => $comment->content],
        'is_read'      => false,
    ]);

    $actor = User::select('id','name','image')->find($comment->user_id);
    $payload = [
        'key'         => $notif->id,
        'type'        => 'profile',
        'user_name'   => $actor?->name ?? 'User',
        'avatar'      => self::avatarUrl($actor),
        'profile_url' => route('users.show', ['name' => $actor?->name]) . '#comment-' . $comment->id,
        'snippet'     => Str::limit((string)$comment->content, 50),
        'datetime'    => optional($notif->created_at)->format('d/m/Y H:i'),
        'is_read'     => false,
    ];

    event(new \App\Events\CommentCreated($notif->user_id, $payload)); // ĐÚNG
}

public static function notifyReply(Comment $parent, Comment $reply): void
{
    if (!$parent->user_id || $parent->user_id === $reply->user_id) return;

    $notif = Notification::create([
        'user_id'      => $parent->user_id,
        'actor_id'     => $reply->user_id,
        'category'     => 'comment',
        'type'         => 'reply_comment',
        'related_id'   => $reply->id,
        'related_type' => 'comment',
        'data'         => ['content' => $reply->content],
        'is_read'      => false,
    ]);

    $actor = User::select('id','name','image')->find($reply->user_id);
    $payload = [
        'key'         => $notif->id,
        'type'        => 'reply',
        'user_name'   => $actor?->name ?? 'User',
        'avatar'      => self::avatarUrl($actor),
        'profile_url' => route('users.show', ['name' => $actor?->name]) . '#comment-' . $reply->id,
        'snippet'     => Str::limit((string)$reply->content, 50),
        'datetime'    => optional($notif->created_at)->format('d/m/Y H:i'),
        'is_read'     => false,
    ];

    event(new \App\Events\CommentCreated($notif->user_id, $payload)); // ĐÚNG
}
  private static function avatarUrl(?User $user): string
    {
        if (!$user) return asset('images/default_avatar.png');
        // nếu image là URL tuyệt đối
        if ($user->image && preg_match('#^https?://#', $user->image)) {
            return $user->image;
        }
        // nếu chỉ là tên file / đường dẫn tương đối trong public/images
        return $user->image
            ? asset('images/' . ltrim($user->image, '/'))
            : asset('images/default_avatar.png');
    }
}
