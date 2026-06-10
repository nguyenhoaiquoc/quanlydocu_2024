<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Giả sử bảng products tồn tại
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Đảm bảo unique phòng chat cho mỗi cặp buyer-seller-product
            $table->unique(['product_id', 'buyer_id', 'seller_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};