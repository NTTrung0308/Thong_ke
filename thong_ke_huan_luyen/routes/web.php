<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SoldierController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard (Bảo vệ bằng middleware auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('backend.index');
    })->name('dashboard');

    // Users Management - Chỉ dành cho Chỉ huy
    Route::resource('users', UserController::class)->middleware('check.role:chi-huy');

    // Soldiers Management
    Route::resource('soldiers', SoldierController::class);
});
