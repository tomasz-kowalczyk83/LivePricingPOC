<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasExpiry
{
    public function isExpired(): bool
    {
        if ($this->expires_at === null) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>=', now());
        });
    }

    public function scopeExpiringWithin(Builder $query, int $minutes): Builder
    {
        $threshold = now()->addMinutes($minutes);

        return $query->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<=', $threshold);
    }

    public function getTimeUntilExpiryAttribute(): ?int
    {
        if ($this->expires_at === null) {
            return null;
        }

        return (int) max(0, now()->diffInSeconds($this->expires_at, false));
    }
}
