<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'pemilik') {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('penyewa.dashboard');
        }
    }
    return redirect()->route('login');
})->name('dashboard');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// Public Routes untuk fitur kamar
Route::view('/rooms', 'rooms.index')->name('rooms.index');
Route::view('/rooms/{roomId}/book', 'rooms.book')->name('rooms.book');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // Penyewa Routes
    Route::prefix('penyewa')->name('penyewa.')->group(function () {
        // Dashboard
        Route::view('/dashboard', 'penyewa.dashboard')->name('dashboard');
        
        // Bookings
        Route::view('/bookings', 'penyewa.bookings')->name('bookings.index');
        Route::view('/payment/{bookingId}', 'penyewa.payment')->name('payment');
        
        // Complaints
        Route::view('/complaints', 'penyewa.complaints')->name('complaints.index');
        Route::view('/complaints/create', 'penyewa.complaint-create')->name('complaints.create');
        
        // Bills & Payments
        Route::view('/bills', 'penyewa.bills')->name('bills.index');
        Route::view('/bills/history', 'penyewa.bill-history')->name('bills.history');
        
        // Announcements
        Route::view('/announcements', 'penyewa.announcements')->name('announcements.index');
        
        // Profile
        Route::view('/profile', 'penyewa.profile')->name('profile');
    });

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
        // Dashboard
        Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
        
        // Room Management
        Route::view('/rooms', 'admin.rooms')->name('rooms.manage');
        
        // User Management
        Route::view('/users', 'admin.users')->name('users.index');
        
        // Booking Management
        Route::view('/bookings', 'admin.bookings')->name('bookings.index');
        
        // Bill Management
        Route::view('/bills', 'admin.bills')->name('bills.index');
        
        // Payment Proof Verification
        Route::view('/payment-proofs', 'admin.payment-proofs')->name('payment-proofs.index');
        
        // Complaints Management
        Route::view('/complaints', 'admin.complaints')->name('complaints.index');
        
        // Announcements
        Route::view('/announcements', 'admin.announcements')->name('announcements.index');
        
        // Export Data
        Route::view('/export', 'admin.export')->name('export.index');
        
        // Profile
        Route::view('/profile', 'admin.profile')->name('profile');
    });
});