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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description');

            $table->decimal('price', 15, 2)->nullable();
            $table->string('deal_type')->default('price');

            $table->string('image')->nullable();
            $table->unsignedBigInteger('user_id')->default(1);
            $table->string('payment_method')->nullable();

            $table->string('location_primary')->nullable();
            $table->string('location_secondary')->nullable();

            $table->string('condition')->nullable();
            $table->string('material')->nullable();
            $table->string('size')->nullable();
            $table->string('brand')->nullable();
            $table->string('used_duration')->nullable();
            $table->string('reason')->nullable();
            $table->string('new_category')->nullable();
            $table->boolean('is_approved')->default(false);
            // $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
