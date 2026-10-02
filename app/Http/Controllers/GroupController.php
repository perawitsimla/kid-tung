<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Str; //คือไร
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    // GET /groups -> แสดงหน้ารายชื่อกลุ่มทั้งหมดที่ User คนนี้อยู่
   // เพิ่มไว้ด้านบนสุดของ GroupController
    public function index()
    {
        // ดึงข้อมูลกลุ่มทั้งหมดที่ user ล็อกอินอยู่เป็นสมาชิก
        // withCount('members') จะไปนับจำนวนเพื่อนในตาราง group_members ให้อัตโนมัติ
        $groups = \Illuminate\Support\Facades\Auth::user()
                    ->groups()
                    ->withCount('members')
                    ->orderByDesc('group_createdAt')
                    ->get();

        return view('groups.index', compact('groups'));
    }

    // GET /groups/create -> แสดงหน้าฟอร์มสร้างกลุ่มใหม่
    public function create()
    {
        return view('groups.create');
    }

    public function checkUser(Request $request)
    {
        $username = $request->user_name;
        
        // เช็คว่ากรอกชื่อตัวเองมาหรือไม่
        if ($username === \Illuminate\Support\Facades\Auth::user()->user_name) {
            return response()->json(['found' => false, 'message' => 'คุณอยู่ในกลุ่มนี้แล้ว']);
        }

        $user = \App\Models\User::where('user_name', $username)->first();

        if ($user) {
            return response()->json(['found' => true, 'user_name' => $user->user_name]);
        }

        return response()->json(['found' => false, 'message' => 'ไม่พบผู้ใช้งานนี้ในระบบ']);
    }

    // POST /groups -> รับข้อมูลจากฟอร์มมาบันทึกลง Database
    public function store(Request $request)
    {
        // 1. ตรวจสอบข้อมูลจากฟอร์ม
        $request->validate([
            'group_name' => ['required', 'string', 'max:100'],
            'members' => ['nullable', 'array'],
        ]);

        $groupId = Str::random(8); // สร้างรหัสกลุ่ม 8 ตัวอักษร

        // 2. บันทึกข้อมูลลงตาราง groups
        Group::create([
            'group_id' => $groupId,
            'group_name' => $request->group_name,
        ]);

        // 3. บันทึก "คนสร้างกลุ่ม" ลงตาราง group_members (ตัวคุณเอง)
        DB::table('group_members')->insert([
            'gm_id' => Str::random(8), // สร้าง PK 8 ตัวอักษร
            'gm_group_id' => $groupId,
            'gm_user_id' => Auth::user()->user_id,
            'gm_role' => 'creator',
            'gm_createdAt' => now(),
            'gm_updatedAt' => now(),
        ]);

        // 4. บันทึก "เพื่อน" ลงตาราง group_members
        if ($request->has('members')) {
            $memberUsers = User::whereIn('user_name', $request->members)->get();
            
            $membersData = [];
            foreach ($memberUsers as $user) {
                $membersData[] = [
                    'gm_id' => Str::random(8),
                    'gm_group_id' => $groupId,
                    'gm_user_id' => $user->user_id,
                    'gm_role' => 'member',
                    'gm_createdAt' => now(),
                    'gm_updatedAt' => now(),
                ];
            }
            
            // Insert ทีเดียวรวดเดียว (Performance ดีกว่า)
            if (!empty($membersData)) {
                DB::table('group_members')->insert($membersData);
            }
        }

        return redirect()->route('dashboard')->with('success', 'สร้างกลุ่มและเพิ่มสมาชิกสำเร็จแล้ว!');
    }



    public function show($id)
    {
        $group = \App\Models\Group::with('members')->findOrFail($id);

        // ดึงข้อมูลรายการบิล (expenses) พร้อมข้อมูลราคา (expense_items) และชื่อคนจ่าย (users)
        $expenses = \Illuminate\Support\Facades\DB::table('expenses')
            ->join('expense_items', 'expenses.exp_id', '=', 'expense_items.item_exp_id')
            ->join('users', 'expense_items.item_payer_id', '=', 'users.user_id')
            ->where('expenses.exp_group_id', $id)
            ->select(
                'expenses.exp_id',
                'expenses.exp_title',
                'expense_items.item_price',
                'users.user_name as payer_name',
                'expenses.exp_createdAt'
            )
            ->orderBy('expenses.exp_createdAt', 'desc')
            ->get();

        // ดึงข้อมูลว่าใครหารบ้าง (item_splits) แล้วจัดกลุ่มตามรหัสบิล (exp_id)
        $splits = \Illuminate\Support\Facades\DB::table('item_splits')
            ->join('expense_items', 'item_splits.is_item_id', '=', 'expense_items.item_id')
            ->join('expenses', 'expense_items.item_exp_id', '=', 'expenses.exp_id')
            ->join('users', 'item_splits.is_user_id', '=', 'users.user_id')
            ->where('expenses.exp_group_id', $id)
            ->select('expenses.exp_id', 'users.user_name', 'item_splits.is_amount')
            ->get()
            ->groupBy('exp_id');

        return view('groups.show', compact('group', 'expenses', 'splits'));
    }

    // สร้างฟังก์ชันเปล่าเตรียมไว้สำหรับหน้า "สรุปการโอนเงิน"
    public function summary($id)
    {
        return "นี่คือหน้าสรุปการโอนเงินของกลุ่ม: " . $id;
    }

    public function removeMember($groupId, $userId)
    {
        // ลบข้อมูลจากตาราง group_members
        \Illuminate\Support\Facades\DB::table('group_members')
            ->where('gm_group_id', $groupId)
            ->where('gm_user_id', $userId)
            ->delete();

        return back()->with('success', 'ลบสมาชิกออกจากกลุ่มเรียบร้อยแล้ว');
    }

    public function createItem($id)
    {
        // ดึงข้อมูลกลุ่มและสมาชิกเพื่อเอาไปแสดงในหน้าฟอร์มสร้างรายการ
        $group = \App\Models\Group::with('members')->findOrFail($id);
        return view('groups.create-item', compact('group'));
    }

    // POST /groups/{group}/members -> เพิ่มสมาชิกใหม่จากหน้า Group Info
    public function addMember(Request $request, $id)
    {
        // 1. ตรวจสอบว่ามีการกรอกชื่อเข้ามา
        $request->validate(['user_name' => 'required|string']);
        
        // 2. ค้นหา User ในระบบ
        $user = User::where('user_name', $request->user_name)->first();
        
        if (!$user) {
            return back()->withErrors(['user_name' => 'ไม่พบผู้ใช้นี้ในระบบ']);
        }

        // 3. เช็คว่า user คนนี้อยู่ในกลุ่มนี้หรือยัง ป้องกันการเพิ่มซ้ำ
        $exists = DB::table('group_members')
                    ->where('gm_group_id', $id)
                    ->where('gm_user_id', $user->user_id)
                    ->exists();

        if (!$exists) {
            // 4. บันทึกลงตาราง group_members ด้วยโครงสร้าง Custom ของคุณ
            DB::table('group_members')->insert([
                'gm_id' => Str::random(8),
                'gm_group_id' => $id,
                'gm_user_id' => $user->user_id,
                'gm_role' => 'member',
                'gm_createdAt' => now(),
                'gm_updatedAt' => now(),
            ]);
        }

        return back()->with('success', 'เพิ่มสมาชิกลงกลุ่มเรียบร้อยแล้ว');
    }

    // POST /groups/{group}/expenses -> รับข้อมูลจากหน้า Add Expense
    public function addExpense(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'price' => 'required|numeric|min:0',
            'paid_by' => 'required|string',
            'split_users' => 'required|array',
        ]);

        DB::transaction(function () use ($request, $id) {
            $expId = Str::random(8);
            
            // 1. สร้างหัวบิล (expenses)
            DB::table('expenses')->insert([
                'exp_id' => $expId,
                'exp_group_id' => $id,
                'exp_title' => $request->title,
                'exp_createdAt' => now(),
                'exp_updatedAt' => now(),
            ]);

            // 2. สร้างรายการย่อย (expense_items) ผูกกับคนจ่าย
            $itemId = Str::random(8);
            DB::table('expense_items')->insert([
                'item_id' => $itemId,
                'item_exp_id' => $expId,
                'item_payer_id' => $request->paid_by,
                'item_name' => $request->title,
                'item_price' => $request->price,
                'item_createdAt' => now(),
                'item_updatedAt' => now(),
            ]);

            // 3. สร้างข้อมูลการหาร (item_splits) ตามคนที่ถูกติ๊ก Checkbox
            $splitCount = count($request->split_users);
            $splitAmount = $request->price / $splitCount; // คำนวณหารเท่า

            $splits = [];
            foreach ($request->split_users as $userId) {
                $splits[] = [
                    'is_id' => Str::random(8),
                    'is_item_id' => $itemId,
                    'is_user_id' => $userId,
                    'is_amount' => $splitAmount,
                    'is_split_method' => 'equal', // กำหนดเป็นหารเท่าไปก่อน
                    'is_createdAt' => now(),
                    'is_updatedAt' => now(),
                ];
            }
            DB::table('item_splits')->insert($splits);
        });

        // บันทึกเสร็จให้เด้งกลับไปหน้า Group Info
        return redirect()->route('groups.show', $id)->with('success', 'เพิ่มรายการสำเร็จ');
    }
}
