<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SettlementController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// หน้าแรกสุด (ก่อน Login)
Route::get('/', function () {
    return view('welcome'); // อาจจะเป็นหน้า Landing Page แนะนำแอป
});

// กลุ่ม Route ที่ต้อง Login ก่อนถึงจะใช้งานได้
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Groups
    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');

    Route::post('/groups/check-user', [App\Http\Controllers\GroupController::class, 'checkUser'])->name('groups.check-user');
    Route::get('/groups', [App\Http\Controllers\GroupController::class, 'index'])->name('groups.index');
    
    // ลบสมาชิกออกจากกลุ่ม
    Route::delete('/groups/{group}/members/{user}', [App\Http\Controllers\GroupController::class, 'removeMember'])->name('groups.members.destroy');

    // หน้าฟอร์มเพิ่มรายการบิล (Add Item)
    Route::get('/groups/{group}/items/create', [App\Http\Controllers\GroupController::class, 'createItem'])->name('groups.items.create');

    // จัดการสมาชิกและบิลในกลุ่ม
    Route::post('/groups/{group}/members', [App\Http\Controllers\GroupController::class, 'addMember'])->name('groups.members.store');
    Route::post('/groups/{group}/expenses', [App\Http\Controllers\GroupController::class, 'addExpense'])->name('groups.expenses.store');

    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{id}', [GroupController::class, 'show'])->name('groups.show');
    Route::post('/groups/{id}/members', [GroupController::class, 'addMember'])->name('groups.members.add');
    //แก้ไข
    Route::put('/groups/{group}/expenses/{expense}', [App\Http\Controllers\GroupController::class, 'updateExpense'])->name('groups.expenses.update');
    // Expenses
    Route::get('/groups/{group_id}/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/groups/{group_id}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{id}', [ExpenseController::class, 'show'])->name('expenses.show');
    // ลบรายการบิล
    Route::delete('/groups/{group}/expenses/{expense}', [App\Http\Controllers\GroupController::class, 'destroyExpense'])->name('groups.expenses.destroy');
    Route::delete('/groups/{group}/expenses/{expense}', [GroupController::class, 'destroyExpense'])->name('groups.expenses.destroy');
    Route::get('/groups/{group}/expenses/{expense}/edit', [App\Http\Controllers\GroupController::class, 'editExpense'])->name('groups.expenses.edit');
    // Settlements
    Route::post('/groups/{group_id}/settlements', [SettlementController::class, 'store'])->name('settlements.store');
    Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    
    // เรียกใช้ session ผ่าน method ของ Request ที่ถูกต้อง
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
})->name('logout');
});