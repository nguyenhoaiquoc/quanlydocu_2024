<div class="comment-item" data-id="{{ $comment->id }}">
    <div class="comment-author">
        <strong>{{ $comment->user->name }}</strong> –
        {!! str_repeat('★', $comment->rating ?? 0) . str_repeat('☆', 5 - ($comment->rating ?? 0)) !!}
    </div>
    <div class="comment-text">{{ $comment->content }}</div>
    <button class="reply-btn" data-id="{{ $comment->id }}">Trả lời</button>

    {{-- Form trả lời --}}
    <form class="reply-form" data-parent="{{ $comment->id }}" style="display:none">
        @csrf
        <textarea name="content" placeholder="Nhập câu trả lời..." rows="2" required></textarea>
        <button type="submit">Gửi</button>
    </form>

    {{-- Các phản hồi (comment con) --}}
    <div class="replies">
        @foreach ($comment->replies as $reply)
            @include('partials.comment-item', ['comment' => $reply])
        @endforeach
    </div>
</div>
