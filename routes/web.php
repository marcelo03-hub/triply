<?php

use App\Http\Controllers\Admin\AdminDestinationController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AiTripController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FavoriteDestinationController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\PackingItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Destination Routes (Public)
Route::get('/destinasi', [DestinationController::class, 'index'])->name('destinasi');
Route::get('/destinasi/{destination:slug}', [DestinationController::class, 'show'])->name('destinasi.show');

// AI Assistant Feature (Public access with trip suggestions)
Route::get('/ai', [AiTripController::class, 'index'])->name('ai.index');
Route::post('/ai/generate', [AiTripController::class, 'generate'])->name('ai.generate');

// User Protected Routes (Requires Login)
Route::middleware('auth')->group(function () {
    // Feature Pages (Personalized to authenticated user)
    Route::get('/jadwal', [TripController::class, 'jadwal'])->name('jadwal');
    Route::get('/anggaran', [TripController::class, 'anggaran'])->name('anggaran');
    Route::post('/ai/save-trip', [AiTripController::class, 'saveTrip'])->name('ai.save-trip');

    // Trips CRUD
    Route::get('/trips', [TripController::class, 'index'])->name('trips.index');
    Route::get('/trips/create', [TripController::class, 'create'])->name('trips.create');
    Route::post('/trips', [TripController::class, 'store'])->name('trips.store');
    Route::get('/rencanakan', [TripController::class, 'create'])->name('trips.create.alias');
    Route::get('/trips/{trip}', [TripController::class, 'show'])->name('trips.show');
    Route::get('/trips/{trip}/pdf', [TripController::class, 'exportPdf'])->name('trips.pdf');
    Route::get('/trips/{trip}/edit', [TripController::class, 'edit'])->name('trips.edit');
    Route::put('/trips/{trip}', [TripController::class, 'update'])->name('trips.update');
    Route::delete('/trips/{trip}', [TripController::class, 'destroy'])->name('trips.destroy');

    // Itinerary Routes for Trip
    Route::post('/trips/{trip}/itineraries', [ItineraryController::class, 'store'])->name('trips.itineraries.store');
    Route::put('/trips/{trip}/itineraries/{itinerary}', [ItineraryController::class, 'update'])->name('trips.itineraries.update');
    Route::patch('/trips/{trip}/itineraries/{itinerary}/toggle', [ItineraryController::class, 'toggle'])->name('trips.itineraries.toggle');
    Route::delete('/trips/{trip}/itineraries/{itinerary}', [ItineraryController::class, 'destroy'])->name('trips.itineraries.destroy');

    // Expense Tracker Routes
    Route::post('/trips/{trip}/expenses', [ExpenseController::class, 'store'])->name('trips.expenses.store');
    Route::put('/trips/{trip}/expenses/{expense}', [ExpenseController::class, 'update'])->name('trips.expenses.update');
    Route::delete('/trips/{trip}/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('trips.expenses.destroy');

    // Packing Checklist Routes
    Route::post('/trips/{trip}/packing', [PackingItemController::class, 'store'])->name('trips.packing.store');
    Route::put('/trips/{trip}/packing/{packingItem}', [PackingItemController::class, 'update'])->name('trips.packing.update');
    Route::patch('/trips/{trip}/packing/{packingItem}/toggle', [PackingItemController::class, 'toggle'])->name('trips.packing.toggle');
    Route::delete('/trips/{trip}/packing/{packingItem}', [PackingItemController::class, 'destroy'])->name('trips.packing.destroy');

    // Favorite Destinations Routes
    Route::get('/favorit', [FavoriteDestinationController::class, 'index'])->name('favorites.index');
    Route::post('/destinasi/{destination}/favorite', [FavoriteDestinationController::class, 'toggle'])->name('destinations.favorite.toggle');

    // Profile & Account Settings Routes
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Password Reset Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Admin Routes (Protected by auth and admin middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.destinations.index');
    })->name('dashboard');

    // Manajemen Destinasi oleh Admin
    Route::resource('destinations', AdminDestinationController::class);

    // Manajemen Pengguna oleh Admin
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/toggle-admin', [AdminUserController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});
