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
        Schema::create('product_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')        // người SẼ THẤY thông báo
                ->constrained('users')->onDelete('cascade');
            $table->foreignId('actor_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->foreignId('product_id')->nullable()
                ->constrained('products')->nullOnDelete();

            $table->string('type', 50)->index();


            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_notifications');
    }
};
