<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OutboxEvent extends Model
{
    protected $fillable = [
        'event_type',
        'eventable_type',
        'eventable_id',
        'payload',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];

    public function eventable(): MorphTo
    {
        return $this->morphTo();
    }

    public function markAsProcessed(): void
    {
        $this->update(['processed_at' => now()]);
    }

    public function isPending(): bool
    {
        return $this->processed_at === null;
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('processed_at');
    }

    public function scopeProcessed(Builder $query): Builder
    {
        return $query->whereNotNull('processed_at');
    }
}
