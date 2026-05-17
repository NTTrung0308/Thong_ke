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
    return redirect()->route('soldiers.index');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard (Bảo vệ bằng middleware auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('soldiers.index')->with('show_dashboard', true);
    })->name('dashboard');

    // Search function
    Route::get('/search', [SoldierController::class, 'search'])->name('search.index');

    // Users Management - Chỉ dành cho Chỉ huy
    Route::resource('users', UserController::class)->middleware('check.role:chi-huy');

    // Activity Logs - Chỉ dành cho Chỉ huy
    Route::get('/activity-logs', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index')->middleware('check.role:chi-huy');

    // Soldiers Management
    Route::get('soldiers/export-excel', [SoldierController::class, 'exportExcel'])->name('soldiers.export-excel');
    Route::get('soldiers/export-pdf', [SoldierController::class, 'exportPdf'])->name('soldiers.export-pdf');
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
    Route::get('training-results/export-excel', [TrainingResultController::class, 'exportExcel'])->name('training-results.export-excel');
    Route::get('training-results/export-pdf', [TrainingResultController::class, 'exportPdf'])->name('training-results.export-pdf');
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
