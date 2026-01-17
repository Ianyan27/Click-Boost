<?php

use App\Http\Controllers\ClickUpAuthController;
use App\Http\Controllers\ClickupApiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('landing_page_folder.login');
});

Route::get('/', [
    ClickUpAuthController::class, 'showLoginForm'
]);

Route::get('/login', [ClickUpAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [ClickUpAuthController::class, 'login'])->name('user.login');
Route::post('/logout', [ClickUpAuthController::class, 'logout'])->name('logout');

// Admin: Manual Sync Route
Route::post('/admin/sync-members', [ClickUpAuthController::class, 'syncMembers'])->name('admin.sync.members');

// Protected Routes
Route::middleware(['clickup.auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Session::get('user');
        return view('dashboard', compact('user'));
    })->name('dashboard');
    
    // Add more protected routes here
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

    Route::delete('/clickup/delete', [
        ClickupAPiController::class, 'delete'
    ])->name('delete');

});


