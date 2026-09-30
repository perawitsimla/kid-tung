<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SettlementController;

// หน้าแรกสุด (ก่อน Login)
Route::get('/', function () {
    return view('welcome'); // อาจจะเป็นหน้า Landing Page แนะนำแอป
});

// กลุ่ม Route ที่ต้อง Login ก่อนถึงจะใช้งานได้
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Groups
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{id}', [GroupController::class, 'show'])->name('groups.show');
    Route::post('/groups/{id}/members', [GroupController::class, 'addMember'])->name('groups.members.add');

    // Expenses
    Route::get('/groups/{group_id}/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/groups/{group_id}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{id}', [ExpenseController::class, 'show'])->name('expenses.show');

    // Settlements
    Route::post('/groups/{group_id}/settlements', [SettlementController::class, 'store'])->name('settlements.store');

});
