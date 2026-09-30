<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    // บิลนี้เป็นของกลุ่มไหน
public function group() {
    return $this->belongsTo(Group::class, 'exp_group_id', 'group_id');
}

// ดึงเมนูย่อยทั้งหมดในบิลนี้
public function items() {
    return $this->hasMany(ExpenseItem::class, 'item_exp_id', 'exp_id');
}
}
