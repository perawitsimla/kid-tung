<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemSplit extends Model
{
    // ส่วนแบ่งนี้เป็นของเมนูไหน
public function item() {
    return $this->belongsTo(ExpenseItem::class, 'is_item_id', 'item_id');
}

// ใครคือคนที่ต้องจ่ายส่วนแบ่งนี้
public function user() {
    return $this->belongsTo(User::class, 'is_user_id', 'user_id');
}
}
