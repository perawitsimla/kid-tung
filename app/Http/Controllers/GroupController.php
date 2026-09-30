<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GroupController extends Controller
{
    namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    // GET /groups -> แสดงหน้ารายชื่อกลุ่มทั้งหมดที่ User คนนี้อยู่
    public function index()
    {
        // 1. ดึงข้อมูล User ปัจจุบันที่ Login อยู่
        $user = Auth::user();
        
        // 2. ดึงกลุ่มทั้งหมดที่ User คนนี้เป็นสมาชิก (ใช้ relation 'groups' ที่เราผูกไว้)
        $groups = $user->groups; 
        
        // 3. ส่งตัวแปร $groups ไปแสดงผลที่ไฟล์ view resources/views/groups/index.blade.php
        return view('groups.index', compact('groups'));
    }

    // GET /groups/create -> แสดงหน้าฟอร์มสร้างกลุ่มใหม่
    public function create()
    {
        return view('groups.create');
    }

    // POST /groups -> รับข้อมูลจากฟอร์มมาบันทึกลง Database
    public function store(Request $request)
    {
        // 1. ตรวจสอบความถูกต้องของข้อมูล (Validation)
        $request->validate([
            'group_name' => 'required|string|max:255',
        ]);

        // 2. สร้างกลุ่มใหม่
        $group = new Group();
        $group->group_name = $request->group_name;
        // หากมี group_cover_image ก็จัดการอัปโหลดและเซฟตรงนี้
        $group->save();

        // 3. ดึง User ที่สร้างกลุ่มนี้ เข้าไปเป็นสมาชิกกลุ่มทันที (ตาราง group_members)
        $group->members()->attach(Auth::id(), ['gm_role' => 'admin']);

        // 4. บันทึกเสร็จให้ Redirect กลับไปหน้ารวมกลุ่ม
        return redirect()->route('groups.index')->with('success', 'สร้างกลุ่มสำเร็จ!');
    }

    // GET /groups/{id} -> แสดงรายละเอียดของกลุ่ม (บิลทั้งหมด, สมาชิกทั้งหมด)
    public function show($id)
    {
        // ดึงข้อมูลกลุ่ม พร้อมกับรายการบิลและสมาชิก
        $group = Group::with(['expenses', 'members'])->findOrFail($id);
        
        return view('groups.show', compact('group'));
    }

    // POST /groups/{id}/members -> เพิ่มเพื่อนเข้ากลุ่ม
    public function addMember(Request $request, $id)
    {
        // 1. ตรวจสอบว่ากรอกอีเมลมาไหม
        $request->validate(['email' => 'required|email']);

        // 2. ค้นหา User จากอีเมล
        $userToAdd = User::where('user_email', $request->email)->first();

        if (!$userToAdd) {
            return back()->withErrors(['email' => 'ไม่พบผู้ใช้งานอีเมลนี้ในระบบ']);
        }

        // 3. เพิ่ม User ลงในกลุ่ม
        $group = Group::findOrFail($id);
        
        // เช็คก่อนว่าอยู่ในกลุ่มอยู่แล้วหรือเปล่า (ถ้าใช้ syncWithoutDetaching ก็ได้)
        if (!$group->members->contains($userToAdd->user_id)) {
            $group->members()->attach($userToAdd->user_id, ['gm_role' => 'member']);
        }

        return back()->with('success', 'เพิ่มสมาชิกเรียบร้อย');
    }
}
}
