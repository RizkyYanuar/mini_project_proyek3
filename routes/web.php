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
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});


Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Pelapor
    Route::middleware('role:pelapor')->group(function () {
        Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
        Route::get('/reports/{report}/edit', [ReportController::class, 'edit'])->whereNumber('report')->name('reports.edit');
        Route::put('/reports/{report}', [ReportController::class, 'update'])->whereNumber('report')->name('reports.update');
    });

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->whereNumber('report')->name('reports.show');
    Route::get('/reports/{report}/evidence', [ReportController::class, 'evidence'])->whereNumber('report')->name('reports.evidence');

    // Pemantau Komunitas
    Route::middleware('role:pemantau')->group(function () {
        Route::post('/reports/{report}/notes', [ReviewController::class, 'storeNote'])->whereNumber('report')->name('reports.notes.store');
        Route::patch('/reports/{report}/review', [ReviewController::class, 'updateStatus'])->whereNumber('report')->name('reports.review');
    });

    // Koordinator Komunitas
    Route::middleware('role:koordinator')->group(function () {
        Route::post('/reports/{report}/forward', [ForwardingController::class, 'store'])->whereNumber('report')->name('reports.forward');
        Route::resource('schools', SchoolController::class)->except(['show', 'create']);
        Route::resource('distributions', MealDistributionController::class)->except(['show', 'create']);
    });

    // Pemantau + Koordinator
    Route::middleware('role:pemantau,koordinator')->group(function () {
        Route::get('/recap', [RecapController::class, 'index'])->name('recap');
    });

    Route::get('/schools/{school}/distributions', [MealDistributionController::class, 'bySchool'])
        ->whereNumber('school')
        ->name('schools.distributions');
});
