@extends('layouts.app')
@section('title', $group->group_name)
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/group-show.css') }}">
    <style>
        /* สไตล์สำหรับปุ่มลบสมาชิก และตั้งค่า VAT/SC */
        .btn-remove { background: none; border: none; color: #EF4444; cursor: pointer; font-size: 1.2rem; }
        .tax-settings { display: flex; gap: 1rem; margin-bottom: 1.5rem; }
        .tax-card { background: #F8FAFC; border: 1px solid #E2E8F0; padding: 1rem; border-radius: 8px; flex: 1; }
        .tax-header { display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-weight: bold; }
        .tax-input-group { display: flex; align-items: center; gap: 0.5rem; }
        .tax-input-group input { width: 60px; padding: 0.25rem; border: 1px solid #CBD5E1; border-radius: 4px; text-align: center; }
        .tax-amount { color: #8B5CF6; font-weight: bold; margin-top: 0.5rem; font-size: 0.9rem; }
        /* สวิตช์ Toggle */
        .switch { position: relative; display: inline-block; width: 34px; height: 20px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 2px; bottom: 2px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: #8B5CF6; }
        input:checked + .slider:before { transform: translateX(14px); }
    </style>
@endpush

@section('content')
<div class="manage-container">
    <div class="manage-header">
        <h1>{{ $group->group_name }}</h1>
        <a href="{{ route('groups.index') }}" class="btn-back">&larr; กลับไปหน้ากลุ่ม</a>
    </div>

    <div class="split-layout">
        <!-- คอลัมน์ซ้าย: สมาชิก -->
        <div class="manage-card members-section">
            <h3 class="section-title">👥 สมาชิก (ใครกินบ้าง)</h3>
            
            <form action="{{ route('groups.members.store', $group->group_id) }}" method="POST" class="add-form">
                @csrf
                <input type="text" name="user_name" placeholder="พิมพ์ Username เพื่อน..." class="input-field" required>
                <button type="submit" class="btn-add">+</button>
            </form>

            <div class="item-list">
                @foreach($group->members as $member)
                <div class="list-item">
                    <div class="item-info">
                        <strong>{{ $member->user_name }}</strong>
                        <p class="text-muted">ออกเงินไปแล้ว: ฿0.00</p>
                    </div>
                    <!-- ฟอร์มสำหรับลบสมาชิก -->
                    <form action="{{ route('groups.members.destroy', ['group' => $group->group_id, 'user' => $member->user_id]) }}" method="POST" onsubmit="return confirm('ต้องการลบสมาชิกคนนี้หรือไม่?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-remove" title="ลบสมาชิก">&times;</button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>

        <!-- คอลัมน์ขวา: รายการบิล และตั้งค่า VAT/SC -->
        <div class="manage-card expenses-section">
            <h3 class="section-title">🧾 รายการอาหาร / บิล</h3>
            
            <!-- ตั้งค่า VAT และ Service Charge -->
            <div class="tax-settings">
                <!-- VAT -->
                <div class="tax-card">
                    <div class="tax-header">
                        <span>VAT</span>
                        <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
                    </div>
                    <div class="tax-input-group">
                        <input type="number" value="7"> <span>%</span>
                    </div>
                    <div class="tax-amount">+฿0.00</div>
                </div>
                <!-- Service Charge -->
                <div class="tax-card">
                    <div class="tax-header">
                        <span>Service Charge</span>
                        <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
                    </div>
                    <div class="tax-input-group">
                        <input type="number" value="10"> <span>%</span>
                    </div>
                    <div class="tax-amount">+฿0.00</div>
                </div>
            </div>

            <!-- ปุ่มเปิดไปหน้าฟอร์มเพิ่มรายการ -->
            <a href="{{ route('groups.items.create', $group->group_id) }}" class="btn-primary" style="display: block; text-align: center; margin-bottom: 1rem;">
                + เพิ่มรายการ
            </a>

            <!-- รายการที่ถูกเพิ่มแล้ว (ดึงจาก Database) -->
            <div class="item-list">
                @if($expenses->isEmpty())
                    <div style="text-align: center; padding: 2rem 0; color: #9CA3AF;">
                        <p>ยังไม่มีรายการบิลในกลุ่มนี้</p>
                    </div>
                @else
                    @foreach($expenses as $expense)
                    <div class="list-item" style="display: flex; flex-direction: column;">
                        <div class="item-info" style="width: 100%;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <strong style="font-size: 1.1rem;">{{ $expense->exp_title }}</strong>
                                <strong style="color: #EA580C; font-size: 1.1rem;">฿{{ number_format($expense->item_price, 2) }}</strong>
                            </div>
                            <p class="text-muted" style="margin-top: 0.25rem;">
                                จ่ายโดย: <strong>{{ $expense->payer_name }}</strong>
                            </p>
                            
                            <!-- วงกลมแสดงคนหาร -->
                            <div class="avatar-group" style="margin-top: 0.5rem; display: flex; gap: 0.25rem; align-items: center;">
                                @if(isset($splits[$expense->exp_id]))
                                    @foreach($splits[$expense->exp_id] as $split)
                                        <div style="width: 26px; height: 26px; font-size: 0.75rem; background: #8B5CF6; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;" title="{{ $split->user_name }} (หาร ฿{{ number_format($split->is_amount, 2) }})">
                                            {{ strtoupper(substr($split->user_name, 0, 1)) }}
                                        </div>
                                    @endforeach
                                    <span style="font-size: 0.8rem; color: #6B7280; margin-left: 0.5rem;">
                                        หาร {{ count($splits[$expense->exp_id]) }} คน
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
            <div class="item-list">
                <div style="text-align: center; padding: 2rem 0; color: #9CA3AF;">
                    <p>ยังไม่มีรายการบิลในกลุ่มนี้</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection