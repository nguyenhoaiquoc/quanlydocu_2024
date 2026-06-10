<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('message_notifications', function (Blueprint $table) {
            $table->id();

            // Người nhận thông báo (receiver của tin nhắn)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Người gửi (sender)
            $table->foreignId('actor_id')->constrained('users')->onDelete('cascade');

            // Tin nhắn cụ thể (dùng để lấy snippet)
            $table->foreignId('message_id')->constrained('messages')->onDelete('cascade');

            // Tin nhắn có thể gắn với sản phẩm (nhiều cuộc chat theo sản phẩm)
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');

            // Để mở rộng về sau (ví dụ system_message,...)
            $table->string('type')->default('message');

            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_notifications');
    }
};
