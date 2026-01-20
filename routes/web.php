<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClickupApiController;
use App\Http\Controllers\ClickupUserController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing_page_folder.login');
})->name('login');

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard')->middleware('auth.clickup');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('user')->name('user.')->group(function () {

    Route::post('/login', [
        ClickupUserController::class, 'check'
    ])->name('login');

    Route::post('register', [
        ClickupUserController::class, 'store'
    ])->name('register');

    Route::post('/logout', [
        ClickupUserController::class, 'logout'
    ])->name('logout');

});

// Clickup API Routes

Route::get('/dashboard', [
    ClickupApiController::class, 'index'
])->name('dashboard');

Route::prefix('clickup')->name('clickup.')->group(function (){

    Route::get('/spaces', [
        ClickupApiController::class, 'getTeams'
    ])->name('spaces');

    Route::get('/folders', [
        ClickupApiController::class, 'getFolders'
    ])->name('folders');

    Route::get('/lists', [
        ClickupApiController::class, 'getLists'
    ])->name('lists');

    Route::get('/tasks', [
        ClickupApiController::class, 'getTasks'
    ])->name('tasks');

    Route::get('/members', [
        ClickupApiController::class, 'getMembers'
    ])->name('members');

    Route::post('/space', [
        ClickupApiController::class, 'createSpace'
    ])->name('space.create');

    Route::post('/folder', [
        ClickupApiController::class, 'createFolder'
    ])->name('folder.create');

    Route::post('/list', [
        ClickupApiController::class, 'createList'
    ])->name('list.create');

    Route::post('/create/task', [
        ClickupApiController::class, 'createTask'
    ])->name('task.create');

    Route::put('/spaces/{id}', [
        ClickupApiController::class, 'updateSpace'
    ])->name('spaces.update');

    Route::delete('/clickup/delete', [
        ClickupAPiController::class, 'delete'
    ])->name('delete');

});


