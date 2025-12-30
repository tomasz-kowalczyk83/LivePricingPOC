# Parts Sync Platform - POC

A B2B parts sourcing platform that eliminates manual quoting by aggregating real-time pricing from suppliers using multiple integration types.

## 🚀 Features

### Core Functionality
- **Multi-Type Supplier Integration**: Supports 3 integration patterns
  - **Database**: Instant query of uploaded catalogs
  - **API**: External system integration
  - **Manual**: Human notification + response

- **Real-Time Quote Aggregation**: Live updates via Reverb WebSockets
- **Smart Price Comparison**: Automatically identifies best quotes
- **Quote Expiry System**: Hierarchical expiry configuration (global → trader → per-item)
- **Anonymous Buyer Requests**: Hide buyer identity until quote is accepted
- **Trader Settings**: Configurable default expiry values per trader
- **Analytics Dashboard**: Supplier performance metrics and buyer intelligence
- **Professional B2B UI**: Clean, card-based design with Tailwind CSS

### Success Criteria ✅
- ✅ All 3 integration types working
- ✅ Real-time updates (<2s for auto suppliers)
- ✅ Analytics showing competitive metrics
- ✅ Professional UI with mobile-first design

## 🛠 Tech Stack

- **Laravel 12**: Backend framework
- **Filament 4**: Admin panels (Vendor, Admin)
- **Reverb**: WebSocket server for real-time updates
- **Tailwind CSS 4**: UI styling
- **MySQL/SQLite**: Database
- **Redis Queue**: Job processing
- **Horizon**: Queue monitoring
- **Pulse**: Application performance monitoring

## 📁 Project Structure

```
app/
├── Actions/
│   └── QuoteResponse/
│       ├── SubmitQuoteAction.php           # Submit quote with event dispatch
│       ├── DeclineQuoteAction.php          # Decline quote with event dispatch
│       └── AcceptQuoteAction.php           # Buyer accepts a quote
├── Concerns/
│   └── HasExpiry.php                       # Shared trait for expiry logic
├── Console/Commands/
│   ├── ExpireQuoteRequestsCommand.php      # Expire stale requests
│   ├── TimeoutQuoteResponsesCommand.php    # Timeout stale responses
│   └── ProcessOutboxCommand.php            # Process outbox events
├── Enums/
│   ├── QuoteRequestStatusEnum.php          # Request statuses
│   ├── QuoteResponseStatusEnum.php         # Response statuses
│   └── TraderTypeEnum.php                  # Buyer/Supplier types
├── Events/
│   ├── QuoteResponseSubmitted.php          # Fired when supplier submits
│   ├── QuoteResponseDeclined.php           # Fired when supplier declines
│   ├── QuoteRequestExpired.php             # Fired when request expires
│   └── QuoteResponseTimedOut.php           # Fired when response times out
├── Filament/
│   └── Vendor/
│       ├── Pages/
│       │   └── TraderSettings.php          # Settings page for traders
│       └── Resources/
│           ├── QuoteRequests/              # Buyer's quote requests
│           └── QuoteResponses/             # Supplier's quote responses
├── Jobs/
│   ├── ProcessQuoteRequest.php             # Dispatches quotes to suppliers
│   ├── ProcessSupplierQuote.php            # Processes individual quote
│   └── ProcessOutboxEvents.php             # Process outbox events
├── Models/
│   ├── Company.php                         # Tenant model
│   ├── Trader.php                          # Buyer/Supplier model
│   ├── QuoteRequest.php                    # Quote request model
│   ├── QuoteResponse.php                   # Supplier response model
│   └── OutboxEvent.php                     # Transactional outbox
└── Services/
    └── OutboxEventService.php              # Record outbox events
```

## 🗄 Database Schema

### Companies (Tenants)
- Multitenancy via Filament - traders belong to companies

### Traders (Users)
- `type`: buyer | supplier
- `is_active`: Boolean
- `default_request_expiry_minutes`: Default expiry for quote requests
- `default_response_expiry_minutes`: Default expiry for quote responses
- Belongs to a Company

### Vehicles
- `year`, `make`, `model`: Vehicle identification
- Used for part fitment matching

### Parts
- `sku`: Stock keeping unit (e.g., `BRK-HON-CIV-2019-F`)
- `name`, `category`, `description`
- Linked to vehicles via `PartVehicleFitment` pivot

### QuoteRequests
- `trader_id`: Buyer who created the request
- `is_anonymous`: Hide buyer identity from suppliers
- `expires_at`: When the request expires
- `expected_responses_count`: Number of suppliers contacted
- `responses_count`: Number of responses received
- `status`: See workflow below

### QuoteResponses
- `quote_request_id`: Foreign key to quote requests
- `trader_id`: Supplier responding
- `quoted_price`, `stock_available`: The quote
- `expires_at`: When the quote expires
- `response_time_seconds`: How fast they responded
- `status`: See workflow below

## 📋 Quote Workflow

### Quote Request Status Flow
```
pending → processing → completed
                    ↘ cancelled
                    ↘ expired
```

| Status | Description |
|--------|-------------|
| `pending` | Created, not yet sent to suppliers |
| `processing` | Sent to suppliers, awaiting responses |
| `completed` | Buyer accepted a quote |
| `cancelled` | Buyer cancelled the request |
| `expired` | No decision made in time |

### Quote Response Status Flow
```
pending → submitted → accepted
       ↘ declined   ↘ rejected
       ↘ timeout
```

| Status | Description |
|--------|-------------|
| `pending` | Awaiting supplier response |
| `submitted` | Supplier provided price/availability |
| `declined` | Supplier won't quote |
| `timeout` | No response in time |
| `accepted` | Buyer chose this quote |
| `rejected` | Buyer chose different supplier |

## 🎯 How It Works

1. **Quote Request**: Buyer submits a quote request form
2. **Job Dispatch**: `ProcessQuoteRequest` job dispatches to all active suppliers
3. **Integration Execution**: Each supplier is queried via their integration type:
   - **Database**: Instant local query
   - **API**: HTTP request to external endpoint
   - **Manual**: Notification sent to supplier contact
4. **Real-Time Broadcast**: Each response triggers a WebSocket broadcast
5. **Live Updates**: Livewire component receives updates and refreshes UI
6. **Best Quote**: System automatically identifies and highlights best price

## 📊 Integration Types Explained

### Database Integration
```php
// Instantly queries local part catalog
$parts = Part::where('supplier_id', $supplier->id)
    ->where('stock_quantity', '>', 0)
    ->get();
```

### API Integration
```php
// Makes HTTP request to external API
$response = Http::post($supplier->api_endpoint, [
    'part_description' => $quoteRequest->part_description,
    'vehicle_info' => $quoteRequest->vehicle_info,
]);
```

### Manual Integration
```php
// Sends notification to supplier (email/SMS)
// Waits for human response via admin panel
```

## 🚦 Getting Started

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL or SQLite
- Redis (for queues)

### Installation

1. **Install Dependencies**
```bash
composer install
npm install
```

2. **Configure Environment**
```bash
cp .env.example .env
php artisan key:generate
```

Update `.env`:
```env
DB_CONNECTION=mysql  # or sqlite
DB_DATABASE=parts_sync_platform

QUEUE_CONNECTION=redis
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
```

3. **Run Migrations & Seed Data**
```bash
php artisan migrate
php artisan db:seed
```

4. **Build Assets**
```bash
npm run build
# or for development
npm run dev
```

5. **Start Services**

In separate terminals:

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Queue worker
php artisan queue:work

# Terminal 3: Reverb WebSocket server
php artisan reverb:start
```

6. **Access Application**
Open browser to `http://localhost:8000`

## 📝 Testing the POC

### Test Database Integration
1. Navigate to "New Quote Request"
2. Fill in form:
   - Part: "Front Brake Pads"
   - Vehicle: 2020 Toyota Camry
3. Submit and watch real-time quotes appear
4. You should see instant responses from database suppliers

### Test Manual Integration
1. Submit a quote request
2. Check logs: `tail -f storage/logs/laravel.log`
3. You'll see notifications being logged for manual suppliers

### Test API Integration
Note: API integration requires a real API endpoint. In the POC, it will log attempts but won't receive responses unless you set up a test API.

## 🎨 UI Features

### Dashboard
- Total requests & responses
- Average responses per request
- Active suppliers count
- Supplier performance table with response times & pricing
- Recent quote requests list

### Quote Request Form
- Buyer information
- Part description
- Optional vehicle information (year, make, model)
- Clean validation with inline errors

### Quote Response View
- Real-time response updates (no page refresh needed!)
- Best quote highlighted in green
- Supplier badges showing integration type
- Response time tracking
- Stock availability display

## 🔧 Customization

### Adding New Supplier Integration Type
1. Create new class implementing `SupplierIntegrationInterface`
2. Add case to `SupplierIntegrationFactory::make()`
3. Update migration for new integration type enum value

### Modifying Real-Time Behavior
Edit `QuoteResponseReceived` event:
- Change broadcast channel
- Customize data sent to frontend
- Add additional listeners

## 🐛 Troubleshooting

### WebSocket Not Connecting
- Ensure Reverb is running: `php artisan reverb:start`
- Check `.env` has correct `REVERB_*` settings
- Verify `BROADCAST_CONNECTION=reverb`

### Queue Jobs Not Processing
- Start queue worker: `php artisan queue:work`
- Check Redis is running: `redis-cli ping`
- Verify `QUEUE_CONNECTION=redis`

### No Real-Time Updates
- Check browser console for WebSocket errors
- Ensure Livewire is properly loaded
- Verify event is being dispatched in logs

## 📈 Performance Notes

- Database integration: <100ms response time
- Real-time broadcast: <200ms latency
- Target: <2s for all automatic suppliers
- Manual suppliers: Response time depends on human availability

## 🎓 Key Learnings from POC

1. **Interface-Based Design**: Clean separation of integration types via interface
2. **Event-Driven Architecture**: WebSocket broadcasts for real-time UX
3. **Queue Processing**: Async job handling for scalability
4. **Livewire Real-Time**: `#[On('echo:...')]` attribute for WebSocket listening
5. **Factory Pattern**: Easy to extend with new integration types

## 📄 License

This is a proof-of-concept application.

## 🤝 Contributing

This is a POC for demonstration purposes.

## 📦 Versioning

This project follows [Semantic Versioning 2.0.0](https://semver.org/):
- **MAJOR.MINOR.PATCH** (e.g., 1.0.0)
- Current version: See `VERSION` file
- Release history: See `CHANGELOG.md`

### Version History
- **v1.0.0** (2025-12-18): Initial release with all core features

## 🤝 Contributing Guidelines

### Commit Message Format
This project uses [Conventional Commits](https://www.conventionalcommits.org/):

```bash
<type>(<scope>): <subject>

<body>

<footer>
```

**Types:** `feat`, `fix`, `docs`, `style`, `refactor`, `perf`, `test`, `chore`

**Examples:**
```bash
feat(suppliers): add webhook integration type
fix(quotes): correct price comparison logic  
docs(readme): update installation instructions
```

### Pull Request Process
1. Create feature branch from `main`
2. Use conventional commits for all changes
3. Update CHANGELOG.md for user-facing changes
4. Ensure all tests pass
5. Submit PR with clear description

See `PROJECT_CONTEXT.md` for detailed MCP instructions.
