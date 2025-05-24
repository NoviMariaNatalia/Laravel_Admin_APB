<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController; 

// =====================================
// DEVELOPMENT & TESTING ROUTES
// =====================================

Route::get('/debug-firebase-data', function () {
    try {
        $service = app(\App\Services\SimpleFirebaseService::class);
        $bookings = $service->getAllBookings();
        
        return response()->json([
            'status' => 'SUCCESS',
            'count' => count($bookings),
            'raw_data_sample' => $bookings[0] ?? null,
            'all_fields' => $bookings
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

// Firebase connection test route
Route::get('/test-firebase', function () {
    try {
        $service = app(\App\Services\SimpleFirebaseService::class);
        $bookings = $service->getAllBookings();
        
        return response()->json([
            'status' => 'SUCCESS',
            'count' => count($bookings),
            'sample' => array_slice($bookings, 0, 2),
            'message' => 'Simple Firebase HTTP connection working!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'ERROR',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Debug route untuk cek pending bookings
Route::get('/debug-pending', function () {
    try {
        $service = app(\App\Services\BookingFirebaseService::class);
        $pending = $service->getPendingBookings();
        
        return response()->json([
            'status' => 'SUCCESS',
            'pending_count' => count($pending),
            'pending_bookings' => $pending
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

// ======= ROOT ROUTE =======
Route::get('/', function () {
    return view('welcome');
});

// ======= ADMIN AUTH ROUTES (PUBLIC - TIDAK PERLU LOGIN) =======
Route::prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AdminAuthController::class, 'logout'])->name('logout.get'); // Optional: support GET juga
 
    // Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('index_admin');
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    
    // Firebase Booking Management
    Route::post('/bookings/{id}/update-status', [AdminController::class, 'updateBookingStatus'])->name('bookings.updateStatus');
    Route::get('/bookings/history', [AdminController::class, 'bookingsHistory'])->name('bookings.history');
    Route::get('/debug-firebase', [AdminController::class, 'debugFirebase'])->name('debug.firebase');
    
    // ======= ROOM MANAGEMENT (Firebase) =======
    Route::get('/rooms/create', [RoomController::class, 'input'])->name('create_room');
    Route::post('/rooms', [RoomController::class, 'store'])->name('store_room');
    Route::get('/rooms', [RoomController::class, 'display'])->name('display_room');
    Route::get('/rooms/{id}/edit', [RoomController::class, 'edit'])->name('edit_room');
    Route::put('/rooms/{id}', [RoomController::class, 'update'])->name('update_room');
    Route::delete('/rooms/{id}', [RoomController::class, 'destroy'])->name('delete_room');
    
    // ======= BOOKING MANAGEMENT (Firebase) =======
    Route::get('/bookings/pending', [AdminController::class, 'showPendingBookings'])->name('bookings.pending');
    Route::post('/bookings/{id}/approve', [AdminController::class, 'approveBooking'])->name('bookings.approve');
    Route::post('/bookings/{id}/reject', [AdminController::class, 'rejectBooking'])->name('bookings.reject');
    Route::get('/bookings/history', [AdminController::class, 'bookingsHistory'])->name('bookings.history');
});

// =====================================
// LEGACY ROUTES (BACKUP - Optional)
// =====================================

// Uncomment these if you need fallback to old MySQL booking system
/*
Route::get('/admin/bookings', [BookingController::class, 'adminIndex'])->name('admin.bookings.index');
Route::get('/admin/bookings/history', [BookingController::class, 'adminHistory'])->name('admin.bookings.history.legacy');
Route::post('/admin/bookings/{id}/update-status', [BookingController::class, 'updateStatus'])->name('admin.bookings.updateStatus.legacy');
*/

// Route fallback untuk handle 404
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
