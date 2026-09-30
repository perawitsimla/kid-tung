<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    // ข้อมูล User ของสมาชิกนี้
public function user() {
    return $this->belongsTo(User::class, 'gm_user_id', 'user_id');
}

// ข้อมูล Group ของสมาชิกนี้
public function group() {
    return $this->belongsTo(Group::class, 'gm_group_id', 'group_id');
}
}
