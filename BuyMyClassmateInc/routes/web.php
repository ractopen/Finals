<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StoreController;

// Public Routes
Route::get('/', [StoreController::class, 'index'])->name('home');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// User Routes (Protected)
Route::middleware('auth')->group(function () {
    Route::post('/cart/add/{id}', [StoreController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart', [StoreController::class, 'viewCart'])->name('cart.view');
    Route::patch('/cart/{id}', [StoreController::class, 'updateCartItem'])->name('cart.update');
    Route::delete('/cart/{id}', [StoreController::class, 'removeCartItem'])->name('cart.remove');
    
    Route::get('/checkout', [StoreController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [StoreController::class, 'processPayment'])->name('checkout.process');
    
    Route::get('/payment/{order}', [StoreController::class, 'showPayment'])->name('payment.show');
    Route::post('/payment/{order}', [StoreController::class, 'confirmPayment'])->name('payment.confirm');
    
    Route::get('/receipt/{order}', [StoreController::class, 'receipt'])->name('receipt');
});

// Admin Routes (Protected + Admin Check)
Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Items
    Route::post('/items', [AdminController::class, 'storeItem'])->name('admin.items.store');
    Route::put('/items/{id}', [AdminController::class, 'updateItem'])->name('admin.items.update');
    Route::delete('/items/{id}', [AdminController::class, 'destroyItem'])->name('admin.items.destroy');
    
    // Users
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    
    // Orders
    Route::put('/orders/{id}', [AdminController::class, 'updateOrder'])->name('admin.orders.update');
    Route::delete('/orders/{id}', [AdminController::class, 'destroyOrder'])->name('admin.orders.destroy');
});
