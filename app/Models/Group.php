<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Import SoftDeletes

class Group extends Model
{
    use SoftDeletes; // 2. เรียกใช้งาน SoftDeletes

    protected $table = 'groups';

    // 1. ตั้งค่า Primary Key เป็น CHAR(8)
    protected $primaryKey = 'group_id';
    public $incrementing = false;
    protected $keyType = 'string';

    // 2. ตั้งค่าชื่อคอลัมน์ Timestamp และ SoftDeletes ใหม่
    const CREATED_AT = 'group_createdAt';
    const UPDATED_AT = 'group_updatedAt';
    const DELETED_AT = 'group_deletedAt'; // 3. เพิ่มคอลัมน์สำหรับ SoftDeletes

    // 3. อนุญาตให้บันทึกข้อมูลในคอลัมน์เหล่านี้ได้
    protected $fillable = [
        'group_id', 
        'group_name'
        // ลบ created_by ออกไปแล้ว
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