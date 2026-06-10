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
        Schema::create('follow_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')   // người sẽ THẤY thông báo trong panel
                  ->constrained('users')->onDelete('cascade');
            $table->foreignId('actor_id')  // người gây ra hành động (theo dõi bạn / bạn theo dõi ai)
                  ->constrained('users')->onDelete('cascade');
            $table->enum('type', ['in', 'out']); // in = ai đó theo dõi bạn, out = bạn theo dõi ai
            $table->boolean('is_read')->default(false);
            $table->timestamps(); // created_at dùng làm thời gian hiển thị
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_notifications');
    }
};
