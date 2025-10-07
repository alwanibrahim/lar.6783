# Role-Based Authentication System

## Overview
Sistem ini telah diimplementasikan dengan 2 role utama:
- **Admin**: Memiliki akses penuh ke semua fitur
- **User**: Memiliki akses terbatas sesuai kebutuhan

## Database Changes

### Migration: `add_role_to_users_table`
- Menambahkan field `role` dengan tipe `enum('admin', 'user')`
- Default value: `'user'`
- Field ini ditambahkan setelah `is_verified`

### Model User Updates
- Field `role` ditambahkan ke `$fillable`
- Method helper ditambahkan:
  - `isAdmin()`: Cek apakah user adalah admin
  - `isUser()`: Cek apakah user adalah user biasa
  - `hasRole(string $role)`: Cek role spesifik

## Middleware

### RoleMiddleware
- File: `app/Http/Middleware/RoleMiddleware.php`
- Alias: `role`
- Fungsi: Memvalidasi role user sebelum mengakses route
- Response:
  - 401: Jika user tidak terautentikasi
  - 403: Jika user tidak memiliki role yang diperlukan

## Route Access Control

### Public Routes (Tidak perlu authentication)
```
POST /api/register
POST /api/login
POST /api/transaction/create
```

### Authenticated Routes (User & Admin)
```
POST /api/logout
GET  /api/profile
POST /api/verify-otp
POST /api/resend-otp

# Deposits
GET    /api/deposits
POST   /api/deposits
GET    /api/deposits/{id}
DELETE /api/deposits/{id}

# Distributions
GET    /api/distributions
POST   /api/distributions
GET    /api/distributions/{id}
DELETE /api/distributions/{id}

# Notifications
GET    /api/notifications
POST   /api/notifications
PATCH  /api/notifications/{id}/read
PATCH  /api/notifications/mark-all-read
GET    /api/notifications/unread-count

# Affiliate
GET /api/affiliate/commissions
GET /api/affiliate/referrals
GET /api/affiliate/stats

# Categories (Read Only)
GET /api/categories
GET /api/categories/{id}
```

### Admin Only Routes
```
# Product Management
GET    /api/products
POST   /api/products
GET    /api/products/{id}
PUT    /api/products/{id}
DELETE /api/products/{id}
POST   /api/products/{product}/accounts
POST   /api/products/{product}/invites

# Deposit Status Management
PATCH /api/deposits/{deposit}/status

# Distribution Status Management
PATCH /api/distributions/{distribution}/status

# Category Management
POST   /api/categories
PUT    /api/categories/{category}
DELETE /api/categories/{category}
```

## Usage Examples

### Middleware Usage
```php
// Hanya admin yang bisa akses
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Admin routes
});

// User dan admin bisa akses
Route::middleware(['auth:sanctum', 'role:user,admin'])->group(function () {
    // Both roles routes
});
```

### Controller Usage
```php
// Cek role di controller
if ($request->user()->isAdmin()) {
    // Admin logic
}

if ($request->user()->hasRole('user')) {
    // User logic
}
```

## Test Users

### Admin User
- Email: `admin@example.com`
- Role: `admin`
- Verified: `true`

### Regular User
- Email: `user@example.com`
- Role: `user`
- Verified: `true`

## Setup Instructions

1. Jalankan migration:
```bash
php artisan migrate
```

2. Jalankan seeder:
```bash
php artisan db:seed
```

3. Test dengan login menggunakan email di atas

## Security Notes

- Semua route admin dilindungi dengan middleware `role:admin`
- User biasa tidak bisa mengakses route admin
- Middleware memvalidasi authentication terlebih dahulu
- Response error yang jelas untuk unauthorized access


