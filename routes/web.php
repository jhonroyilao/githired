<?php

use App\Actions\Onboarding\ResolveUserDestinationRouteAction;
use App\Enums\UserRole;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Applicant\ApplicationController;
use App\Http\Controllers\Applicant\DashboardController as ApplicantDashboardController;
use App\Http\Controllers\Applicant\Onboarding\BasicProfileController;
use App\Http\Controllers\Applicant\Onboarding\LinksController;
use App\Http\Controllers\Applicant\Onboarding\PreferencesController;
use App\Http\Controllers\Applicant\Onboarding\SummaryController;
use App\Http\Controllers\Applicant\ProfileController as ApplicantProfileController;
use App\Http\Controllers\Applicant\ResumeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Employer\DashboardController as EmployerDashboardController;
use App\Http\Controllers\Employer\Onboarding\CompanyProfileController;
use App\Http\Controllers\JobController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function (ResolveUserDestinationRouteAction $resolveDestination) {
    if ($user = Auth::user()) {
        return redirect()->route($resolveDestination->handle($user));
    }

    return redirect()->route('login');
});

Route::view('/mockup', 'mockup');
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{jobListing}', [JobController::class, 'show'])->name('jobs.show');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::post('/logout', LogoutController::class)->name('logout');

/*
|--------------------------------------------------------------------------
| Applicant Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:'.UserRole::Applicant->value])->prefix('applicant')->name('applicant.')->group(function () {

    // Onboarding Sub-Group
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/profile', [BasicProfileController::class, 'create'])->name('profile');
        Route::post('/profile', [BasicProfileController::class, 'store'])->name('profile.store');

        // Dynamic Text blocks logic references
        Route::get('/summary', [SummaryController::class, 'create'])->name('summary');
        Route::post('/summary', [SummaryController::class, 'store'])->name('summary.store');

        Route::get('/preferences', [PreferencesController::class, 'create'])->name('preferences');
        Route::post('/preferences', [PreferencesController::class, 'store'])->name('preferences.store');

        Route::get('/links', [LinksController::class, 'create'])->name('links');
        Route::post('/links', [LinksController::class, 'store'])->name('links.store');
    });

    // Core Applicant Workspace Layout
    Route::get('/dashboard', ApplicantDashboardController::class)->name('dashboard');
    Route::get('/profile', [ApplicantProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ApplicantProfileController::class, 'update'])->name('profile.update');
    Route::get('/applications', fn () => 'applications index')->name('applications.index');
    Route::get('/resume', [ResumeController::class, 'index'])->name('resume');
    Route::post('/resume', [ResumeController::class, 'store'])->name('resume.store');
    Route::get('/resume/{resumeDocument}', [ResumeController::class, 'show'])->name('resume.show');
    Route::patch('/resume/{resumeDocument}/set-current', [ResumeController::class, 'setCurrent'])->name('resume.set-current');
    Route::delete('/resume/{resumeDocument}', [ResumeController::class, 'destroy'])
        ->name('resume.destroy')
        ->missing(fn (Request $request) => redirect()
            ->route($request->input('redirect_to') === 'applicant.onboarding.links'
                ? 'applicant.onboarding.links'
                : 'applicant.resume')
            ->with('status', 'Resume already removed.'));
    Route::get('/job-listings/{jobListing}/apply', [ApplicationController::class, 'create'])->name('job-listings.apply');
    Route::post('/job-listings/{jobListing}/apply', [ApplicationController::class, 'store'])->name('job-listings.apply.store');
});

/*
|--------------------------------------------------------------------------
| Employer Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:'.UserRole::Employer->value])->prefix('employer')->name('employer.')->group(function () {
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/company', [CompanyProfileController::class, 'create'])->name('company');
        Route::post('/company', [CompanyProfileController::class, 'store'])->name('company.store');
    });

    Route::get('/dashboard', EmployerDashboardController::class)->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:'.UserRole::Admin->value])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
});
