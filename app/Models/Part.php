<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Part extends Model
{
    protected $fillable = [
        'supplier_id',
        'sku',
        'name',
        'description',
        'price',
        'stock_quantity',
        'fits_vehicle',
    ];

    protected $casts = [
        'fits_vehicle' => 'array',
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function matchesVehicle(array $vehicleInfo): bool
    {
        if (empty($this->fits_vehicle)) {
            return false;
        }

        foreach (['year', 'make', 'model'] as $key) {
            if (isset($vehicleInfo[$key]) && isset($this->fits_vehicle[$key])) {
                if ($this->fits_vehicle[$key] !== $vehicleInfo[$key]) {
                    return false;
                }
            }
        }

        return true;
    }
}
