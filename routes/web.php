<?php

use App\Http\Controllers\Applicant\DashboardController as ApplicantDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('public.home');
});

Route::get('/mockup', function () {
    return view('dev.mockup');
});

Route::get('/tailwind', function () {
    return view('dev.frontend-decision');
})->name('tailwind');

Route::get('/jobs', function () {
    return 'Job listing page — to be built';
})->name('jobs.index');

Route::get('/jobs/{id}', function ($id) {
    return 'Job detail page — to be built for job #'.$id;
})->name('jobs.show');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'create'])->name('login');

Route::post('/login', [LoginController::class, 'store'])->name('login.store');

Route::get('/register', [RegisterController::class, 'create'])->name('register');

Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

Route::post('/logout', LogoutController::class)->name('logout');

/*
|--------------------------------------------------------------------------
| Applicant Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('applicant')->name('applicant.')->group(function () {
    Route::get('/dashboard', [ApplicantDashboardController::class, 'index'])->name('dashboard');

    // These are referenced by the dashboard view's links.
    // Build their controllers next; for now you can stub them
    // with a closure so the dashboard doesn't break:

    Route::get('/applications', function () {
        return 'Applications list — to be built';
    })->name('applications.index');

    Route::get('/resume', function () {
        return 'Resume upload page — to be built';
    })->name('resume');

    Route::get('/profile/edit', function () {
        return 'Profile edit page — to be built';
    })->name('profile.edit');
});

/*
|--------------------------------------------------------------------------
| Employer Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('employer')->name('employer.')->group(function () {
    Route::get('/dashboard', function () {
        return 'Employer dashboard — to be built';
    })->name('dashboard');

    Route::get('/jobs', function () {
        return 'Employer jobs — to be built';
    })->name('jobs.index');

    Route::get('/applicants', function () {
        return 'Employer applicants — to be built';
    })->name('applicants.index');

    Route::get('/profile/edit', function () {
        return 'Employer profile edit — to be built';
    })->name('profile.edit');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return 'Admin dashboard — to be built';
    })->name('dashboard');
});
