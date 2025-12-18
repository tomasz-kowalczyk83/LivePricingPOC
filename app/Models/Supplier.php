<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'integration_type',
        'api_endpoint',
        'notification_channels',
        'is_active',
    ];

    protected $casts = [
        'notification_channels' => 'array',
        'is_active' => 'boolean',
    ];

    public function parts(): HasMany
    {
        return $this->hasMany(Part::class);
    }

    public function quoteResponses(): HasMany
    {
        return $this->hasMany(QuoteResponse::class);
    }

    public function isDatabase(): bool
    {
        return $this->integration_type === 'database';
    }

    public function isApi(): bool
    {
        return $this->integration_type === 'api';
    }

    public function isManual(): bool
    {
        return $this->integration_type === 'manual';
    }
}
