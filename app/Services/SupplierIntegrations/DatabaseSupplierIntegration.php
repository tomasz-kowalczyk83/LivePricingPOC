<?php

namespace App\Services\SupplierIntegrations;

use App\Models\QuoteRequest;
use App\Models\Supplier;
use App\Models\Part;

class DatabaseSupplierIntegration implements SupplierIntegrationInterface
{
    public function requestQuote(Supplier $supplier, QuoteRequest $quoteRequest): ?array
    {
        $startTime = microtime(true);

        $parts = Part::where('supplier_id', $supplier->id)
            ->where('stock_quantity', '>', 0)
            ->get();

        $matchingPart = $parts->first(function ($part) use ($quoteRequest) {
            if (empty($quoteRequest->vehicle_info)) {
                return true;
            }
            return $part->matchesVehicle($quoteRequest->vehicle_info);
        });

        if (!$matchingPart) {
            return null;
        }

        $responseTime = (microtime(true) - $startTime) * 1000;

        return [
            'quoted_price' => $matchingPart->price,
            'stock_available' => $matchingPart->stock_quantity,
            'response_time_seconds' => round($responseTime / 1000, 2),
            'notes' => "Part: {$matchingPart->name} (SKU: {$matchingPart->sku})",
            'status' => 'received',
        ];
    }
}
