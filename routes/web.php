<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\PublicPortfolioController;
use App\Http\Controllers\PortfolioDashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\SocialLinkController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\GraduateController as AdminGraduateController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicPortfolioController::class, 'index'])->name('home');
Route::get('/directory', [PublicPortfolioController::class, 'index'])->name('directory');
Route::get('/p/{slug}', [PublicPortfolioController::class, 'show'])->name('portfolio.show');
Route::get('/p/{slug}/cv', [PublicPortfolioController::class, 'downloadCv'])->name('portfolio.cv.download')->middleware('throttle:analytics');
Route::post('/projects/{project}/click', [PublicPortfolioController::class, 'recordProjectClick'])->name('projects.click')->middleware('throttle:analytics');

// Temporary route to trigger migrations and seeds on Railway
Route::get('/run-migrations', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        return "Database migration and seeding completed successfully!<br><br>Output:<br>" . nl2br(\Illuminate\Support\Facades\Artisan::output());
    } catch (\Exception $e) {
        return "Error running migrations: " . $e->getMessage();
    }
});

// Redirect old routes to new /p/ URLs
Route::get('/portfolio/{slug}', function (string $slug) {
    return redirect()->route('portfolio.show', ['slug' => $slug], 301);
});
Route::get('/portfolio/{slug}/cv', function (string $slug) {
    return redirect()->route('portfolio.cv.download', ['slug' => $slug], 301);
});

/*
|--------------------------------------------------------------------------
| Guest-Only Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Login only — registration is admin-only
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

    // Forgot / Reset Password
    Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ── Force Password Change ──────────────────────────────────
    Route::get('/change-password', [ChangePasswordController::class, 'show'])->name('password.change');
    Route::post('/change-password', [ChangePasswordController::class, 'update'])->name('password.change.update');

    // ── Email Verification ─────────────────────────────────────
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')->name('verification.send');

    // ── Graduate Dashboard (role:graduate + force password change) ──
    Route::middleware(['role:graduate', 'force.password'])->group(function () {

        // Dashboard home
        Route::get('/dashboard', [PortfolioDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/analytics', [PortfolioDashboardController::class, 'analytics'])->name('dashboard.analytics');

        // Personal Information
        Route::post('/dashboard/personal', [PortfolioDashboardController::class, 'updatePersonal'])->name('dashboard.personal.update');
        Route::post('/dashboard/portfolio/update', [PortfolioDashboardController::class, 'update'])->name('dashboard.portfolio.update');
        Route::post('/dashboard/portfolio/picture', [PortfolioDashboardController::class, 'uploadProfilePicture'])->name('dashboard.portfolio.picture');

        // Contact Information
        Route::post('/dashboard/contact', [PortfolioDashboardController::class, 'updateContact'])->name('dashboard.contact.update');

        // CV
        Route::post('/dashboard/portfolio/cv', [PortfolioDashboardController::class, 'uploadCv'])->name('dashboard.portfolio.cv');

        // Publish Toggle
        Route::post('/dashboard/portfolio/toggle-publish', [PortfolioDashboardController::class, 'togglePublish'])->name('dashboard.portfolio.toggle');

        // Skills
        Route::post('/dashboard/skills', [SkillController::class, 'store'])->name('dashboard.skills.store');
        Route::put('/dashboard/skills/{skill}', [SkillController::class, 'update'])->name('dashboard.skills.update');
        Route::delete('/dashboard/skills/{skill}', [SkillController::class, 'destroy'])->name('dashboard.skills.destroy');

        // Projects — management hub
        Route::get('/dashboard/projects', [ProjectController::class, 'index'])->name('dashboard.projects.index');
        Route::get('/dashboard/projects/create', [ProjectController::class, 'create'])->name('dashboard.projects.create');
        Route::post('/dashboard/projects', [ProjectController::class, 'store'])->name('dashboard.projects.store');
        Route::get('/dashboard/projects/{project}/edit', [ProjectController::class, 'edit'])->name('dashboard.projects.edit');
        Route::put('/dashboard/projects/{project}', [ProjectController::class, 'update'])->name('dashboard.projects.update');
        Route::delete('/dashboard/projects/{project}', [ProjectController::class, 'destroy'])->name('dashboard.projects.destroy');

        // Project gallery images
        Route::post('/dashboard/projects/{project}/images', [ProjectController::class, 'addImage'])->name('dashboard.projects.images.add');
        Route::delete('/dashboard/project-images/{image}', [ProjectController::class, 'destroyImage'])->name('dashboard.projects.images.destroy');
        Route::post('/dashboard/project-images/{image}/set-cover', [ProjectController::class, 'setCover'])->name('dashboard.projects.images.set-cover');
        Route::delete('/dashboard/projects/{project}/cover', [ProjectController::class, 'removeCover'])->name('dashboard.projects.cover.remove');

        // Social Links
        Route::post('/dashboard/social-links', [SocialLinkController::class, 'store'])->name('dashboard.social.store');
        Route::delete('/dashboard/social-links/{socialLink}', [SocialLinkController::class, 'destroy'])->name('dashboard.social.destroy');
    });

    // ── Admin Routes ───────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Graduates CRUD
        Route::get('/graduates', [AdminGraduateController::class, 'index'])->name('admin.graduates.index');
        Route::get('/graduates/create', [AdminGraduateController::class, 'create'])->name('admin.graduates.create');
        Route::post('/graduates', [AdminGraduateController::class, 'store'])->name('admin.graduates.store');
        Route::get('/graduates/{user}/credentials', [AdminGraduateController::class, 'credentials'])->name('admin.graduates.credentials');
        Route::get('/graduates/{user}', [AdminGraduateController::class, 'show'])->name('admin.graduates.show');
        Route::get('/graduates/{user}/edit', [AdminGraduateController::class, 'edit'])->name('admin.graduates.edit');
        Route::put('/graduates/{user}', [AdminGraduateController::class, 'update'])->name('admin.graduates.update');
        Route::delete('/graduates/{user}', [AdminGraduateController::class, 'destroy'])->name('admin.graduates.destroy');

        // Portfolio Toggle
        Route::post('/portfolios/{portfolio}/toggle-publish', [AdminGraduateController::class, 'togglePublish'])->name('admin.portfolios.toggle');
        Route::post('/portfolios/{portfolio}/toggle-verification', [AdminGraduateController::class, 'toggleVerification'])->name('admin.portfolios.toggle-verification');

        // Graduates Actions
        Route::post('/graduates/{user}/toggle-suspension', [AdminGraduateController::class, 'toggleSuspension'])->name('admin.graduates.toggle-suspension');

        // Site-wide Projects CRUD
        Route::get('/projects', [AdminProjectController::class, 'index'])->name('admin.projects.index');
        Route::delete('/projects/{project}', [AdminProjectController::class, 'destroy'])->name('admin.projects.destroy');
    });
});
