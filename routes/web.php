<?php

use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\TraderController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// لوحة التحكم
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

// المسارات التي تتطلب مصادقة
Route::middleware('auth')->group(function () {

    // --- مسارات المعاملات وسير العمل والمراحل الست ---
    Route::get('/transactions/workflow', [TransactionController::class, 'workflow'])->name('transactions.workflow');
    Route::get('/transactions/completed', [TransactionController::class, 'completed'])->name('transactions.completed');
    Route::post('/transactions/{transaction}/stage/{stage}', [TransactionController::class, 'updateStage'])->name('transactions.update-stage');
    Route::get('/transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
    Route::get('/transactions/reports', [ReportController::class, 'index'])->name('transactions.reports');

    Route::resource('transactions', TransactionController::class);

    // --- موارد الإدارة الأساسية ---
    Route::resource('companies', CompanyController::class);
    Route::resource('traders', TraderController::class);
    Route::resource('statuses', StatusController::class);

    // --- مسارات الملف الشخصي ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// إدارة المستخدمين والأدوار للمدراء وممن لديهم صلاحية
Route::middleware(['auth'])->group(function () {
    Route::resource('roles', RoleController::class);
    Route::post('/roles-ajax', [RoleController::class, 'storeAjax'])->name('roles.store.ajax');
    Route::resource('users', UserController::class);
});
Route::middleware(['auth'])->group(function () {
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/export-pdf', [AuditLogController::class, 'exportPdf'])->name('audit-logs.export-pdf');
});

require __DIR__ . '/auth.php';
