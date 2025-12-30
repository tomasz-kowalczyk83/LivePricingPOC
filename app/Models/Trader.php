<?php

namespace App\Models;

use App\Enums\TraderTypeEnum;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trader extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'name',
        'type',
        'is_active',
        'default_request_expiry_minutes',
        'default_response_expiry_minutes',
    ];

    protected $casts = [
        'type' => TraderTypeEnum::class,
        'is_active' => 'boolean',
        'default_request_expiry_minutes' => 'integer',
        'default_response_expiry_minutes' => 'integer',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function isBuyer(): bool
    {
        return $this->type === TraderTypeEnum::BUYER;
    }

    public function isSupplier(): bool
    {
        return $this->type === TraderTypeEnum::SUPPLIER;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function quoteRequests(): HasMany
    {
        return $this->hasMany(QuoteRequest::class);
    }

    public function quoteResponses(): HasMany
    {
        return $this->hasMany(QuoteResponse::class);
    }
}
