# Translation Management Service (Laravel 11)

Translation Management API built on **Laravel 11** fast, secure, and scalable.  
Designed to handle large multilingual datasets (100k+ records) with low latency JSON export for web and mobile clients.

---

## Features

- Manage translations for multiple locales (`en`, `fr`, `es`, etc.)
- Tag translations for context (`web`, `mobile`, `api`)
- Search by key, locale, tag, or content
- Token-based authentication
- JSON export endpoint (<500ms)
- Scalable schema and optimized queries
- Follows PSR-12 + SOLID principles
- Unit, feature, and performance tests (>95% coverage)
- Docker support
- Swagger API docs

---

## Stack

| Component | Tech |
|------------|------|
| Framework | Laravel 11 (PHP 8.3) |
| Database | MySQL 8+ |
| Cache | Redis |
| Container | Docker / Docker Compose |
| Testing | PHPUnit |
| Docs | Swagger API |

---

## Installation

```bash
git clone https://github.com/<your-username>/translation-service.git
cd translation-service
composer install
cp .env.example .env
php artisan migrate
php artisan db:seed-translations
php artisan db:seed-translations 50000
http://localhost:8000/api/v1/
docker compose up -d
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed-translations
```
## ENV
```bash
APP_NAME=DigitalTolk
APP_URL=http://localhost
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=translation_service
DB_USERNAME=root
DB_PASSWORD=
API_TOKEN=2Cd8mFEdMV3Vy9JpswmMrOtmwOLx93jOHd2XZari3X9KAx7e8Ijk6h4ywQEMachu
```
---

# TESTING
## All tests
``` bash
php artisan test
```

## Coverage report
``` bash
php artisan test --coverage-html=coverage/
```

## Swagger Docs
``` bash
php artisan l5-swagger:generate
http://localhost:8000/api/documentation
```

# Author
## Bilal Ahmed
## Senior Full Stack Developer