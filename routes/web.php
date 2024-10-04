<?php

use App\Http\Controllers\NoIdeaController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
// Route::get('no-idea')

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('no-idea', [NoIdeaController::class, 'index'])->name('no-idea');

require __DIR__.'/auth.php';
