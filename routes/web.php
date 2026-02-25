<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ApproveController;
use App\Http\Controllers\KaizenController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('dashboard');
    }

    $stats = [
        'total' => App\Models\KaizenProject::where('status', '!=', 'draft')->count(),
        'completed' => App\Models\KaizenProject::where('status', 'completed')->count(),
        'in_progress' => App\Models\KaizenProject::whereIn('status', [
            'pending',
            'in_progress',
            'waiting_for_manager_result_approval',
            'waiting_for_chairman_approval'
        ])->count(),
    ];

    return view('welcome', compact('stats'));
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class , 'index'])->name('home');

Route::middleware(['auth'])->group(function () {

    Route::get('/activities', [KaizenController::class , 'index'])->name('activities.index');
    Route::post('/activities', [KaizenController::class , 'store'])->name('activities.store');
    Route::get('/activities/create', [KaizenController::class , 'create'])->name('activities.create');
    Route::get('/activities/status', [KaizenController::class , 'status'])->name('activities.status');
    Route::get('/activities/approve', [KaizenController::class , 'approve'])->name('activities.approve');

    Route::get('/activities/draft', [KaizenController::class , 'drafts'])->name('activities.draft');
    Route::get('/activities/draft-count', [KaizenController::class , 'draftCount'])->name('activities.draftCount');
    Route::post('/activities/save-draft', [KaizenController::class , 'saveDraft'])->name('activities.saveDraft');
    Route::get('/activities/draft/{id}/edit', [KaizenController::class , 'editDraft'])->name('activities.editDraft');
    Route::delete('/activities/draft/{id}', [KaizenController::class , 'deleteDraft'])->name('activities.deleteDraft');

    Route::get('/activities/{id}/report', [KaizenController::class , 'report'])->name('activities.report');
    Route::post('/activities/{id}/report', [KaizenController::class , 'saveReport'])->name('activities.saveReport');
    Route::get('/activities/{id}/edit', [KaizenController::class , 'edit'])->name('activities.edit');
    Route::put('/activities/{id}', [KaizenController::class , 'update'])->name('activities.update');
    Route::get('/activities/{id}', [KaizenController::class , 'show'])->name('activities.show');

    // ── Manager / Chairman / Admin ─────────────────────────────────────────
    Route::middleware(['role:manager,chairman,admin'])->group(function () {
            Route::post('/activities/{id}/update-status', [KaizenController::class , 'updateStatus'])->name('activities.updateStatus');
        }
        );

        // ── Dashboard ──────────────────────────────────────────────────────────
        Route::get('/dashboard', [DashboardController::class , 'index'])->name('dashboard');

        // ── Admin only ─────────────────────────────────────────────────────────
        Route::middleware(['role:admin'])->group(function () {
            Route::resource('roles', RoleController::class);
            Route::resource('user', UserController::class);
            Route::post('user/{id}/toggle-status', [UserController::class , 'toggleStatus'])->name('user.toggle-status');
        }
        );
    });