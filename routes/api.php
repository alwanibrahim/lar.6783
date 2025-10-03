<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\DistributionController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AffiliateController;
use App\Http\Controllers\Api\CategoryController;




Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public routes (no authentication required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/transaction/create', [TransactionController::class, 'create']);

// Authenticated routes (accessible by both user and admin)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);

    // Deposit routes (user can create and view their own deposits)
    Route::apiResource('deposits', DepositController::class)->except(['update', 'destroy']);
    
    // Distribution routes (user can create and view their own distributions)
    Route::apiResource('distributions', DistributionController::class)->except(['update', 'destroy']);

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications', [NotificationController::class, 'store']);
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);

    // Affiliate routes
    Route::get('/affiliate/commissions', [AffiliateController::class, 'commissions']);
    Route::get('/affiliate/referrals', [AffiliateController::class, 'referrals']);
    Route::get('/affiliate/stats', [AffiliateController::class, 'stats']);

    // Category routes (read only for users)
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
});

// Admin only routes
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Product management (admin only)
    Route::apiResource('products', ProductController::class);
    Route::post('/products/{product}/accounts', [ProductController::class, 'addAccount']);
    Route::post('/products/{product}/invites', [ProductController::class, 'addInvite']);

    // Deposit status management (admin only)
    Route::patch('/deposits/{deposit}/status', [DepositController::class, 'updateStatus']);

    // Distribution status management (admin only)
    Route::patch('/distributions/{distribution}/status', [DistributionController::class, 'updateStatus']);

    // Category management (admin only)
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
});
