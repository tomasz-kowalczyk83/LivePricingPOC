<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('traders', function (Blueprint $table) {
            $table->unsignedInteger('default_request_expiry_minutes')->nullable()->after('is_active');
            $table->unsignedInteger('default_response_expiry_minutes')->nullable()->after('default_request_expiry_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('traders', function (Blueprint $table) {
            $table->dropColumn(['default_request_expiry_minutes', 'default_response_expiry_minutes']);
        });
    }
};
