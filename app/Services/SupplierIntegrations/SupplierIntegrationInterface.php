<?php

namespace App\Services\SupplierIntegrations;

use App\Models\QuoteRequest;
use App\Models\Supplier;

interface SupplierIntegrationInterface
{
    public function requestQuote(Supplier $supplier, QuoteRequest $quoteRequest): ?array;
}
