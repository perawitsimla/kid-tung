<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseItem extends Model
{
    // เมนูนี้อยู่ในบิลไหน
public function expense() {
    return $this->belongsTo(Expense::class, 'item_exp_id', 'exp_id');
}

// ใครเป็นคนจ่ายค่าเมนูนี้ (สำรองจ่าย)
public function payer() {
    return $this->belongsTo(User::class, 'item_payer_id', 'user_id');
}

// ดึงรายชื่อคนหารค่าเมนูนี้ทั้งหมด
public function splits() {
    return $this->hasMany(ItemSplit::class, 'is_item_id', 'item_id');
}
}
