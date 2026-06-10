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
        Schema::create('product_reports', function (Blueprint $table) {
            $table->id();

            // Sản phẩm bị báo cáo
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            // Người gửi báo cáo
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');

            // Lý do ngắn (spam, sai giá, cấm, lừa đảo, khác...)
            $table->string('reason', 100);

            // Mô tả chi tiết
            $table->text('message')->nullable();

            // Trạng thái xử lý
            $table->string('status', 20)->default('pending'); // pending, reviewing, resolved, dismissed

            // Admin xử lý
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();

            $table->timestamps();

            $table->index(['product_id', 'status']);
            $table->index(['reporter_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reports');
    }
};
