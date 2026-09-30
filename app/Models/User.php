<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    // 1. ตั้งค่า Primary Key เป็น CHAR(8)
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    // 2. ตั้งค่าชื่อคอลัมน์ Timestamp ใหม่
    const CREATED_AT = 'user_createdAt';
    const UPDATED_AT = 'user_updatedAt';

    // 3. กำหนดคอลัมน์ที่อนุญาตให้บันทึกข้อมูล (แก้ให้ตรงกับ Database)
    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'user_password',
        'user_promptpay_no'
    ];

    // 4. ซ่อนรหัสผ่านไม่ให้แสดงตอนดึงข้อมูล API หรือ Query
    protected $hidden = [
        'user_password',
    ];

    // 5. บอกให้ระบบ Login ของ Laravel รู้ว่าช่องรหัสผ่านของเราชื่ออะไร
    public function getAuthPassword()
    {
        return $this->user_password;
    }

    protected function casts(): array
    {
        return [
            'user_password' => 'hashed',
        ];
    }

    // ==========================================
    // ส่วนของการผูกความสัมพันธ์ (Relationships)
    // ==========================================

    public function groups() {
        return $this->belongsToMany(Group::class, 'group_members', 'gm_user_id', 'gm_group_id')
                    ->withPivot('gm_role', 'gm_createdAt'); 
    }

    public function paidItems() {
        return $this->hasMany(ExpenseItem::class, 'item_payer_id', 'user_id');
    }

    public function splitItems() {
        return $this->hasMany(ItemSplit::class, 'is_user_id', 'user_id');
    }

    public function settlementsAsDebtor() {
        return $this->hasMany(Settlement::class, 'stl_debtor_id', 'user_id');
    }

    public function settlementsAsCreditor() {
        return $this->hasMany(Settlement::class, 'stl_creditor_id', 'user_id');
    }
}