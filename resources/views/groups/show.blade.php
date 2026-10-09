@extends('layouts.app')
@section('title', 'รายละเอียดกลุ่ม: ' . $group->group_name)

@push('styles')
<style>
    .group-container { max-width: 1200px; margin: 0 auto; }
    .group-header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    .group-header-bar h2 { font-size: 1.8rem; color: #111827; margin: 0; font-weight: 800; }
    .btn-back { color: #6B7280; text-decoration: none; font-weight: 600; padding: 0.5rem 1rem; border: 1.5px solid #E5E7EB; border-radius: 8px; background: white; transition: 0.2s; }
    .btn-back:hover { background: #F9FAFB; color: #111827; border-color: #D1D5DB; }

    /* แบ่ง 2 คอลัมน์: ซ้าย 350px, ขวาพื้นที่ที่เหลือ */
    .group-grid { display: grid; grid-template-columns: 350px 1fr; gap: 2rem; align-items: start; }
    @media (max-width: 768px) { .group-grid { grid-template-columns: 1fr; } }

    /* กล่องพื้นฐาน */
    .content-card { background: #FFFFFF; border: 1px solid #F3F4F6; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
    .section-title { color: #EA580C; font-size: 1.15rem; font-weight: 700; margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; }

    /* ================================= ฝั่งซ้าย: สมาชิก ================================= */
    .add-member-form { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; }
    .add-member-form input { flex: 1; padding: 0.75rem; border: 1.5px solid #E5E7EB; border-radius: 8px; outline: none; background: #F9FAFB; }
    .add-member-form input:focus { border-color: #EA580C; background: white; }
    .btn-add-member { background: #EA580C; color: white; border: none; border-radius: 8px; width: 45px; font-weight: bold; font-size: 1.2rem; cursor: pointer; transition: 0.2s; }
    .btn-add-member:hover { background: #C2410C; }

    .member-list { display: flex; flex-direction: column; gap: 0.75rem; }
    .member-item { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; border: 1px solid #F3F4F6; border-radius: 10px; background: #FAFAFA; }
    .member-info { display: flex; align-items: center; gap: 0.75rem; }
    .member-avatar { width: 35px; height: 35px; background: #FFEDD5; color: #EA580C; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.9rem; }
    .member-name { font-weight: 600; color: #374151; font-size: 0.95rem; }
    .member-paid { font-size: 0.8rem; color: #6B7280; display: block; }
    .btn-remove { color: #DC2626; background: #FEE2E2; border: none; width: 28px; height: 28px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: bold; transition: 0.2s; }
    .btn-remove:hover { background: #FECACA; }

    /* ================================= ฝั่งขวา: รายการบิล ================================= */
    .btn-add-bill { display: block; width: 94%; padding: 1.25rem; border: 2px dashed #FDBA74; border-radius: 12px; background: #FFF7ED; color: #EA580C; text-align: center; text-decoration: none; font-weight: 700; font-size: 1.05rem; margin-bottom: 1.5rem; transition: 0.2s; }
    .btn-add-bill:hover { background: #EA580C; color: white; border-color: #EA580C; }

    .bill-list { display: flex; flex-direction: column; gap: 1rem; }
    .bill-item { background: white; border: 1.5px solid #E5E7EB; border-radius: 12px; padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; transition: 0.2s; }
    .bill-item:hover { border-color: #FDBA74; box-shadow: 0 4px 12px rgba(234, 88, 12, 0.05); }
    
    .bill-info h3 { margin: 0 0 0.25rem 0; font-size: 1.1rem; color: #111827; }
    .bill-info p { margin: 0 0 0.75rem 0; font-size: 0.85rem; color: #6B7280; }
    .bill-info p strong { color: #374151; }
    
    .split-avatars { display: flex; gap: 0.25rem; align-items: center; }
    .split-avatar { width: 26px; height: 26px; background: #8B5CF6; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: bold; }
    .split-count { font-size: 0.8rem; color: #6B7280; margin-left: 0.5rem; font-weight: 500; }

    .bill-actions-container { display: flex; flex-direction: column; align-items: flex-end; gap: 0.75rem; }
    .bill-price { font-size: 1.35rem; font-weight: 800; color: #EA580C; margin: 0; }
    
    .action-buttons { display: flex; gap: 0.5rem; }
    .btn-edit { padding: 0.4rem 0.8rem; background: #FFF7ED; color: #EA580C; border: 1px solid #FDBA74; border-radius: 6px; font-size: 0.8rem; font-weight: 700; text-decoration: none; transition: 0.2s; }
    .btn-edit:hover { background: #EA580C; color: white; }
    .btn-delete { padding: 0.4rem 0.8rem; background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; border-radius: 6px; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: 0.2s; }
    .btn-delete:hover { background: #DC2626; color: white; border-color: #DC2626; }
    .empty-bills { text-align: center; padding: 3rem 1rem; color: #9CA3AF; background: #FAFAFA; border-radius: 12px; border: 1.5px dashed #E5E7EB; }
</style>
@endpush

@section('content')
<div class="group-container">
    
    <!-- 1. ย้ายปุ่มกลับไปหน้ากลุ่มมาไว้ด้านบนชื่อกลุ่ม -->
    <div style="margin-bottom: 15px;">
        <a href="{{ route('groups.index') }}" class="btn-back" style="text-decoration: none; color: #6B7280; font-weight: 600;">
            &larr; กลับไปหน้ากลุ่ม
        </a>
    </div>

    <!-- 2. แถบ Header: ชื่อกลุ่มอยู่ซ้าย ยอดรวมอยู่ขวา -->
    <div class="group-header-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 2px solid #F3F4F6; padding-bottom: 15px;">
        
        <h2 style="margin: 0; font-size: 1.8rem; color: #111827;">{{ $group->group_name }}</h2>
        
        <!-- กล่องแสดงยอดรวม (แทนที่ปุ่มกลับเดิม) -->
        <div class="group-total-badge" style="background: #FFF7ED; color: #EA580C; padding: 8px 16px; border-radius: 999px; font-weight: 800; font-size: 1.1rem; border: 1px solid #FDBA74; box-shadow: 0 2px 4px rgba(234,88,12,0.1);">
            ยอดรวมกลุ่ม: ฿ {{ isset($expenses) ? number_format($expenses->sum('item_price'), 2) : '0.00' }}
        </div>

    </div>

    <div class="group-grid">
        
        <!-- ================= คอลัมน์ซ้าย: สมาชิก ================= -->
        <div class="content-card">
            <h3 class="section-title">👥 สมาชิก (ใครกินบ้าง)</h3>
            
            <form action="{{ route('groups.members.store', $group->group_id) }}" method="POST" class="add-member-form">
                @csrf
                <input type="text" name="user_name" placeholder="พิมพ์ Username เพื่อน..." required>
                <button type="submit" class="btn-add-member">+</button>
            </form>

            <div class="member-list">
                @foreach($group->members as $member)
                <div class="member-item">
                    <div class="member-info">
                        <div class="member-avatar">{{ strtoupper(substr($member->user_name, 0, 1)) }}</div>
                        <div>
                            <span class="member-name">{{ $member->user_name }}</span>
                            <!-- คุณสามารถคำนวณยอดเงินที่จ่ายไปแล้วมาแสดงตรงนี้ได้ในอนาคต -->
                            <span class="member-paid">ออกเงินไปแล้ว: ฿0.00</span>
                        </div>
                    </div>
                    @if($member->pivot->gm_role !== 'creator')
                    <form action="{{ route('groups.members.destroy', [$group->group_id, $member->user_id]) }}" method="POST" onsubmit="return confirm('ลบสมาชิกคนนี้?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-remove">×</button>
                    </form>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <!-- ================= คอลัมน์ขวา: รายการบิล ================= -->
        <div class="content-card">
            <h3 class="section-title">🧾 รายการอาหาร / บิล</h3>
            
            <a href="{{ route('groups.items.create', $group->group_id) }}" class="btn-add-bill">
                + เพิ่มรายการ
            </a>

            <div class="bill-list">
                @if(isset($expenses) && $expenses->isNotEmpty())
                    @foreach($expenses as $expense)
                    <div class="bill-item">
                        
                        <!-- ข้อมูลบิลฝั่งซ้าย -->
                        <div class="bill-info">
                            <h3>{{ $expense->exp_title }}</h3>
                            <p>จ่ายโดย: <strong>{{ $expense->payer_name }}</strong></p>
                            
                            <div class="split-avatars">
                                @if(isset($splits[$expense->exp_id]))
                                    @foreach($splits[$expense->exp_id]->take(5) as $split)
                                        <div class="split-avatar" title="{{ $split->user_name }}">
                                            {{ strtoupper(substr($split->user_name, 0, 1)) }}
                                        </div>
                                    @endforeach
                                    @if(count($splits[$expense->exp_id]) > 5)
                                        <div class="split-avatar" style="background:#D1D5DB; color:#374151;">+{{ count($splits[$expense->exp_id]) - 5 }}</div>
                                    @endif
                                    <span class="split-count">หาร {{ count($splits[$expense->exp_id]) }} คน</span>
                                @endif
                            </div>
                        </div>

                        <!-- ราคาและปุ่มแก้ไข ฝั่งขวา -->
                        <div class="bill-actions-container">
                            <h4 class="bill-price">฿{{ number_format($expense->item_price, 2) }}</h4>
                            <div class="action-buttons">
                                <!-- ปุ่มแก้ไขเดิม -->
                                <a href="{{ url('/groups/'.$group->group_id.'/expenses/'.$expense->exp_id.'/edit') }}" class="btn-edit">+ แก้ไข</a>
                                
                                <!-- ปุ่มลบที่เพิ่มเข้ามาใหม่ -->
                                <form action="{{ route('groups.expenses.destroy', [$group->group_id, $expense->exp_id]) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรายการบิลนี้?');" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">ลบ</button>
                                </form>
                            </div>
                        </div>

                    </div>
                    @endforeach
                @else
                    <div class="empty-bills">
                        <p>ยังไม่มีรายการบิลในกลุ่มนี้</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection