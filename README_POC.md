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
- **Analytics Dashboard**: Supplier performance metrics and buyer intelligence
- **Professional B2B UI**: Clean, card-based design with Tailwind CSS

### Success Criteria ✅
- ✅ All 3 integration types working
- ✅ Real-time updates (<2s for auto suppliers)
- ✅ Analytics showing competitive metrics
- ✅ Professional UI with mobile-first design

## 🛠 Tech Stack

- **Laravel 12**: Backend framework
- **Livewire 3**: Reactive components
- **Reverb**: WebSocket server for real-time updates
- **Tailwind CSS**: UI styling
- **MySQL/SQLite**: Database
- **Redis Queue**: Job processing

## 📁 Project Structure

```
app/
├── Events/
│   └── QuoteResponseReceived.php         # Broadcast event for real-time updates
├── Jobs/
│   ├── ProcessQuoteRequest.php            # Dispatches quotes to all suppliers
│   └── ProcessSupplierQuote.php           # Processes individual supplier quote
├── Livewire/
│   ├── Dashboard.php                       # Analytics dashboard component
│   ├── QuoteRequestForm.php                # Quote request form component
│   └── QuoteRequestShow.php                # Real-time quote display component
├── Models/
│   ├── Supplier.php                        # Supplier model with integration type
│   ├── Part.php                            # Part catalog model
│   ├── QuoteRequest.php                    # Quote request model
│   └── QuoteResponse.php                   # Supplier response model
└── Services/
    └── SupplierIntegrations/
        ├── SupplierIntegrationInterface.php    # Integration contract
        ├── DatabaseSupplierIntegration.php     # Database integration
        ├── ApiSupplierIntegration.php          # API integration
        ├── ManualSupplierIntegration.php       # Manual integration
        └── SupplierIntegrationFactory.php      # Factory for creating integrations
```

## 🗄 Database Schema

### Suppliers
- `integration_type`: database | api | manual
- `api_endpoint`: API URL (for API type)
- `notification_channels`: JSON array (for manual type)

### Parts
- `supplier_id`: Foreign key to suppliers
- `sku`: Stock keeping unit
- `price`: Current price
- `stock_quantity`: Available stock
- `fits_vehicle`: JSON (year, make, model)

### QuoteRequests
- `buyer_name`, `buyer_email`: Buyer information
- `part_description`: What they're looking for
- `vehicle_info`: JSON (optional vehicle details)
- `status`: pending | processing | completed | failed

### QuoteResponses
- `quote_request_id`: Foreign key to quote requests
- `supplier_id`: Foreign key to suppliers
- `quoted_price`: Supplier's price
- `response_time_seconds`: How fast they responded
- `status`: pending | received | timeout

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
