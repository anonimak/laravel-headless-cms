<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


Route::middleware(['auth'])->group(function () {
    Volt::route('/', 'dashboard.index')->name('dashboard.index');
    Volt::route('dashboard', 'dashboard.index')->name('dashboard');
    Volt::route('category', 'category.index')->name('category');
    Volt::route('post', 'post.index')->name('post');
    Volt::route('page', 'page.index')->name('page');
    Route::view('profile', 'profile')
        ->name('profile');
});

require __DIR__ . '/auth.php';
