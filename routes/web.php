<?php

use App\Http\Controllers\Admin\AdminDestinationController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AiTripController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Destination Routes (Public)
Route::get('/destinasi', [DestinationController::class, 'index'])->name('destinasi');
Route::get('/destinasi/{destination:slug}', [DestinationController::class, 'show'])->name('destinasi.show');

// Feature Pages
Route::get('/jadwal', [TripController::class, 'jadwal'])->name('jadwal');
Route::get('/anggaran', [TripController::class, 'anggaran'])->name('anggaran');

// AI Assistant Feature
Route::get('/ai', [AiTripController::class, 'index'])->name('ai.index');
Route::post('/ai/generate', [AiTripController::class, 'generate'])->name('ai.generate');

// Trips CRUD (Requires Login for Create, Edit, Delete, Update, and Itinerary Management)
Route::get('/trips', [TripController::class, 'index'])->name('trips.index');

Route::middleware('auth')->group(function () {
    Route::get('/trips/create', [TripController::class, 'create'])->name('trips.create');
    Route::post('/trips', [TripController::class, 'store'])->name('trips.store');
    Route::get('/rencanakan', [TripController::class, 'create'])->name('trips.create.alias');
    Route::get('/trips/{trip}/edit', [TripController::class, 'edit'])->name('trips.edit');
    Route::put('/trips/{trip}', [TripController::class, 'update'])->name('trips.update');
    Route::delete('/trips/{trip}', [TripController::class, 'destroy'])->name('trips.destroy');

    // Itinerary Routes for Trip
    Route::post('/trips/{trip}/itineraries', [ItineraryController::class, 'store'])->name('trips.itineraries.store');
    Route::put('/trips/{trip}/itineraries/{itinerary}', [ItineraryController::class, 'update'])->name('trips.itineraries.update');
    Route::patch('/trips/{trip}/itineraries/{itinerary}/toggle', [ItineraryController::class, 'toggle'])->name('trips.itineraries.toggle');
    Route::delete('/trips/{trip}/itineraries/{itinerary}', [ItineraryController::class, 'destroy'])->name('trips.itineraries.destroy');
});

Route::get('/trips/{trip}', [TripController::class, 'show'])->name('trips.show');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
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
