# Parts Sync Platform - POC

## Stack
Laravel 12, Livewire 3, Reverb, Tailwind, MySQL, Redis Queue

## Core Concept
B2B parts sourcing platform that eliminates manual quoting by aggregating real-time pricing from suppliers with 3 integration types:
- Database (instant query of uploaded catalog)
- API (external system integration)  
- Manual (human notification + response)

## Key Models
- Supplier (integration_type, api_endpoint, notification_channels)
- Part (supplier_id, sku, price, stock_quantity, fits_vehicle JSON)
- QuoteRequest (buyer_id, part_description, vehicle_info, status)
- QuoteResponse (quote_request_id, supplier_id, quoted_price, response_time_seconds)

## Features
1. Multi-type supplier integration (3 patterns)
2. Real-time quote aggregation (Reverb WebSockets)
3. Analytics dashboards (supplier performance, buyer intelligence)
4. Smart price comparison

## Integration Service Pattern
SupplierIntegrationInterface → DatabaseSupplierIntegration, ApiSupplierIntegration, ManualSupplierIntegration

## Job Flow
QuoteRequest → ProcessQuoteRequest → ProcessSupplierQuote (per supplier) → Response saved → Broadcast

## Design
Professional B2B (Stripe/Linear style), card-based, blue/gray palette, mobile-first

## Success Criteria
- All 3 integration types working
- Real-time updates (<2s for auto suppliers)
- Analytics showing competitive metrics
- Professional UI
```