<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outbox_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type', 100);
            $table->morphs('eventable');
            $table->json('payload');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['processed_at', 'created_at'], 'outbox_events_pending_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbox_events');
    }
};
