<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SoldierController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\WeaponEquipmentController;
use App\Http\Controllers\Admin\RewardController;
use App\Http\Controllers\Admin\DisciplineController;
use App\Http\Controllers\Admin\TrainingResultController;
use App\Http\Controllers\Admin\TrainingLogController;
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
    return redirect()->route('dashboard');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard (Bảo vệ bằng middleware auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('backend.dashboard');
    })->name('dashboard');

    // Search function
    Route::get('/search', [SoldierController::class, 'search'])->name('search.index');

    // Users Management - Chỉ dành cho Chỉ huy
    Route::resource('users', UserController::class)->middleware('check.role:chi-huy');

    // Soldiers Management
    Route::resource('soldiers', SoldierController::class);

    // Units Management
    Route::get('units/children/{parentId?}', [UnitController::class, 'getChildren'])->name('units.getChildren');
    Route::resource('units', UnitController::class)->middleware('check.role:chi-huy');

    // Weapon & Equipment Management
    Route::resource('weapon-equipments', WeaponEquipmentController::class);

    // Rewards Management
    Route::get('rewards/report', [RewardController::class, 'report'])->name('rewards.report');
    Route::resource('rewards', RewardController::class);

    // Discipline Management
    Route::get('disciplines/report', [DisciplineController::class, 'report'])->name('disciplines.report');
    Route::resource('disciplines', DisciplineController::class);

    // Training Results Management
    Route::get('training-results/report', [TrainingResultController::class, 'report'])->name('training-results.report');
    Route::resource('training-results', TrainingResultController::class);

    // Training Logs Management
    Route::get('training-logs/report', [TrainingLogController::class, 'report'])->name('training-logs.report');
    Route::resource('training-logs', TrainingLogController::class);

    // Profile & Settings
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});
