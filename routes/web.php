<?php

use App\Actions\Onboarding\ResolveUserDestinationRouteAction;
use App\Enums\UserRole;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JobManagementController;
use App\Http\Controllers\Admin\JobModerationController;
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
use App\Http\Controllers\Employer\EmployerJobListingController;
use App\Http\Controllers\Employer\Onboarding\CompanyProfileController;
use App\Http\Controllers\Employer\ProfileController;
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

    return view('public.landing');
})->name('home');

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
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
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
    Route::put('/password', [ApplicantProfileController::class, 'updatePassword'])->name('password.update');
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

    Route::prefix('company')->name('company.')->group(function () {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/', [EmployerJobListingController::class, 'index'])->name('index');
        Route::get('/create', [EmployerJobListingController::class, 'create'])->name('create');
        Route::post('/', [EmployerJobListingController::class, 'store'])->name('store');
        Route::get('/{jobListing}', [EmployerJobListingController::class, 'show'])->name('show');
        Route::get('/{jobListing}/edit', [EmployerJobListingController::class, 'edit'])->name('edit');
        Route::put('/{jobListing}', [EmployerJobListingController::class, 'update'])->name('update');

        Route::get('/{jobListing}/applicants', [EmployerJobListingController::class, 'applicants'])->name('applicants');
        Route::get('/{jobListing}/applicants/{application}', [EmployerJobListingController::class, 'showApplication'])->name('applicants.show');
        Route::get('/{jobListing}/applicants/{application}/resume', [EmployerJobListingController::class, 'downloadApplicationResume'])->name('applicants.resume');
        Route::patch('/{jobListing}/applicants/{application}/status', [EmployerJobListingController::class, 'updateApplicationStatus'])->name('applicants.status.update');

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
    Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');

    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/pending', [JobModerationController::class, 'index'])->name('pending');
        Route::get('/all', [JobManagementController::class, 'index'])->name('all');
        Route::post('/{jobListing}/approve', [JobModerationController::class, 'approve'])->name('approve');
        Route::post('/{jobListing}/reject', [JobModerationController::class, 'reject'])->name('reject');
        Route::post('/{jobListing}/hide', [JobManagementController::class, 'hide'])->name('hide');
        Route::post('/{jobListing}/reapprove', [JobModerationController::class, 'reapprove'])->name('reapprove');
        Route::post('/{jobListing}/reactivate', [JobModerationController::class, 'reactivate'])->name('reactivate');
        Route::post('/{jobListing}/restore', [JobManagementController::class, 'restore'])->withTrashed()->name('restore');
        Route::delete('/{jobListing}', [JobManagementController::class, 'destroy'])->name('destroy');
    });
});
