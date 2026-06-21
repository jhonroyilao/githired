<?php

use App\Http\Controllers\Applicant\DashboardController as ApplicantDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mockup', function () {
    return view('mockup');
});

Route::get('/tailwind', function () {
    return view('tailwind');
})->name('tailwind');

Route::get('/jobs', function () {
    return 'Job listing page — to be built';
})->name('jobs.index');

Route::get('/jobs/{id}', function ($id) {
    return 'Job detail page — to be built for job #' . $id;
})->name('jobs.show');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return 'Login page coming soon';
})->name('login');

Route::get('/register', function () {
    return 'Register page coming soon';
})->name('register');

Route::post('/logout', function () {
    return 'Logout route coming soon';
})->name('logout');

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
