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
    Schema::table('messages', function (Blueprint $table) {
        $table->boolean('is_revoked')->default(false)->after('message');
        $table->timestamp('revoked_at')->nullable()->after('is_revoked');
    });
}

public function down(): void
{
    Schema::table('messages', function (Blueprint $table) {
        $table->dropColumn(['is_revoked', 'revoked_at']);
    });
}

};
