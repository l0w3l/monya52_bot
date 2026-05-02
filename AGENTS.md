# Agent Instructions: Monya52 (Laravel + Telegram)

This repository is a Laravel 12 application focused on Telegram bot functionality using `lowel/telepath`.

## 🛠 Critical Commands

- **Test**: `php artisan test` or `composer test` (uses Pest PHP).
- **Lint**: `vendor/bin/pint` (Laravel Pint).
- **Static Analysis**: `vendor/bin/phpstan` (Larastan, Level 5).
- **Dev Stack**: `composer dev` (runs server, queue, logs, and vite concurrently).
- **Service Generation**: Use `php artisan make:service {Name}` (provided by `lowel/laravel-service-maker`).

## 🏗 Architecture & Patterns

### Interface-Service-Factory Pattern
All business logic MUST follow this pattern in `app/Services/`:
1. **Interface**: Defines the contract.
2. **Service**: Implements the interface.
3. **Factory**: Handles instantiation (often injecting dependencies).

Example structure: `app/Services/QuoteApi/QuoteApiService[Interface|Factory].php`.

### Telegram Integration (`lowel/telepath`)
- **Routes**: Defined in `routes/telegram.php`.
- **Logic**: Organized in `app/Telegram/`:
    - `Handlers/`: Request processing.
    - `Middlewares/`: Request filtering.
    - `Keyboards/`: UI components.
- **Services**: Telegram-specific business logic resides in `app/Services/Telegram/`.

## 📝 Conventions

- **Enums**: Used for fixed sets of values (e.g., `app/Enums/`).
- **Models**: Standard Eloquent models in `app/Models/`.
- **Database**: SQLite is the default for local development (`database/database.sqlite`).
- **Type Safety**: Strict typing is preferred. PHPStan is enforced at level 5.

## ⚠️ Gotchas

- **Service Discovery**: Laravel's package discovery is active.
- **IDE Helper**: Run `php artisan ide-helper:generate` and `php artisan ide-helper:models -M` if models or classes are not recognized by your environment.
- **Telegram Webhooks**: When working on Telegram features, ensure the bot token is set in `.env`.
