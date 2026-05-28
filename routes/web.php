<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/captcha', [LoginController::class, 'generateCaptcha'])->name('captcha');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Tickets Base Routes
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/api/categories/{category}/subcategories', [TicketController::class, 'getSubcategories']);

    // IT Staff / IT Lead Routes
    Route::middleware('role:it-staff,it-lead,admin')->group(function () {
        Route::post('/tickets/{ticket}/claim', [TicketController::class, 'claim'])->name('tickets.claim');
        Route::post('/tickets/{ticket}/start', [TicketController::class, 'startWork'])->name('tickets.start');
        Route::post('/tickets/{ticket}/priority', [TicketController::class, 'updatePriority'])->name('tickets.priority');
        Route::post('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.status');
        Route::post('/tickets/{ticket}/activities', [TicketController::class, 'addActivity'])->name('tickets.activities.store');
        Route::post('/tickets/{ticket}/submit-review', [TicketController::class, 'submitForReview'])->name('tickets.submit_review');
    });

    // IT Lead Routes
    Route::middleware('role:it-lead,admin')->group(function () {
        Route::post('/tickets/{ticket}/approve', [TicketController::class, 'approve'])->name('tickets.approve');
        Route::post('/tickets/{ticket}/return', [TicketController::class, 'returnTicket'])->name('tickets.return');
    });

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['create', 'show', 'edit']);
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->except(['create', 'show', 'edit']);
        Route::resource('assets', \App\Http\Controllers\Admin\AssetController::class)->except(['create', 'show', 'edit']);
        Route::resource('sla', \App\Http\Controllers\Admin\SlaConfigController::class)->except(['create', 'show', 'edit', 'update']);
    });

    // User Routes
    Route::post('/tickets/{ticket}/reopen', [TicketController::class, 'reopen'])->name('tickets.reopen');
});
