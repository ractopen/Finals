<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\AdminController;

// Public Routes
Route::get('/', [StoreController::class, 'index'])->name('home');
Route::get('/item/{id}', [StoreController::class, 'show'])->name('item.show');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Store Routes (Protected)
Route::middleware('auth')->group(function () {
    Route::get('/cart', [StoreController::class, 'viewCart'])->name('cart.view');
    Route::post('/cart/add/{id}', [StoreController::class, 'addToCart'])->name('cart.add');
    Route::patch('/cart/update/{id}', [StoreController::class, 'updateCartItem'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [StoreController::class, 'removeFromCart'])->name('cart.remove');
    
    Route::get('/checkout', [StoreController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [StoreController::class, 'processOrder'])->name('checkout.process');
    Route::get('/receipt/{order}', [StoreController::class, 'receipt'])->name('receipt');
});

// Admin Routes
Route::middleware(['auth', 'can:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Items
    Route::post('/items', [AdminController::class, 'storeItem'])->name('items.store');
    Route::put('/items/{item}', [AdminController::class, 'updateItem'])->name('items.update');
    Route::delete('/items/{item}', [AdminController::class, 'destroyItem'])->name('items.destroy');
    
    // Users
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    
    // Orders
    Route::put('/orders/{order}', [AdminController::class, 'updateOrder'])->name('orders.update');
});
