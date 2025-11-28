<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log; // Diperlukan jika Anda ingin menambahkan Log di Route Closure
use App\Http\Controllers\TodoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\TimerController;

// ---------------------------------------------------------------------
// 1. ROUTE WEBHOOK TELEGRAM (PUBLIC - TIDAK PERLU AUTH)
//    Route ini harus diexclude dari CSRF protection di VerifyCsrfToken.php
// ---------------------------------------------------------------------
Route::post('/telegram/webhook', [TelegramController::class, 'webhook']);

// ---------------------------------------------------------------------
// 2. ROUTE UJI COBA MANUAL (PUBLIC - UNTUK DEMO NOTIFIKASI)
//    Akses ini untuk mengirim notifikasi ke TELEGRAM_OWNER_CHAT_ID di .env
// ---------------------------------------------------------------------
Route::get('/telegram/test-notif', [TelegramController::class, 'sendTestNotification']);

// ---------------------------------------------------------------------
// 3. ROUTE UTAMA
// ---------------------------------------------------------------------
Route::get('/', function () {
    return view('welcome');
});

// ---------------------------------------------------------------------
// 4. ROUTE YANG MEMERLUKAN AUTENTIKASI (PROTECTED ROUTES)
// ---------------------------------------------------------------------
Route::middleware(['auth'])->group(function () {

    // Notifikasi
    Route::get('/notifications', [TodoController::class, 'getNotifications'])->middleware(['auth'])->name('notifications.get');

    // Dalam web.php
    Route::get('/help', function () {return view('help.index');})->name('help');

    // Route Dashboard Utama (Biasanya untuk ringkasan/statistik)
    Route::get('/dashboard', [TodoController::class, 'index'])->name('dashboard');

    // Route Daftar Tugas KHUSUS (untuk tampilan list/filter)
    Route::get('/todos/list', [TodoController::class, 'index'])->name('todos.list');

    // Route Dashboard Utama
    Route::get('/dashboard', [TodoController::class, 'index'])->name('dashboard');

    // Route Sumber Daya (Resource Routes)
    Route::resource('todos', TodoController::class);
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'destroy']);

    // Route Utility BARU
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/timer', [TimerController::class, 'index'])->name('timer');

    // Route Profil (Breeze Standard)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route Telegram Connect/Disconnect (Akses dari Halaman Profile)
    Route::post('/telegram/connect', [TelegramController::class, 'connect'])->name('telegram.connect');
    Route::post('/telegram/disconnect', [TelegramController::class, 'disconnect'])->name('telegram.disconnect');
});

// ---------------------------------------------------------------------
// 5. ROUTE BAWAAN AUTENTIKASI LARAVEL BREEZE
// ---------------------------------------------------------------------
require __DIR__.'/auth.php';