<?php

use Illuminate\Support\Facades\Route;

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