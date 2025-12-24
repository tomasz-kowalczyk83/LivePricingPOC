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