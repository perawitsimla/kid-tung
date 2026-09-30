<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Expense;
use App\Models\ExpenseItem;
use App\Models\ItemSplit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    // GET /groups/{group_id}/expenses/create -> หน้าฟอร์มสร้างบิล
    public function create($group_id)
    {
        // ดึงข้อมูลกลุ่มเพื่อเอาไปแสดงชื่อกลุ่ม และเอาสมาชิกกลุ่มไปทำตัวเลือกคนจ่าย/คนหาร
        $group = Group::with('members')->findOrFail($group_id);
        
        return view('expenses.create', compact('group'));
    }

    // POST /groups/{group_id}/expenses -> เซฟบิลลง Database
    public function store(Request $request, $group_id)
    {
        // ** ข้อแนะนำ: ควรใช้ DB Transaction เพราะเรามีการบันทึกลงหลายตาราง 
        // ถ้าตารางไหนบันทึกพลาด จะได้ Rollback (ยกเลิก) ทั้งหมด ป้องกันข้อมูลแหว่ง
        
        DB::beginTransaction();

        try {
            // 1. สร้างบิลหลัก (ตาราง expenses)
            $expense = new Expense();
            $expense->exp_group_id = $group_id;
            $expense->exp_name = $request->exp_name;
            $expense->exp_date = now(); // หรือรับจากฟอร์ม
            // $expense->exp_receipt_image = ... (ถ้ามีการอัปโหลดสลิป)
            $expense->save();

            // 2. วนลูปบันทึกเมนูย่อย (ตาราง expense_items)
            // สมมติว่าหน้าเว็บส่ง Array ของ items มา
            foreach ($request->items as $itemData) {
                $item = new ExpenseItem();
                $item->item_exp_id = $expense->exp_id;
                $item->item_payer_id = $itemData['payer_id']; // คนที่สำรองจ่ายเมนูนี้
                $item->item_name = $itemData['name'];
                $item->item_price = $itemData['price'];
                $item->save();

                // 3. วนลูปบันทึกคนหารในเมนูนั้นๆ (ตาราง item_splits)
                foreach ($itemData['splits'] as $splitData) {
                    $split = new ItemSplit();
                    $split->is_item_id = $item->item_id;
                    $split->is_user_id = $splitData['user_id']; // คนที่ต้องหาร
                    $split->is_amount = $splitData['amount'];   // จำนวนเงินที่ต้องจ่าย
                    $split->save();
                }
            }

            DB::commit(); // บันทึกข้อมูลทั้งหมดลงฐานข้อมูลจริง
            
            return redirect()->route('groups.show', $group_id)->with('success', 'เพิ่มบิลเรียบร้อย!');

        } catch (\Exception $e) {
            DB::rollBack(); // ถ้าระหว่างเซฟมี Error ให้ยกเลิกทั้งหมด
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage()]);
        }
    }

    // GET /expenses/{id} -> ดูรายละเอียดบิล 1 ใบแบบเจาะลึก
    public function show($id)
    {
        // ดึงบิล พร้อมกับดึงเมนูย่อย คนจ่าย และคนหารทั้งหมดออกมาทีเดียว (Eager Loading)
        $expense = Expense::with(['items.payer', 'items.splits.user'])->findOrFail($id);
        
        return view('expenses.show', compact('expense'));
    }
}
}
