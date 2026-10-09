<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->user_id;

        // 1. คุณติดหนี้คนอื่น (คำนวณจากสิ่งที่เราหาร แต่ไม่ได้เป็นคนจ่าย)
        $myDebts = DB::table('item_splits')
            ->join('expense_items', 'item_splits.is_item_id', '=', 'expense_items.item_id')
            ->join('users as creditors', 'expense_items.item_payer_id', '=', 'creditors.user_id')
            ->where('item_splits.is_user_id', $userId)
            ->where('expense_items.item_payer_id', '!=', $userId)
            ->select('item_splits.is_amount', 'creditors.user_name as creditor_name', 'expense_items.item_name')
            ->orderBy('item_splits.is_createdAt', 'desc')
            ->get();
        
        $totalDebt = $myDebts->sum('is_amount');
        $latestDebt = $myDebts->first(); // ดึงรายการล่าสุดมาโชว์ชื่อเพื่อน

        // 2. คนอื่นติดหนี้คุณ (คำนวณจากสิ่งที่เราจ่าย แต่คนอื่นหาร)
        $myCredits = DB::table('item_splits')
            ->join('expense_items', 'item_splits.is_item_id', '=', 'expense_items.item_id')
            ->join('users as debtors', 'item_splits.is_user_id', '=', 'debtors.user_id')
            ->where('expense_items.item_payer_id', $userId)
            ->where('item_splits.is_user_id', '!=', $userId)
            ->select('item_splits.is_amount', 'debtors.user_name as debtor_name')
            ->orderBy('item_splits.is_createdAt', 'desc')
            ->get();
        
        $totalCredit = $myCredits->sum('is_amount');
        $latestCredit = $myCredits->first();

        // 3. ค่าใช้จ่ายรวมทั้งหมดของคุณ
        $totalExpense = DB::table('item_splits')->where('is_user_id', $userId)->sum('is_amount');
        
        // ข้อมูลกลุ่มล่าสุดที่ใช้งาน
        $latestGroup = DB::table('group_members')
            ->join('groups', 'group_members.gm_group_id', '=', 'groups.group_id')
            ->where('group_members.gm_user_id', $userId)
            ->orderBy('groups.group_createdAt', 'desc')
            ->select('groups.group_name', 'groups.group_id')
            ->first();
            
        $groupMemberCount = 0;
        if ($latestGroup) {
            $groupMemberCount = DB::table('group_members')->where('gm_group_id', $latestGroup->group_id)->count();
        }

        return view('dashboard', compact(
            'totalDebt', 'latestDebt', 
            'totalCredit', 'latestCredit', 
            'totalExpense', 'latestGroup', 'groupMemberCount'
        ));
    }
}