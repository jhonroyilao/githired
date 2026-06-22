<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Applicant\ResumeController;
use App\Http\Controllers\Applicant\DashboardController as ApplicantDashboardController;

Route::get('/', function () {
    return view('welcome');
});

//Applicant route group where users must be logged in to access these endpoints and handles all the logic for resumes.
Route::name('applicant.')->middleware(['auth'])->group(function () {
    Route::get('/resume', [ResumeController::class, 'index'])->name('resume');
    Route::post('/resume', [ResumeController::class, 'store'])->name('resume.store');
    Route::get('/resume/{resumeDocument}', [ResumeController::class, 'show'])->name('resume.show');
    Route::patch('/resume/{resumeDocument}/set-current', [ResumeController::class, 'setCurrent'])->name('resume.set-current');
    Route::delete('/resume/{resumeDocument}', [ResumeController::class, 'destroy'])->name('resume.destroy');
});