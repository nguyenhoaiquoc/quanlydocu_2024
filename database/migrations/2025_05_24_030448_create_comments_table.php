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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->unsignedTinyInteger('rating')->nullable(); // Chỉ dùng cho bình luận gốc
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Người viết comment

            // Người được đánh giá (dùng cho hồ sơ người bán)
            $table->foreignId('target_user_id')->nullable()->constrained('users')->onDelete('cascade');

            // Sản phẩm được đánh giá
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');

            // Bình luận cha (nếu là reply)
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
