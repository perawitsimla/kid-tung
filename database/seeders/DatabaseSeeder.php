<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; // <-- อย่าลืม import ตัวนี้สำหรับสุ่มรหัส 8 ตัวอักษร

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. สร้าง User หลักสำหรับให้เราใช้ Login เข้าไปดู Dashboard
        $me = User::create([
            'user_id'       => Str::random(8), // สุ่มรหัส CHAR(8)
            'user_name'     => 'Me (แอดมิน)',
            'user_email'    => 'test@example.com',
            'user_password' => Hash::make('password'),
        ]);

        // 2. สร้าง User เพื่อนในทีม เพื่อเอาไว้จำลองการหารเงิน
        $friend1 = User::create([
            'user_id'       => Str::random(8), // สุ่มรหัส CHAR(8)
            'user_name'     => 'สมชาย สายเปย์',
            'user_email'    => 'somchai@example.com',
            'user_password' => Hash::make('password'),
        ]);

        $friend2 = User::create([
            'user_id'       => Str::random(8), // สุ่มรหัส CHAR(8)
            'user_name'     => 'สมหญิง รักการกิน',
            'user_email'    => 'somying@example.com',
            'user_password' => Hash::make('password'),
        ]);

        // 3. สร้างกลุ่มตัวอย่าง 3 กลุ่ม
        $group1 = Group::create([
            'group_id'   => Str::random(8), // สุ่มรหัส CHAR(8)
            'group_name' => 'ทริปเชียงใหม่'
        ]);
        
        $group2 = Group::create([
            'group_id'   => Str::random(8),
            'group_name' => 'ค่าหอพัก เดือนนี้'
        ]);
        
        $group3 = Group::create([
            'group_id'   => Str::random(8),
            'group_name' => 'บุฟเฟต์หมูกระทะ'
        ]);

        // 4. จับ User เข้ากลุ่ม (บันทึกลงตารางเชื่อม group_members)
        // กลุ่มที่ 1: เราเป็น admin, สมชายเป็น member
        $group1->members()->attach([
            $me->user_id => [
                'gm_id' => Str::random(8), 
                'gm_role' => 'admin', 
                'gm_createdAt' => now()
            ],
            $friend1->user_id => [
                'gm_id' => Str::random(8), 
                'gm_role' => 'member', 
                'gm_createdAt' => now()
            ],
        ]);

        // กลุ่มที่ 2: สมหญิงเป็น admin, เราเป็น member
        $group2->members()->attach([
            $friend2->user_id => [
                'gm_id' => Str::random(8), 
                'gm_role' => 'admin', 
                'gm_createdAt' => now()
            ],
            $me->user_id => [
                'gm_id' => Str::random(8), 
                'gm_role' => 'member', 
                'gm_createdAt' => now()
            ],
        ]);

        // กลุ่มที่ 3: อยู่รวมกันทั้ง 3 คน
        $group3->members()->attach([
            $me->user_id => [
                'gm_id' => Str::random(8), 
                'gm_role' => 'admin', 
                'gm_createdAt' => now()
            ],
            $friend1->user_id => [
                'gm_id' => Str::random(8), 
                'gm_role' => 'member', 
                'gm_createdAt' => now()
            ],
            $friend2->user_id => [
                'gm_id' => Str::random(8), 
                'gm_role' => 'member', 
                'gm_createdAt' => now()
            ],
        ]);
    }
}