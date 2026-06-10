<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');       // người nhận
            $table->unsignedBigInteger('actor_id')->nullable(); // người gây ra sự kiện
            $table->string('category', 32);              // message | comment | follow | product
            $table->string('type', 64)->nullable();      // ví dụ: reply_comment, product_approved...
            $table->unsignedBigInteger('related_id')->nullable();
            $table->string('related_type', 64)->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('notifications');
    }
};
