<?php

use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdvertisementController;
use App\Http\Controllers\Admin\CounterController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CounterReviewController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewSetupController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;

// Public pages
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/about', [LandingController::class, 'about'])->name('landing.about');
Route::get('/services', [LandingController::class, 'services'])->name('landing.services');
Route::get('/features', [LandingController::class, 'features'])->name('landing.features');
Route::get('/contact', [LandingController::class, 'contact'])->name('landing.contact');
Route::get('/pricing', [LandingController::class, 'pricing'])->name('landing.pricing');
Route::get('/industry', [LandingController::class, 'industry'])->name('landing.industry');

// Review kiosk (public, code-gated, shown after a service is completed).
// The review code is the only accepted key — token numbers restart daily and
// cannot identify a visit — and every step is rate limited per IP.
Route::get('/review', [ReviewController::class, 'index'])->name('review.kiosk');
Route::post('/review/verify', [ReviewController::class, 'verify'])
    ->middleware('throttle:review-kiosk')
    ->name('review.verify');
Route::post('/review/submit', [ReviewController::class, 'submit'])
    ->middleware('throttle:review-kiosk')
    ->name('review.submit');
Route::post('/review/reset', [ReviewController::class, 'resetSession'])->name('review.reset');
Route::get('/review/thanks', [ReviewController::class, 'thanks'])->name('review.thanks');

// Contact form
Route::post('/contact', [LandingController::class, 'submitContact'])->name('contact.submit');

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
        // Must stay ahead of /tokens/{token} so "issued" is not captured as a
        // token id (guarded by AuditSmokeTest::test_token_create_route_not_shadowed_by_show).
        Route::get('/tokens/issued', [TokenController::class, 'issued'])->name('tokens.issued');
        Route::get('/tokens', [TokenController::class, 'index'])->name('tokens.index');
        Route::get('/tokens/{token}', [TokenController::class, 'show'])->name('tokens.show');
        Route::get('/tokens/{token}/print', [TokenController::class, 'print'])->name('tokens.print');
    });

    Route::middleware('permission:queue.view')->group(function () {
        Route::get('/queue', [QueueController::class, 'index'])->name('queue.index');
    });
    // The counter review page is the operator-facing counterpart to
    // /admin/reviews: same filters and actions, scoped to their own counter.
    Route::middleware('permission:reviews.manage')->group(function () {
        Route::get('/queue/reviews', [CounterReviewController::class, 'index'])->name('queue.reviews');
    });
    Route::middleware('permission:queue.next')->post('/queue/next', [QueueController::class, 'next'])->name('queue.next');
    // Operators flip their own counter open/closed; closing hides its waiting
    // patients from the display and blocks new calls.
    Route::middleware('permission:queue.next')->post('/queue/counter/toggle', [QueueController::class, 'toggleCounter'])->name('queue.counter.toggle');
    Route::middleware('permission:queue.previous')->get('/queue/previous', [QueueController::class, 'previous'])->name('queue.previous');
    Route::post('/queue/{token}/{action}', [QueueController::class, 'action'])->whereIn('action', ['start', 'complete', 'skip', 'recall', 'cancel'])->name('queue.action');

    Route::middleware('permission:queue.view')->group(function () {
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::post('/staff/{token}/process', [StaffController::class, 'process'])->name('staff.process')->middleware('permission:queue.complete');
    });

    Route::middleware('permission:display.manage')->group(function () {
        Route::get('/display/manage', [DisplayController::class, 'manage'])->name('display.manage');
        Route::put('/display/manage', [DisplayController::class, 'updateManage'])->name('display.update');
        Route::get('/reviews/setup', [ReviewSetupController::class, 'edit'])->name('reviews.setup');
        Route::put('/reviews/setup', [ReviewSetupController::class, 'update'])->name('reviews.setup.update');
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
        Route::post('/admin/counters/{counter}/toggle', [CounterController::class, 'toggle'])->name('admin.counters.toggle');
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

    Route::middleware('permission:adverts.view')->group(function () {
        // Form (index playlists) is group-viewable; management actions sit
        // behind adverts.manage / adverts.configure so access can be granted
        // narrowly.
        Route::get('/admin/advertisements', [AdvertisementController::class, 'index'])->name('admin.advertisements.index');
    });
    Route::middleware('permission:adverts.configure')->group(function () {
        Route::get('/admin/advertisements/settings', [AdvertisementController::class, 'settings'])->name('admin.advertisements.settings');
        Route::patch('/admin/advertisements/settings', [AdvertisementController::class, 'updateSettings'])->name('admin.advertisements.settings.update');
    });
    Route::middleware('permission:adverts.manage')->group(function () {
        Route::get('/admin/advertisements/create', [AdvertisementController::class, 'create'])->name('admin.advertisements.create');
        Route::post('/admin/advertisements', [AdvertisementController::class, 'store'])->name('admin.advertisements.store');
        Route::get('/admin/advertisements/{advertisement}/edit', [AdvertisementController::class, 'edit'])->name('admin.advertisements.edit');
        Route::put('/admin/advertisements/{advertisement}', [AdvertisementController::class, 'update'])->name('admin.advertisements.update');
        Route::delete('/admin/advertisements/{advertisement}', [AdvertisementController::class, 'destroy'])->name('admin.advertisements.destroy');
        Route::post('/admin/advertisements/{advertisement}/toggle', [AdvertisementController::class, 'toggle'])->name('admin.advertisements.toggle');
        Route::post('/admin/advertisements/{advertisement}/live', [AdvertisementController::class, 'setLive'])->name('admin.advertisements.live');
        Route::post('/admin/advertisements/reorder', [AdvertisementController::class, 'reorder'])->name('admin.advertisements.reorder');
    });

    Route::middleware('permission:reviews.view')->group(function () {
        Route::get('/admin/reviews', [AdminReviewController::class, 'index'])->name('admin.reviews.index');
        Route::get('/admin/reviews/export', [AdminReviewController::class, 'export'])->name('admin.reviews.export');
    });

    Route::middleware('permission:reviews.manage')->group(function () {
        // Readable by counter operators as well, so they can see the full
        // comment at their own counter. AdminReviewController::show applies the
        // same counter-scope guard as the approve/reject actions.
        Route::get('/admin/reviews/{review}', [AdminReviewController::class, 'show'])->name('admin.reviews.show');
        Route::patch('/admin/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('admin.reviews.approve');
        Route::patch('/admin/reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('admin.reviews.reject');
        Route::post('/admin/reviews/bulk', [AdminReviewController::class, 'bulk'])->name('admin.reviews.bulk');
    });

    // Delete and restore are reserved for admins (see AdminReviewController).
    Route::middleware('permission:reviews.delete')->group(function () {
        Route::patch('/admin/reviews/{review}/restore', [AdminReviewController::class, 'restore'])->name('admin.reviews.restore');
        Route::delete('/admin/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('admin.reviews.destroy');
    });
});

require __DIR__.'/auth.php';
