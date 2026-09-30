<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. ดึงข้อมูล User ที่กำลังล็อกอินอยู่
        $user = Auth::user();

        // 2. ดึงข้อมูลกลุ่มทั้งหมดที่ User คนนี้เป็นสมาชิกอยู่ (ผ่าน Relationship ที่ตั้งไว้)
        $recentGroups = $user->groups;

        // 3. จำลองตัวเลขหนี้สินไปก่อน (เดี๋ยวเราค่อยมาเขียน Query คำนวณทีหลัง)
        $totalOwed = 1500.50; 
        $totalLent = 500.00;

        // 4. ส่งข้อมูลทั้งหมดไปแสดงที่ resources/views/dashboard.blade.php
        return view('dashboard', compact('user', 'recentGroups', 'totalOwed', 'totalLent'));
    }
}