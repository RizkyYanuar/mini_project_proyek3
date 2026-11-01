<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForwardingController;
use App\Http\Controllers\MealDistributionController;
use App\Http\Controllers\RecapController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SchoolController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');

    // Pelapor 
    Route::middleware('role:pelapor')->group(function () {
        Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
        Route::get('/reports/{report}/edit', [ReportController::class, 'edit'])->name('reports.edit');
        Route::put('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');
    });

    // Pemantau Komunitas
    Route::middleware('role:pemantau')->group(function () {
        Route::post('/reports/{report}/notes', [ReviewController::class, 'storeNote'])->name('reports.notes.store');
        Route::patch('/reports/{report}/review', [ReviewController::class, 'updateStatus'])->name('reports.review');
    });

    // Koordinator Komunitas
    Route::middleware('role:koordinator')->group(function () {
        Route::post('/reports/{report}/forward', [ForwardingController::class, 'store'])->name('reports.forward');
        Route::resource('schools', SchoolController::class)->except('show');
        Route::resource('distributions', MealDistributionController::class)->except('show');
    });

    // Pemantau + Koordinator
    Route::middleware('role:pemantau,koordinator')->group(function () {
        Route::get('/recap', [RecapController::class, 'index'])->name('recap');
    });

    // Endpoint fetch(): distribusi berdasarkan sekolah
    Route::get('/schools/{school}/distributions', [MealDistributionController::class, 'bySchool'])
        ->name('schools.distributions');
});