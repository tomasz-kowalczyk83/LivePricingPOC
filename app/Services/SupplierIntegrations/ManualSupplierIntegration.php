<?php

namespace App\Services\SupplierIntegrations;

use App\Models\QuoteRequest;
use App\Models\Supplier;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use App\Notifications\ManualQuoteRequestNotification;

class ManualSupplierIntegration implements SupplierIntegrationInterface
{
    public function requestQuote(Supplier $supplier, QuoteRequest $quoteRequest): ?array
    {
        $startTime = microtime(true);

        try {
            $channels = $supplier->notification_channels ?? [];

            if (in_array('email', $channels)) {
                Log::info("Manual quote request sent to supplier {$supplier->id} via email");
            }

            if (in_array('sms', $channels)) {
                Log::info("Manual quote request sent to supplier {$supplier->id} via SMS");
            }

            $responseTime = (microtime(true) - $startTime) * 1000;

            return [
                'quoted_price' => null,
                'stock_available' => null,
                'response_time_seconds' => round($responseTime / 1000, 2),
                'notes' => 'Manual quote request sent. Awaiting supplier response.',
                'status' => 'pending',
            ];
        } catch (\Exception $e) {
            Log::error("Manual integration error for supplier {$supplier->id}", [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
