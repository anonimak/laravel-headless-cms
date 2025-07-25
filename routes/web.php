<?php

use Livewire\Volt\Volt;


// Route::middleware(['auth'])->group(function () {
//     Volt::route('/', 'dashboard.index')->name('dashboard.index');
//     Volt::route('category', 'category.index')->name('category');
//     Volt::route('post', 'post.index')->name('post');
//     Volt::route('page', 'page.index')->name('page');
//     Route::view('profile', 'profile')
//     ->name('profile');
// });
Volt::route('/', 'dashboard.index')->name('dashboard');

require __DIR__ . '/auth.php';
