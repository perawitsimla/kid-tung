<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settlement;
use Illuminate\Support\Facades\Auth;

class SettlementController extends Controller
{
    // POST /groups/{group_id}/settlements -> บันทึกการเคลียร์เงินคืน
    public function store(Request $request, $group_id)
    {
        // 1. ตรวจสอบข้อมูลที่ส่งมาจากฟอร์ม
        $request->validate([
            'creditor_id' => 'required|exists:users,user_id', // ต้องระบุว่าจ่ายคืนให้ใคร
            'amount'      => 'required|numeric|min:1',        // จำนวนเงินต้องมากกว่า 0
            // 'slip_image' => 'nullable|image', // ออปชันเสริม ถ้าต้องการให้อัปโหลดสลิป
        ]);

        // 2. สร้างบันทึกการโอนเงิน
        $settlement = new Settlement();
        // กรณีที่เราเก็บ group_id ในตาราง settlements ด้วย (แนะนำให้เพิ่มคอลัมน์นี้เพื่อแยกหนี้ตามกลุ่ม)
        // $settlement->stl_group_id = $group_id; 
        
        $settlement->stl_debtor_id = Auth::id(); // คนที่กำลังล็อกอินและกดโอน = ลูกหนี้
        $settlement->stl_creditor_id = $request->creditor_id; // คนที่รับโอน = เจ้าหนี้
        $settlement->stl_amount = $request->amount;
        $settlement->stl_date = now(); // บันทึกเวลาปัจจุบัน
        
        // ถ้ามีการอัปโหลดสลิป ก็เขียนโค้ดเซฟไฟล์ที่นี่ แล้วเก็บชื่อไฟล์ลง Database

        $settlement->save();

        // 3. กลับไปยังหน้าเดิม (หน้ากลุ่ม) พร้อมแจ้งเตือนว่าสำเร็จ
        return back()->with('success', 'บันทึกการเคลียร์เงินเรียบร้อยแล้ว!');
    }
}
