<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    // ใครคือคนที่โอนเงิน (ลูกหนี้)
public function debtor() {
    return $this->belongsTo(User::class, 'stl_debtor_id', 'user_id');
}

// ใครคือคนรับเงิน (เจ้าหนี้)
public function creditor() {
    return $this->belongsTo(User::class, 'stl_creditor_id', 'user_id');
}
}
