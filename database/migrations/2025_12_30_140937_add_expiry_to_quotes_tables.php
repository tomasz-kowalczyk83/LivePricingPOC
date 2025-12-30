<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quote_requests', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('responses_count');
            $table->index(['status', 'expires_at'], 'quote_requests_status_expires_at_index');
        });

        Schema::table('quote_responses', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('status');
            $table->index(['status', 'expires_at'], 'quote_responses_status_expires_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('quote_requests', function (Blueprint $table) {
            $table->dropIndex('quote_requests_status_expires_at_index');
            $table->dropColumn('expires_at');
        });

        Schema::table('quote_responses', function (Blueprint $table) {
            $table->dropIndex('quote_responses_status_expires_at_index');
            $table->dropColumn('expires_at');
        });
    }
};
