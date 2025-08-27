# API Quick Reference

## Setup Commands
```bash
# Install & setup
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

## Authentication Flow

### 1. Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "demo@local", "password": "demo1234"}'
```

### 2. Use Token
```bash
# Replace YOUR_TOKEN with the access_token from login response
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 3. Logout
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Demo Credentials
- **Email:** `demo@local`
- **Password:** `demo1234`

## Common Test Commands
```bash
# Run all tests
php artisan test

# Run auth tests only
php artisan test tests/Feature/AuthTest.php

# Reset database
php artisan migrate:fresh --seed
```

## Health Check
```bash
curl -X GET http://localhost:8000/api/health
```

## Error Status Codes
- `200` - Success
- `201` - Created
- `204` - No Content (logout)
- `401` - Unauthorized (invalid/missing token)
- `404` - Not Found
- `422` - Validation Error
