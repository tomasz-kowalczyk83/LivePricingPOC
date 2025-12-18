<?php

namespace App\Services\SupplierIntegrations;

use App\Models\Supplier;

class SupplierIntegrationFactory
{
    public function make(Supplier $supplier): SupplierIntegrationInterface
    {
        return match ($supplier->integration_type) {
            'database' => new DatabaseSupplierIntegration(),
            'api' => new ApiSupplierIntegration(),
            'manual' => new ManualSupplierIntegration(),
            default => throw new \InvalidArgumentException("Unknown integration type: {$supplier->integration_type}"),
        };
    }
}
