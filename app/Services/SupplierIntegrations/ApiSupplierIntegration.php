<?php

namespace App\Services\SupplierIntegrations;

use App\Models\QuoteRequest;
use App\Models\Supplier;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiSupplierIntegration implements SupplierIntegrationInterface
{
    public function requestQuote(Supplier $supplier, QuoteRequest $quoteRequest): ?array
    {
        if (!$supplier->api_endpoint) {
            return null;
        }

        $startTime = microtime(true);

        try {
            $response = Http::timeout(5)->post($supplier->api_endpoint, [
                'part_description' => $quoteRequest->part_description,
                'vehicle_info' => $quoteRequest->vehicle_info,
                'buyer_email' => $quoteRequest->buyer_email,
            ]);

            if (!$response->successful()) {
                Log::warning("API request failed for supplier {$supplier->id}", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            $responseTime = (microtime(true) - $startTime) * 1000;

            return [
                'quoted_price' => $data['price'] ?? null,
                'stock_available' => $data['stock'] ?? null,
                'response_time_seconds' => round($responseTime / 1000, 2),
                'notes' => $data['notes'] ?? null,
                'status' => 'received',
            ];
        } catch (\Exception $e) {
            Log::error("API integration error for supplier {$supplier->id}", [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
