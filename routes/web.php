<?php

use App\Http\Controllers\ClickupApiController;
use App\Http\Controllers\ClickupUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing_page_folder.login');
});

Route::get('/dashboard', function(){
    return view('landing_page_folder.dashboard');
});

Route::post('/user-login/check', [
    ClickupUserController::class, 'check'
])->name('user#login-account');

Route::post('/user-register/store', [
    ClickupUserController::class, 'store'
])->name('user#register-new-account');

Route::post('/user-logout', [
    ClickupUserController::class, 'logout'
])->name('user#logout');

// Clickup API Routes

Route::get('/dashboard', [
    ClickupApiController::class, 'index'
])->name('dashboard');

Route::get('/clickup-spaces', [
    ClickupApiController::class, 'getTeams'
])->name('spaces');

Route::get('/clickup-folder', [
    ClickupApiController::class, 'getFolders'
])->name('folders');

Route::get('/clickup-lists', [
    ClickupAPIController::class, 'getLists'
])->name('lists');

Route::get('/clickup-tasks', [
    ClickupApiController::class, 'getTasks'
])->name('tasks');

Route::post('/create-space', [
    ClickupApiController::class, 'createSpace'
])->name('create#space');

Route::post('/create-folder', [
    ClickupApiController::class, 'createFolder'
])->name('create#folder');

Route::post('/create-list', [
    ClickupApiController::class, 'createList'
])->name('create#list');

Route::post('/create-task', [
    ClickupApiController::class, 'createTask'
])->name('create#task');