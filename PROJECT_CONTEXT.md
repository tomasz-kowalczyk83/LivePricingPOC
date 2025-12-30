# Parts Sync Platform - POC

## Stack
Laravel 12, Filament 4, Reverb, Tailwind 4, MySQL, Redis Queue, Horizon, Pulse

## Core Concept
B2B **automotive parts marketplace** that eliminates manual quoting by aggregating real-time pricing from multiple suppliers. Buyers (traders) can request quotes for specific car parts, and suppliers respond with pricing and availability in real-time.

**Domain: Automotive Aftermarket Parts**
- Year/Make/Model fitment (e.g., "Brake pads for 2019 Honda Civic")
- Part categories: brakes, filters, batteries, suspension, etc.
- SKU structure: `BRK-HON-CIV-2019-F` (category-make-model-year-position)

## Supplier Integration Types
- **Database**: Instant query of uploaded catalog
- **API**: External system integration (future: TecDoc, PartsLogic)
- **Manual**: Human notification + response via Filament panel

## Quote System Architecture (Phased)

### Phase 1: Single Product per Request (Current)
Simple 1:1 relationship - one product per quote request.

```
QuoteRequest (trader_id, status, is_anonymous, expires_at, expected_responses_count, responses_count)
    └── QuoteResponse[] (trader_id, quoted_price, stock_available, status, expires_at)
```

### Phase 2: Multi-Item Requests (Future)
Support multiple products per request with partial supplier responses.

```
QuoteRequest (trader_id, status)
    └── QuoteRequestItem[] (product_sku, quantity)
          └── QuoteResponseItem[] (supplier_id, quoted_price, stock)
```

Suppliers can respond with partial quotes (only items they stock).

## Key Models

### Multitenancy (Filament)
- Company (tenant) - traders belong to companies
- Trader (user) - type: buyer | supplier
- Trader has configurable default expiry settings

### Vehicles & Parts
- Vehicle (year, make, model)
- Part (sku, name, category, description)
- PartVehicleFitment (part_id, vehicle_id) - compatibility pivot

### Quotes
- QuoteRequest (trader_id, status, is_anonymous, expires_at, expected_responses_count, responses_count)
- QuoteResponse (quote_request_id, trader_id, quoted_price, stock_available, status, expires_at, response_time_seconds)

---

## Business Rules

### Quote Request Lifecycle

**Statuses:** `pending` → `processing` → `completed` | `cancelled` | `expired`

| Status | Description | Triggered By |
|--------|-------------|--------------|
| `pending` | Request created, not yet sent to suppliers | System (on create) |
| `processing` | Sent to suppliers, awaiting responses | System (ProcessQuoteRequest job) |
| `completed` | Buyer accepted a quote | Buyer action |
| `cancelled` | Buyer cancelled the request | Buyer action |
| `expired` | No decision made within time limit | System (scheduled job) |

**Rules:**
- A quote request MUST have at least one product SKU and quantity
- A quote request MUST be associated with a buyer (trader)
- When a request enters `processing`, response records are created for all active suppliers
- A request can only be `completed` if at least one response is `accepted`
- A request can be `cancelled` only while in `pending` or `processing` status

### Quote Response Lifecycle

**Statuses:** `pending` → `submitted` | `declined` | `timeout` → `accepted` | `rejected`

| Status | Description | Triggered By |
|--------|-------------|--------------|
| `pending` | Awaiting supplier response | System (when request broadcast) |
| `submitted` | Supplier provided price and availability | Supplier action |
| `declined` | Supplier explicitly won't quote | Supplier action |
| `timeout` | Supplier didn't respond in time | System (scheduled job) |
| `accepted` | Buyer chose this quote | Buyer action |
| `rejected` | Buyer chose a different supplier | System (when another accepted) |

**Rules:**
- A supplier can only submit ONE response per quote request
- `quoted_price` and `stock_available` are REQUIRED when status is `submitted`
- `response_time_seconds` is calculated automatically (request.created_at → response.updated_at)
- When a response is `accepted`, all other `submitted` responses become `rejected`
- A response can only be `accepted` if its status is `submitted`
- Only responses with status `submitted` are shown in price comparison

### Price Comparison

**Rules:**
- Best price = lowest `quoted_price` among `submitted` responses
- Responses are ranked by price ascending
- Stock availability is displayed but does not affect ranking
- Response time is tracked for supplier performance metrics

### Supplier Visibility

**Rules:**
- Suppliers only see quote requests in `processing` status
- Suppliers do NOT see requests they have already responded to
- Suppliers cannot see other suppliers' responses or pricing
- Buyers can see all responses for their own requests
- Anonymous requests hide buyer identity until quote is accepted

### Quote Expiry

**Hierarchical Configuration:**
1. Per-item expiry (set when creating request/response)
2. Trader default expiry (configurable in Settings page)
3. Global config default (config/quotes.php)

**Rules:**
- Both QuoteRequest and QuoteResponse have `expires_at` timestamps
- Expired quotes/requests cannot be accepted
- Scheduled commands auto-expire stale items
- Expiry can be set via dropdown presets or custom calendar picker

### Action Classes Pattern

Business logic is encapsulated in action classes:
- `App\Actions\QuoteResponse\SubmitQuoteAction` - Submit quote with event dispatch
- `App\Actions\QuoteResponse\DeclineQuoteAction` - Decline quote with event dispatch
- `App\Actions\QuoteResponse\AcceptQuoteAction` - Buyer accepts a quote

### Events

- `QuoteResponseSubmitted` - Fired when supplier submits a quote
- `QuoteResponseDeclined` - Fired when supplier declines a quote
- `QuoteRequestExpired` - Fired when a request expires
- `QuoteResponseTimedOut` - Fired when a response times out

---

## Features
1. Multi-type supplier integration (3 patterns)
2. Real-time quote aggregation (Reverb WebSockets)
3. Analytics dashboards (supplier performance, buyer intelligence)
4. Smart price comparison
5. Quote expiry with hierarchical configuration
6. Anonymous buyer requests
7. Trader settings for default expiry values

## Filament Panels

### Vendor Panel (Traders)
- **Buyers** see: My Quote Requests, Settings
- **Suppliers** see: My Quote Responses, Settings
- Navigation filtered by trader type

### Admin Panel
- Full access to all resources
- User management

## Integration Service Pattern
SupplierIntegrationInterface → DatabaseSupplierIntegration, ApiSupplierIntegration, ManualSupplierIntegration

## Job Flow
QuoteRequest → ProcessQuoteRequest → ProcessSupplierQuote (per supplier) → Response saved → Broadcast

**Safety Check:** ProcessQuoteRequest excludes suppliers from the same company as the buyer.

## Design
Professional B2B (Stripe/Linear style), card-based, blue/gray palette, mobile-first

## Success Criteria
- All 3 integration types working
- Real-time updates (<2s for auto suppliers)
- Analytics showing competitive metrics
- Professional UI

## MCP Instructions

### Semantic Versioning
This project follows [Semantic Versioning 2.0.0](https://semver.org/):
- **MAJOR** version (X.0.0): Incompatible API changes
- **MINOR** version (0.X.0): Backwards-compatible functionality additions
- **PATCH** version (0.0.X): Backwards-compatible bug fixes

Current version is tracked in the `VERSION` file.

### Conventional Commits
All commits MUST follow [Conventional Commits](https://www.conventionalcommits.org/) specification:

**Format:** `<type>(<scope>): <subject>`

**Types:**
- `feat`: New feature (triggers MINOR version bump)
- `fix`: Bug fix (triggers PATCH version bump)
- `docs`: Documentation changes
- `style`: Code style changes (formatting, missing semicolons, etc.)
- `refactor`: Code refactoring without feature changes
- `perf`: Performance improvements
- `test`: Adding or updating tests
- `chore`: Maintenance tasks, dependency updates
- `ci`: CI/CD configuration changes
- `build`: Build system or external dependency changes
- `revert`: Reverting a previous commit

**Breaking Changes:**
- Add `BREAKING CHANGE:` in commit footer (triggers MAJOR version bump)
- Or append `!` after type: `feat!: breaking change`

**Examples:**
```bash
feat(suppliers): add webhook integration type
fix(quotes): correct price comparison logic
docs(readme): update installation instructions
chore(deps): upgrade Laravel to 12.1
feat!: redesign supplier integration interface
```

**Scopes (optional but recommended):**
- `suppliers`: Supplier-related features
- `quotes`: Quote request/response features
- `dashboard`: Analytics dashboard
- `ui`: User interface components
- `api`: API integrations
- `jobs`: Queue jobs
- `websocket`: Real-time features
- `database`: Database migrations/schema
- `auth`: Authentication/authorization

### Version Bumping Process
1. Update `VERSION` file with new version
2. Update `CHANGELOG.md` with changes under new version heading
3. Commit with type: `chore(release): bump version to X.Y.Z`
4. Create git tag: `git tag -a vX.Y.Z -m "Release vX.Y.Z"`
5. Push with tags: `git push --follow-tags`

### CHANGELOG.md Format
Follow [Keep a Changelog](https://keepachangelog.com/) format:
- Group changes under: Added, Changed, Deprecated, Removed, Fixed, Security
- Keep unreleased changes at top
- Link versions to release tags

### AI Agent Guidelines
When working with MCP tools:
1. Always use conventional commit format
2. Keep commits atomic and focused
3. Update CHANGELOG.md for user-facing changes
4. Bump VERSION file when releasing
5. Reference issue/ticket numbers in commit body
6. Use imperative mood in subject line
7. Limit subject line to 50 characters
8. Wrap body at 72 characters