<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\Ajax\SoldierAjaxController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DisciplineController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RewardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SoldierController;
use App\Http\Controllers\Admin\TrainingLogController;
use App\Http\Controllers\Admin\TrainingResultController;
use App\Http\Controllers\Admin\TrainingSubjectController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WeaponEquipmentController;
use App\Http\Controllers\AuthController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/units-by-level', [DashboardController::class, 'getUnitsByLevel'])->name('api.units-by-level');
    Route::get('/api/units-tree', [DashboardController::class, 'getTreeData'])->name('api.units-tree');
    Route::get('/api/dashboard-stats', [DashboardController::class, 'dashboardStats'])->name('api.dashboard-stats');

    // Notifications
    Route::post('/notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::get('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');

    // Search function
    Route::get('/search', [SoldierController::class, 'search'])->name('search.index');
    Route::get('/search/quick-view/{soldier}', [SoldierAjaxController::class, 'quickView'])->name('search.quick-view');

    // Users Management - Chỉ dành cho Chỉ huy
    Route::resource('users', UserController::class)->middleware('check.role:chi-huy');

    // Activity Logs - Chỉ dành cho Chỉ huy
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index')->middleware('check.role:chi-huy');

    // Roles & Permissions Management
    Route::resource('roles', RoleController::class)->middleware('check.role:chi-huy');
    Route::resource('permissions', PermissionController::class)->only(['index', 'store', 'destroy'])->middleware('check.role:chi-huy');

    // Soldiers Management
    Route::post('soldiers/bulk-action', [SoldierController::class, 'bulkAction'])->name('soldiers.bulk-action');
    Route::post('soldiers/import', [SoldierController::class, 'importExcel'])->name('soldiers.import');
    Route::get('soldiers/download-template', [SoldierController::class, 'downloadTemplate'])->name('soldiers.download-template');
    Route::get('soldiers/export-excel', [SoldierController::class, 'exportExcel'])->name('soldiers.export-excel');
    Route::get('soldiers/export-pdf', [SoldierController::class, 'exportPdf'])->name('soldiers.export-pdf');
    Route::resource('soldiers', SoldierController::class);

    // Units Management
    Route::get('units/children/{parentId?}', [UnitController::class, 'getChildren'])->name('units.getChildren');
    Route::resource('units', UnitController::class)->middleware('check.role:chi-huy');

    // Weapon & Equipment Management
    Route::post('weapon-equipments/bulk-action', [WeaponEquipmentController::class, 'bulkAction'])->name('weapon-equipments.bulk-action');
    Route::get('weapon-equipments/export-excel', [WeaponEquipmentController::class, 'exportExcel'])->name('weapon-equipments.export-excel');
    Route::get('weapon-equipments/export-pdf', [WeaponEquipmentController::class, 'exportPdf'])->name('weapon-equipments.export-pdf');
    Route::resource('weapon-equipments', WeaponEquipmentController::class);

    // Rewards Management
    Route::get('rewards/export-excel', [RewardController::class, 'exportExcel'])->name('rewards.export-excel');
    Route::get('rewards/export-pdf', [RewardController::class, 'exportPdf'])->name('rewards.export-pdf');
    Route::get('rewards/report', [RewardController::class, 'report'])->name('rewards.report');
    Route::resource('rewards', RewardController::class);

    // Discipline Management
    Route::get('disciplines/export-excel', [DisciplineController::class, 'exportExcel'])->name('disciplines.export-excel');
    Route::get('disciplines/export-pdf', [DisciplineController::class, 'exportPdf'])->name('disciplines.export-pdf');
    Route::get('disciplines/report', [DisciplineController::class, 'report'])->name('disciplines.report');
    Route::resource('disciplines', DisciplineController::class);

    // Training Results Management
    Route::get('training-results/export-excel', [TrainingResultController::class, 'exportExcel'])->name('training-results.export-excel');
    Route::get('training-results/export-pdf', [TrainingResultController::class, 'exportPdf'])->name('training-results.export-pdf');
    Route::get('training-results/report', [TrainingResultController::class, 'report'])->name('training-results.report');
    Route::resource('training-results', TrainingResultController::class);

    // Training Logs Management
    Route::get('training-logs/export-excel', [TrainingLogController::class, 'exportExcel'])->name('training-logs.export-excel');
    Route::get('training-logs/export-pdf', [TrainingLogController::class, 'exportPdf'])->name('training-logs.export-pdf');
    Route::get('training-logs/report', [TrainingLogController::class, 'report'])->name('training-logs.report');
    Route::resource('training-logs', TrainingLogController::class);

    // Training Subjects Management
    Route::get('training-subjects/children/{parentId}', [TrainingSubjectController::class, 'getChildren'])->name('training-subjects.children');
    Route::resource('training-subjects', TrainingSubjectController::class);

    // Profile & Settings
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});
