<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClickupApiController;
use App\Http\Middleware\AuthClickup;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('landing_page_folder.login'))
    ->name('login');

Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('google', [
        AuthController::class, 'redirectToGoogle'
    ])->name('google');

    Route::get('google/callback', [
        AuthController::class, 'handleGoogleCallback'
    ])->name('google.callback');
});

Route::middleware('auth.clickup')->group(function () {

    Route::get('/dashboard', [
        ClickupApiController::class, 'index'
    ])->name('dashboard');

    Route::post('/logout', [
        AuthController::class, 'logout'
    ])->name('logout');

    Route::prefix('clickup')->name('clickup.')->group(function () {

        // Fetch
        Route::get('spaces', [
            ClickupApiController::class, 'getTeams'
        ])->name('spaces');
        Route::get('folders', [
            ClickupApiController::class, 'getFolders'
        ])->name('folders');
        Route::get('lists', [
            ClickupApiController::class, 'getLists'
        ])->name('lists');
        Route::get('tasks', [
            ClickupApiController::class, 'getTasks'
        ])->name('tasks');
        Route::get('members', [
            ClickupApiController::class, 'getMembers'
        ])->name('members');

        // Create
        Route::post('spaces', [
            ClickupApiController::class, 'createSpace'
        ])->name('spaces.create');
        
        Route::post('folders', [
            ClickupApiController::class, 'createFolder'
        ])->name('folders.create');
        Route::post('lists', [
            ClickupApiController::class, 'createList'
        ])->name('lists.create');
        Route::post('tasks', [
            ClickupApiController::class, 'createTask'
        ])->name('tasks.create');

        // Update
        Route::put('spaces/{id}', [
            ClickupApiController::class, 'updateSpace'
        ])->name('spaces.update');

        // Delete
        Route::delete('delete/{id}', [
            ClickupApiController::class, 'delete'
        ])->name('delete');
    });
});