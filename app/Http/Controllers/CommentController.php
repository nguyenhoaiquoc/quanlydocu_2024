<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CommentNotificationService; // Thêm dòng này

class CommentController extends Controller
{
    /**
     * Lưu bình luận cho một sản phẩm
     * Route:  POST /products/{product}/comment
     */
    public function storeForProduct(Request $request, Product $product)
    {
        $data = $request->validate([
            'content' => 'required|string',
            'rating'  => 'required|integer|min:1|max:5',
        ]);

        $comment = $product->comments()->create([
            'content' => $data['content'],
            'rating'  => $data['rating'],
            'user_id' => Auth::id(),
        ]);

        // Gửi thông báo cho chủ sản phẩm (nếu không phải là người bình luận)
        CommentNotificationService::notifyUserComment($product->user, $comment);

        return $request->expectsJson()
            ? response()->json([
                'user'    => $comment->user->name,
                'content' => $comment->content,
                'rating'  => $comment->rating,
                'created' => $comment->created_at->diffForHumans(),
            ])
            : back()->with('success', 'Bình luận của bạn đã được gửi.');
    }

    /**
     * Lưu bình luận cho hồ sơ người bán
     * Route:  POST /users/{user}/comment
     */
    public function storeForUser(Request $request, User $user)
    {
        $data = $request->validate([
            'content' => 'required|string',
            'rating'  => 'required|integer|min:1|max:5',
        ]);

        $comment = Comment::create([
            'content'         => $data['content'],
            'rating'          => $data['rating'],
            'user_id'         => Auth::id(), // người viết
            'target_user_id'  => $user->id,  // người được đánh giá
        ]);

        // Gửi thông báo cho chủ trang cá nhân
        CommentNotificationService::notifyUserComment($user, $comment);

        return $request->expectsJson()
            ? response()->json([
                'user'    => $comment->user->name,
                'content' => $comment->content,
                'rating'  => $comment->rating,
                'created' => $comment->created_at->diffForHumans(),
            ])
            : back()->with('success', 'Bình luận của bạn đã được gửi.');
    }

    /**
     * Trả lời một bình luận
     * Route:  POST /comments/{comment}/reply
     */
    public function reply(Request $request, Comment $comment)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $reply = $comment->replies()->create([
            'content' => $request->input('content'),
            'user_id' => Auth::id(),
        ]);

        // Gửi thông báo cho người đã viết comment gốc
        CommentNotificationService::notifyReply($comment, $reply);

        return response()->json([
            'id'      => $reply->id,
            'user'    => $reply->user->name,
            'content' => $reply->content,
            'created' => $reply->created_at->diffForHumans(),
        ]);
    }
}
