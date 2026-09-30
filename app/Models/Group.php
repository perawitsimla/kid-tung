<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'groups';

    // 1. ตั้งค่า Primary Key เป็น CHAR(8)
    protected $primaryKey = 'group_id';
    public $incrementing = false;
    protected $keyType = 'string';

    // 2. ตั้งค่าชื่อคอลัมน์ Timestamp ใหม่
    const CREATED_AT = 'group_createdAt';
    const UPDATED_AT = 'group_updatedAt';

    // 3. อนุญาตให้บันทึกข้อมูลในคอลัมน์เหล่านี้ได้
    protected $fillable = [
        'group_id', 
        'group_name'
    ];

    // ==========================================
    // ส่วนของการผูกความสัมพันธ์ (Relationships)
    // ==========================================

    // ดึงสมาชิกทุกคนในกลุ่ม
    public function members() {
        return $this->belongsToMany(User::class, 'group_members', 'gm_group_id', 'gm_user_id')
                    ->withPivot('gm_role', 'gm_createdAt');
    }

    // ดึงรายละเอียดการเป็นสมาชิก (ข้อมูลในตาราง group_members)
    public function groupMemberships() {
        return $this->hasMany(GroupMember::class, 'gm_group_id', 'group_id');
    }

    // ดึงบิลทั้งหมดที่อยู่ในกลุ่มนี้
    public function expenses() {
        return $this->hasMany(Expense::class, 'exp_group_id', 'group_id');
    }
}