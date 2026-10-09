@extends('layouts.app')

<!-- เช็ค Title ว่าเป็นโหมด แก้ไข หรือ เพิ่มรายการ -->
@section('title', isset($expense) ? 'แก้ไขรายการ' : 'เพิ่มรายการ')

@push('styles')
    <style>
        .form-container { max-width: 900px; margin: 2rem auto; background: #FFFFFF; padding: 2.5rem; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); border: 1px solid #F3F4F6; }
        .form-header { margin-top: 0; padding-bottom: 1rem; border-bottom: 1px solid #E5E7EB; color: #111827; font-weight: 800; margin-bottom: 2rem; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2.5rem; }
        @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; gap: 1.5rem; } }
        
        .form-group { margin-bottom: 1.75rem; }
        .form-group label.group-title { display: block; font-weight: 700; margin-bottom: 0.75rem; color: #374151; font-size: 0.95rem; }
        .form-control { width: 100%; padding: 0.85rem 1rem; border: 1.5px solid #E5E7EB; border-radius: 10px; box-sizing: border-box; background: #FAFAFA; outline: none; font-size: 1rem; transition: 0.2s; }
        .form-control:focus { border-color: #EA580C; background: #FFF; box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1); }

        /* ระบบ VAT / SC */
        .tax-container { display: flex; gap: 1rem; margin-bottom: 1.5rem; }
        .tax-box { flex: 1; background: #F9FAFB; border: 1.5px solid #E5E7EB; border-radius: 10px; padding: 1rem; }
        .tax-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; }
        .tax-header label { font-weight: 700; color: #374151; margin: 0; }
        .switch { position: relative; display: inline-block; width: 44px; height: 24px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #D1D5DB; transition: .2s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .2s; border-radius: 50%; }
        .switch input:checked + .slider { background-color: #EA580C; }
        .switch input:checked + .slider:before { transform: translateX(20px); }
        .tax-input-group { display: flex; align-items: center; gap: 0.5rem; }
        .tax-input-group input { flex: 1; padding: 0.5rem; border: 1px solid #D1D5DB; border-radius: 6px; outline: none; transition: 0.2s; }
        .tax-input-group input:focus { border-color: #EA580C; }
        .tax-input-group input:disabled { background: #E5E7EB; cursor: not-allowed; }
        .net-total-box { background: #FFF7ED; border: 1.5px dashed #EA580C; border-radius: 10px; padding: 1rem; display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .net-total-box .net-label { color: #C2410C; font-weight: 700; font-size: 1.1rem; }
        .net-total-box .net-amount { color: #EA580C; font-weight: 800; font-size: 1.5rem; }

        /* Pill Radio & Checkbox */
        .pill-selector { display: flex; flex-wrap: wrap; gap: 0.75rem; }
        .pill-label { cursor: pointer; }
        .pill-label input[type="radio"] { display: none; }
        .pill-button { padding: 0.6rem 1.25rem; border: 1.5px solid #E5E7EB; border-radius: 999px; background: #FFFFFF; color: #4B5563; font-weight: 600; font-size: 0.95rem; transition: all 0.2s ease; display: inline-block; }
        .pill-label:hover .pill-button { border-color: #FDBA74; color: #EA580C; background: #FAFAFA; }
        .pill-label input[type="radio"]:checked + .pill-button { border-color: #EA580C; background: #FFF7ED; color: #EA580C; box-shadow: 0 2px 4px rgba(234, 88, 12, 0.1); }

        .member-checklist { display: flex; flex-direction: column; gap: 0.5rem; max-height: 250px; overflow-y: auto; padding-right: 0.5rem; }
        .member-checklist::-webkit-scrollbar { width: 6px; }
        .member-checklist::-webkit-scrollbar-track { background: #F3F4F6; border-radius: 10px; }
        .member-checklist::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 10px; }
        .hidden-cb { display: none; }
        .check-item-label { display: flex; justify-content: space-between; align-items: center; padding: 0.85rem 1.25rem; border: 1.5px solid #E5E7EB; border-radius: 12px; cursor: pointer; transition: all 0.2s; background: #FFFFFF; }
        .check-item-label:hover { border-color: #FDBA74; background: #FFF7ED; }
        .check-info { display: flex; align-items: center; gap: 1rem; }
        .check-info .mini-avatar { width: 32px; height: 32px; background: #F3F4F6; color: #6B7280; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; transition: 0.2s; }
        .check-info span { font-weight: 600; color: #374151; font-size: 0.95rem; transition: 0.2s; }
        .custom-checkbox { width: 22px; height: 22px; border: 2px solid #D1D5DB; border-radius: 6px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
        .custom-checkbox::after { content: ""; width: 5px; height: 10px; border: solid white; border-width: 0 2.5px 2.5px 0; transform: rotate(45deg); display: none; margin-bottom: 2px; }
        .hidden-cb:checked + .check-item-label { border-color: #EA580C; background: #FFF7ED; }
        .hidden-cb:checked + .check-item-label .mini-avatar { background: #EA580C; color: white; }
        .hidden-cb:checked + .check-item-label .check-info span { color: #EA580C; }
        .hidden-cb:checked + .check-item-label .custom-checkbox { background: #EA580C; border-color: #EA580C; }
        .hidden-cb:checked + .check-item-label .custom-checkbox::after { display: block; }

        .form-actions { display: flex; gap: 1rem; margin-top: 1rem; }
        .btn-cancel { flex: 1; background: white; color: #4B5563; padding: 1rem; border: 1.5px solid #E5E7EB; border-radius: 10px; font-weight: 700; text-decoration: none; text-align: center; transition: 0.2s; }
        .btn-cancel:hover { background: #F9FAFB; color: #111827; border-color: #D1D5DB; }
        .btn-save { flex: 2; background: #EA580C; color: white; padding: 1rem; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 1.05rem; box-shadow: 0 4px 6px -1px rgba(234, 88, 12, 0.2); transition: 0.2s; }
        .btn-save:hover { background: #C2410C; transform: translateY(-2px); }
    </style>
@endpush

@section('content')
<div class="form-container">
    <div class="form-header">
        <!-- เปลี่ยนหัวข้อตามโหมด (แก้ไข / เพิ่ม) -->
        <h2>{{ isset($expense) ? '✎ แก้ไขบิล: ' . $expense->exp_title : '➕ เพิ่มรายการบิล' }}</h2>
    </div>
    
    <!-- สลับ Action ว่าจะส่งไปอัปเดต หรือ สร้างใหม่ -->
   <!-- สลับ Action ว่าจะส่งไปอัปเดต หรือ สร้างใหม่ -->
   <!-- สลับ Action ว่าจะส่งไปอัปเดต หรือ สร้างใหม่ -->
    @if(isset($expense))
        <form action="{{ url('/groups/' . $group->group_id . '/expenses/' . $expense->exp_id) }}" method="POST">
        @method('PUT')
    @else
        <form action="{{ url('/groups/' . $group->group_id . '/expenses') }}" method="POST">
    @endif
        @csrf
        
        <div class="form-grid">
            <div class="left-col">
                <div class="form-group">
                    <label class="group-title">ชื่อรายการ (Item Name)</label>
                    <input type="text" name="title" class="form-control" placeholder="เช่น ค่าที่พัก, หมูกระทะ..." 
                           value="{{ isset($expense) ? $expense->exp_title : '' }}" required>
                </div>

                <div class="form-group">
                    <label class="group-title">ราคาตั้งต้น (Base Amount ฿)</label>
                    <!-- ถัาแก้ไข ก็ดึงราคามาใส่ไว้เลย -->
                    <input type="number" id="base_price" class="form-control" placeholder="0.00" step="0.01" 
                           value="{{ isset($expenseItem) ? $expenseItem->item_price : '' }}" required>
                </div>

                <!-- ระบบคำนวณ VAT / SC Realtime -->
                <div class="tax-container">
                    <div class="tax-box">
                        <div class="tax-header">
                            <label>Service Charge</label>
                            <label class="switch">
                                <input type="checkbox" id="sc_toggle">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="tax-input-group">
                            <input type="number" id="sc_percent" value="10" min="0" step="0.1" disabled>
                            <span>%</span>
                        </div>
                    </div>
                    <div class="tax-box">
                        <div class="tax-header">
                            <label>VAT</label>
                            <label class="switch">
                                <input type="checkbox" id="vat_toggle">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="tax-input-group">
                            <input type="number" id="vat_percent" value="7" min="0" step="0.1" disabled>
                            <span>%</span>
                        </div>
                    </div>
                </div>

                <div class="net-total-box">
                    <span class="net-label">ยอดสุทธิที่ต้องหาร:</span>
                    <span class="net-amount" id="net_total_display">฿ 0.00</span>
                    <input type="hidden" id="final_price" name="price" value="{{ isset($expenseItem) ? $expenseItem->item_price : '0' }}">
                </div>

                <div class="form-group">
                    <label class="group-title">คนสำรองจ่าย (Paid By)</label>
                    <div class="pill-selector">
                        @foreach($group->members as $member)
                        <label class="pill-label">
                            <input type="radio" name="paid_by" value="{{ $member->user_id }}" 
                                @if(isset($expenseItem))
                                    {{ $member->user_id == $expenseItem->item_payer_id ? 'checked' : '' }}
                                @else
                                    {{ $loop->first ? 'checked' : '' }}
                                @endif>
                            <div class="pill-button">{{ $member->user_name }}</div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="right-col">
                <div class="form-group">
                    <label class="group-title">ใครหารบ้าง? (Member check-list)</label>
                    <div class="member-checklist">
                        @foreach($group->members as $member)
                        <input type="checkbox" id="user_{{ $member->user_id }}" name="split_users[]" value="{{ $member->user_id }}" class="hidden-cb" 
                            @if(isset($splits))
                                {{ in_array($member->user_id, $splits) ? 'checked' : '' }}
                            @else
                                checked
                            @endif>
                        
                        <label for="user_{{ $member->user_id }}" class="check-item-label">
                            <div class="check-info">
                                <div class="mini-avatar">{{ strtoupper(substr($member->user_name, 0, 1)) }}</div>
                                <span>{{ $member->user_name }}</span>
                            </div>
                            <div class="custom-checkbox"></div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('groups.show', $group->group_id) }}" class="btn-cancel">ยกเลิก</a>
                    <button type="submit" class="btn-save">{{ isset($expense) ? 'บันทึกการแก้ไข' : 'บันทึกรายการ' }}</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const basePriceInput = document.getElementById('base_price');
        const scToggle = document.getElementById('sc_toggle');
        const scPercentInput = document.getElementById('sc_percent');
        const vatToggle = document.getElementById('vat_toggle');
        const vatPercentInput = document.getElementById('vat_percent');
        const netTotalDisplay = document.getElementById('net_total_display');
        const finalPriceInput = document.getElementById('final_price');

        function calculateNetTotal() {
            let basePrice = parseFloat(basePriceInput.value) || 0;
            let scAmount = 0;
            let vatAmount = 0;

            scPercentInput.disabled = !scToggle.checked;
            vatPercentInput.disabled = !vatToggle.checked;

            if (scToggle.checked) {
                let scPercent = parseFloat(scPercentInput.value) || 0;
                scAmount = basePrice * (scPercent / 100);
            }

            let baseWithSc = basePrice + scAmount;
            if (vatToggle.checked) {
                let vatPercent = parseFloat(vatPercentInput.value) || 0;
                vatAmount = baseWithSc * (vatPercent / 100);
            }

            let netTotal = basePrice + scAmount + vatAmount;

            netTotalDisplay.textContent = '฿ ' + netTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            // ส่งค่าสุทธิเข้า hidden input ทันทีที่พิมพ์หรือเปลี่ยนแปลง
            finalPriceInput.value = netTotal.toFixed(2);
        }

        // --- เพิ่มส่วนนี้: ป้องกันกรณีที่กำลังกด "แก้ไข" แล้วค่าราคานิ่งไม่ยอมคำนวณ ---
        if (basePriceInput.value) {
            calculateNetTotal();
        }

        basePriceInput.addEventListener('input', calculateNetTotal);
        scToggle.addEventListener('change', calculateNetTotal);
        scPercentInput.addEventListener('input', calculateNetTotal);
        vatToggle.addEventListener('change', calculateNetTotal);
        vatPercentInput.addEventListener('input', calculateNetTotal);
    });
</script>
@endpush