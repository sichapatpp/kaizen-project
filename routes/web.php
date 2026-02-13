<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ApproveController;
use App\Http\Controllers\KaizenController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/activities', [KaizenController::class, 'index'])->name('activities.index');
    Route::post('/activities', [KaizenController::class, 'store'])->name('activities.store');
    Route::get('/activities/status', [KaizenController::class, 'status'])->name('activities.status');
    Route::get('/activities/approve', [KaizenController::class, 'approve'])->name('activities.approve');

    // Manager & Chairman & Admin
    Route::middleware(['role:manager,chairman,admin'])->group(function () {
        Route::post('/activities/{id}/update-status', [KaizenController::class, 'updateStatus'])->name('activities.updateStatus');
    });

    Route::get('/activities/{id}/report', [KaizenController::class, 'report'])->name('activities.report');
    Route::post('/activities/{id}/report', [KaizenController::class, 'saveReport'])->name('activities.saveReport');
    Route::get('/activities/{id}', [KaizenController::class, 'show'])->name('activities.show');

    // Admin
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('roles', RoleController::class);
        Route::resource('user', UserController::class);
    });
});
