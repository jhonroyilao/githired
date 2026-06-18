<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Applicant\DashboardController as ApplicantDashboardController;
// use App\Http\Controllers\Applicant\ProfileController;
// use App\Http\Controllers\Applicant\ApplicationController;
// use App\Http\Controllers\JobController;
 
Route::get('/', function () {
    return view('welcome');
});

Route::get('/mockup', function () {
    return view('mockup');
});

Route::get('/jobs', function () {
    return 'Jobs page coming soon';
})->name('jobs.index');

Route::get('/login', function () {
    return 'Login page coming soon';
})->name('login');

Route::get('/register', function () {
    return 'Register page coming soon';
})->name('register');




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
 

Route::get('/jobs', function () {
    return 'Job listing page — to be built';
})->name('jobs.index');
 
Route::get('/jobs/{id}', function ($id) {
    return 'Job detail page — to be built for job #' . $id;
})->name('jobs.show');