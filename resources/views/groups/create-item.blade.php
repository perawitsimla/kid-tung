@extends('layouts.app')
@section('title', 'เพิ่มรายการ - ' . $group->group_name)
@push('styles')
    <style>
        .form-container { max-width: 500px; margin: 2rem auto; background: white; padding: 2rem; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 0.5rem; color: #374151; }
        .form-control { width: 100%; padding: 0.75rem; border: 1px solid #D1D5DB; border-radius: 8px; box-sizing: border-box; }
        
        /* สไตล์ Avatar สำหรับเลือกผู้จ่าย */
        .avatar-selector { display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.5rem; }
        .avatar-label { cursor: pointer; }
        .avatar-label input[type="radio"] { display: none; }
        .avatar-circle { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #E5E7EB; color: #374151; font-weight: bold; border: 2px solid transparent; }
        .avatar-label input[type="radio"]:checked + .avatar-circle { border-color: #8B5CF6; background: #EDE9FE; color: #8B5CF6; }

        /* แท็บวิธีหาร */
        .split-tabs { display: flex; background: #F3F4F6; border-radius: 8px; overflow: hidden; margin-bottom: 1rem; }
        .split-tab { flex: 1; text-align: center; padding: 0.5rem; cursor: pointer; font-size: 0.9rem; color: #4B5563; }
        .split-tab.active { background: white; font-weight: bold; border: 1px solid #E5E7EB; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }

        /* เช็คลิสต์สมาชิก */
        .member-checklist { border: 1px solid #E5E7EB; border-radius: 8px; padding: 0.5rem 1rem; }
        .check-item { display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid #F3F4F6; }
        .check-item:last-child { border-bottom: none; }
        .check-info { display: flex; align-items: center; gap: 0.75rem; }
        
        .btn-save { background: #8B5CF6; color: white; width: 100%; padding: 1rem; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 1rem; margin-top: 1rem; }
        .btn-cancel { background: #F3F4F6; color: #374151; width: 100%; padding: 1rem; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 1rem; margin-top: 0.5rem; text-decoration: none; display: block; text-align: center; }
    </style>
@endpush

@section('content')
<div class="form-container">
    <h2 style="margin-top: 0; border-bottom: 1px solid #E5E7EB; padding-bottom: 1rem;">เพิ่มรายการ (Add Expense)</h2>
    
    <<!-- เปลี่ยน action ให้ชี้ไปที่ Route สำหรับบันทึกบิล -->
    <form action="{{ route('groups.expenses.store', $group->group_id) }}" method="POST">
        @csrf
        <!-- ชื่อรายการ -->
        <div class="form-group">
            <label>Item Name</label>
            <input type="text" name="title" class="form-control" placeholder="ชื่อรายการ" required>
        </div>

        <!-- ราคา -->
        <div class="form-group">
            <label>Amount (฿)</label>
            <input type="number" name="price" class="form-control" placeholder="0.00" step="0.01" required>
        </div>

        <!-- เลือกคนจ่าย (Paid By) -->
        <div class="form-group">
            <label>Paid By (คนสำรองจ่าย)</label>
            <div class="avatar-selector">
                @foreach($group->members as $member)
                <label class="avatar-label">
                    <input type="radio" name="paid_by" value="{{ $member->user_id }}" {{ $loop->first ? 'checked' : '' }}>
                    <div class="avatar-circle" title="{{ $member->user_name }}">
                        {{ strtoupper(substr($member->user_name, 0, 1)) }}
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <!-- เลือกวิธีหาร -->
        <div class="form-group">
            <label>วิธีหาร</label>
            <div class="split-tabs">
                <div class="split-tab active">Per Person<br>(หารเท่า)</div>
                <div class="split-tab">Specified Amount<br>(ระบุยอด)</div>
                <div class="split-tab">Percentage<br>(เปอร์เซ็นต์)</div>
            </div>
        </div>

        <!-- Check-list สมาชิกที่หารด้วย -->
        <div class="form-group">
            <label>Member check-list (ใครหารบ้าง)</label>
            <div class="member-checklist">
                @foreach($group->members as $member)
                <label class="check-item">
                    <div class="check-info">
                        <div class="avatar-circle" style="width: 30px; height: 30px; font-size: 0.8rem; background: #8B5CF6; color: white;">
                            {{ strtoupper(substr($member->user_name, 0, 1)) }}
                        </div>
                        <span>{{ $member->user_name }}</span>
                    </div>
                    <!-- Checkbox สีม่วง -->
                    <input type="checkbox" name="split_users[]" value="{{ $member->user_id }}" checked style="accent-color: #8B5CF6; transform: scale(1.2);">
                </label>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn-save">Save (บันทึก)</button>
        <a href="{{ route('groups.show', $group->group_id) }}" class="btn-cancel">Cancel (ยกเลิก)</a>
    </form>
</div>
@endsection