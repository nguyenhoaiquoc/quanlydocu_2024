<?php
namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Cache;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Events\MessageCreated;

class ChatController extends Controller
{
public function getMessages($chatId)
{
    $chat = Chat::findOrFail($chatId);

    // Chỉ cho phép buyer hoặc seller trong chat xem tin nhắn
    if (Auth::id() !== $chat->buyer_id && Auth::id() !== $chat->seller_id) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $messages = $chat->messages()
        ->with('sender')
        ->orderBy('created_at', 'asc')
        ->get()
        ->map(function ($msg) {
            // Nếu có file -> tạo URL đầy đủ
            if (!empty($msg->file_path)) {
                // file_path đang lưu dạng "images/chat/tenfile.jpg"
                $msg->file_url  = asset($msg->file_path); // không còn 'storage/'
                $msg->file_type = $msg->file_type ?? 'file'; // image / video / file
            } else {
                $msg->file_url  = null;
                $msg->file_type = null;
            }

            // Hiển thị nội dung tin nhắn
            $msg->display_message = $msg->is_revoked
                ? 'Tin nhắn đã bị thu hồi'
                : ($msg->message ?? ($msg->file_path ? basename($msg->file_path) : ''));

            return $msg;
        });

    return response()->json($messages);
}




public function storeMessage(Request $request, $chatId)
{
    $chat = Chat::findOrFail($chatId);

    if (Auth::id() !== $chat->buyer_id && Auth::id() !== $chat->seller_id) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $messageData = [
        'chat_id'   => $chatId,
        'sender_id' => Auth::id(),
    ];

    if ($request->hasFile('file')) {
        $file = $request->file('file');

        // Lấy mime type trước khi move file
        $mimeType = $file->getMimeType();

        // Lưu vào thư mục public/images/chat
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('images/chat'), $filename);

        $messageData['file_path'] = 'images/chat/' . $filename;
        $messageData['file_type'] = str_starts_with($mimeType, 'image') ? 'image' : 'video';
        $messageData['message'] = $request->input('message') ?: (
            $messageData['file_type'] === 'image' ? '[Hình ảnh]' : '[Video]'
        );
    } else {
        $request->validate(['message' => 'required|string|max:1000']);
        $messageData['message'] = $request->message;
    }

    $message = Message::create($messageData);

$chat->refresh();
$receiverId = $message->sender_id == $chat->buyer_id ? $chat->seller_id : $chat->buyer_id;
broadcast(new MessageCreated($message, $receiverId))->toOthers();
\Log::info("Broadcasted to user.{$receiverId}, chat.{$message->chat_id}");

    if ($message->file_path) {
        // Vì lưu ở public/images/chat nên dùng asset trực tiếp
        $message->file_url = asset($message->file_path);
    }

    return response()->json($message, 201);
}




    // Tạo chat mới
   public function createChat(Request $request)
{
    $buyerId = auth()->id();
    $sellerId = $request->seller_id ?? Product::findOrFail($request->product_id)->user_id;

    // Tìm chat đã tồn tại giữa 2 người (không quan tâm product_id)
    $chat = Chat::where(function ($q) use ($buyerId, $sellerId) {
                $q->where('buyer_id', $buyerId)->where('seller_id', $sellerId);
            })
            ->orWhere(function ($q) use ($buyerId, $sellerId) {
                $q->where('buyer_id', $sellerId)->where('seller_id', $buyerId);
            })
            ->first();

    // Nếu chưa có → tạo mới
    if (!$chat) {
        $chat = Chat::create([
            'product_id' => $request->product_id,
            'buyer_id'   => $buyerId,
            'seller_id'  => $sellerId
        ]);
    } else {
        // Nếu muốn cập nhật product_id lần gần nhất
        $chat->update(['product_id' => $request->product_id]);
    }

    return response()->json([
        'chat_id' => $chat->id
    ]);
}

public function getConversations(Request $request)
{
    $userId = Auth::id();
    $q = trim((string) $request->get('q', ''));

    $chats = Chat::query()
        ->where(function ($w) use ($userId) {
            $w->where('buyer_id', $userId)->orWhere('seller_id', $userId);
        })

        // 🔎 Nếu có q, lọc theo tên partner
        ->when($q !== '', function ($w) use ($q, $userId) {
            $w->where(function ($sub) use ($q, $userId) {
                // Nếu mình là buyer → partner là seller
                $sub->where(function ($x) use ($q, $userId) {
                    $x->where('buyer_id', $userId)
                      ->whereHas('seller', fn($u) => $u->where('name', 'like', "%{$q}%"));
                })
                // Nếu mình là seller → partner là buyer
                ->orWhere(function ($x) use ($q, $userId) {
                    $x->where('seller_id', $userId)
                      ->whereHas('buyer', fn($u) => $u->where('name', 'like', "%{$q}%"));
                });
            });
        })

        ->with([
            'messages' => fn($q) => $q->latest()->limit(1),
            'buyer:id,name,image,last_login_at',
            'seller:id,name,image,last_login_at',
            'product:id,name,price,image',
        ])
        ->latest()
        ->get();

    // (Giữ nguyên phần format như bạn đã có)
    $formatted = $chats->map(function ($chat) use ($userId) {
        $partner     = ($chat->buyer_id === $userId) ? $chat->seller : $chat->buyer;
        $partnerId   = ($chat->buyer_id === $userId) ? $chat->seller_id : $chat->buyer_id;

        $isOnline = $partner && \Illuminate\Support\Facades\Cache::has("user:online:{$partner->id}");
        $statusText = $isOnline
            ? 'Đang hoạt động'
            : ($partner?->last_login_at ? 'Hoạt động ' . $partner->last_login_at->diffForHumans() : 'Ngoại tuyến');

        return [
            'id'          => $chat->id,
            'name'        => $partner->name ?? 'Người dùng',
            'image'       => asset('images/' . ($partner->image ?? 'default_avatar.png')),
            'lastMessage' => optional($chat->messages->first())->message ?? 'Chưa có tin nhắn',
            'timestamp'   => optional($chat->messages->first()?->created_at)->format('H:i') ?? '',
            'profile_url' => route('users.show', ['name' => $partner->name ?? 'user']),
            'partner_id'  => $partnerId,
            'status'      => $statusText,
            'product'     => [
                'name'  => $chat->product->name ?? '',
                'price' => number_format($chat->product->price ?? 0, 0, ',', '.') . ' đ',
                'image' => asset('images/' . ($chat->product->image ?? 'placeholder.png')),
            ],
        ];
    })->values();

    return response()->json($formatted);
}



public function revokeMessage($id)
{
    $message = Message::findOrFail($id);

    if ($message->sender_id !== Auth::id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // Nếu có file thì xóa luôn
    if ($message->file_path && file_exists(public_path($message->file_path))) {
        unlink(public_path($message->file_path));
    }

    $message->is_revoked = true;
    $message->message = 'Tin nhắn đã bị thu hồi';
    $message->file_path = null;
    $message->file_type = null;
    $message->save();

    return response()->json(['success' => true]);
}



}