<?php

use App\Http\Controllers\Admin\CounterController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::get('/display', [DisplayController::class, 'screen'])->name('display');
Route::get('/api/display', [DisplayController::class, 'api'])->name('display.api');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'permission:dashboard.view'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::middleware('permission:serials.create')->group(function () {
        Route::get('/tokens/create', [TokenController::class, 'create'])->name('tokens.create');
        Route::post('/tokens', [TokenController::class, 'store'])->name('tokens.store');
    });
    Route::middleware('permission:serials.view')->group(function () {
        Route::get('/tokens', [TokenController::class, 'index'])->name('tokens.index');
        Route::get('/tokens/{token}', [TokenController::class, 'show'])->name('tokens.show');
        Route::get('/tokens/{token}/print', [TokenController::class, 'print'])->name('tokens.print');
    });

    Route::middleware('permission:queue.view')->group(function () {
        Route::get('/queue', [QueueController::class, 'index'])->name('queue.index');
    });
    Route::middleware('permission:queue.next')->post('/queue/next', [QueueController::class, 'next'])->name('queue.next');
    Route::middleware('permission:queue.previous')->get('/queue/previous', [QueueController::class, 'previous'])->name('queue.previous');
    Route::post('/queue/{token}/{action}', [QueueController::class, 'action'])->whereIn('action', ['start', 'complete', 'skip', 'recall', 'cancel'])->name('queue.action');

    Route::middleware('permission:queue.view')->group(function () {
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::post('/staff/{token}/process', [StaffController::class, 'process'])->name('staff.process')->middleware('permission:queue.complete');
    });

    Route::middleware('permission:display.manage')->group(function () {
        Route::get('/display/manage', [DisplayController::class, 'manage'])->name('display.manage');
        Route::put('/display/manage', [DisplayController::class, 'updateManage'])->name('display.update');
    });

    Route::middleware('permission:reports.view')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

    Route::middleware('permission:roles.manage')->group(function () {
        Route::get('/admin/roles', [RoleController::class, 'index'])->name('admin.roles.index');
        Route::put('/admin/roles', [RoleController::class, 'update'])->name('admin.roles.update');
    });

    Route::middleware('permission:services.manage')->group(function () {
        Route::resource('admin/services', ServiceController::class, ['as' => 'admin'])->except(['show']);
        Route::resource('admin/doctors', DoctorController::class, ['as' => 'admin'])->except(['show']);
    });
    Route::middleware('permission:counters.manage')->group(function () {
        Route::resource('admin/counters', CounterController::class, ['as' => 'admin'])->except(['show']);
    });
    Route::middleware('permission:users.view')->group(function () {
        Route::get('admin/users', [UserController::class, 'index'])->name('admin.users.index');
    });
    Route::middleware('permission:users.create')->group(function () {
        Route::get('admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('admin/users', [UserController::class, 'store'])->name('admin.users.store');
    });
    Route::middleware('permission:users.edit')->group(function () {
        Route::get('admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::patch('admin/users/{user}', [UserController::class, 'update']);
    });
    Route::middleware('permission:users.delete')->group(function () {
        Route::delete('admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });
    Route::middleware('permission:settings.manage')->group(function () {
        Route::get('/admin/settings', [SettingController::class, 'edit'])->name('admin.settings.edit');
        Route::put('/admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');
    });
});

require __DIR__.'/auth.php';
