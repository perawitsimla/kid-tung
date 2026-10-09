@extends('layouts.app')
@section('title', 'ภาพรวมสรุปยอด')

@push('styles')
    <!-- เรียกผ่านพาธตรงๆ เพื่อตัดปัญหา https/http ขัดแย้งกัน -->
    <link rel="stylesheet" href="/css/dashboard.css">
@endpush

@section('content')
<div class="dashboard-container">
    <h2 class="page-title">ภาพรวมสรุปยอด</h2>

    <div class="summary-cards-grid">
        
        <!-- Card 1: คุณติดหนี้คนอื่น -->
        <div class="summary-card card-debt">
            <div class="card-header">
                <span class="title">คุณติดหนี้คนอื่น</span>
            </div>
            
            <div class="card-amount">฿ {{ number_format($totalDebt, 2) }}</div>
            
            <div class="card-detail">
                @if(!empty($latestDebt))
                    ยอดค้างจ่ายให้ <strong>{{ $latestDebt->creditor_name }}</strong> (ค่า{{ $latestDebt->item_name }})
                @else
                    คุณไม่มีหนี้ค้างจ่าย เยี่ยมมาก!
                @endif
            </div>

            <div class="card-footer">
                <span class="status">สถานะ: รอโอนเงินและแนบสลิป</span>
                <a href="#" class="action-link">ชำระเงิน &rarr;</a>
            </div>
        </div>

        <!-- Card 2: คนอื่นติดหนี้คุณ -->
        <div class="summary-card card-credit">
            <div class="card-header">
                <span class="title">คนอื่นติดหนี้คุณ</span>
            </div>
            
            <div class="card-amount">฿ {{ number_format($totalCredit, 2) }}</div>
            
            <div class="card-detail">
                @if(!empty($latestCredit))
                    รอคุณตรวจสอบและยืนยันสลิปจาก <strong>{{ $latestCredit->debtor_name }}</strong>
                @else
                    ไม่มีใครติดหนี้คุณในตอนนี้
                @endif
            </div>

            <div class="card-footer">
                <span class="status status-highlight">&bull; รอยืนยันสลิป</span>
                <a href="#" class="action-link">ตรวจสลิป &rarr;</a>
            </div>
        </div>

        <!-- Card 3: ค่าใช้จ่ายรวมทั้งหมด -->
        <div class="summary-card card-total">
            <div class="card-header">
                <span class="title">ค่าใช้จ่ายรวมทั้งหมด</span>
            </div>
           
            <div class="card-amount">฿ {{ number_format($totalExpense, 2) }}</div>
            
            <div class="card-detail">
                @if(!empty($latestGroup))
                    จากกลุ่มล่าสุด: <strong>{{ $latestGroup->group_name }}</strong>
                @else
                    คุณยังไม่มีประวัติค่าใช้จ่าย
                @endif
            </div>

            <div class="card-footer">
                <span class="status">สมาชิกในกลุ่ม {{ $groupMemberCount }} คน</span>
                @if(!empty($latestGroup))
                <a href="{{ route('groups.show', $latestGroup->group_id) }}" class="action-link">+ เพิ่มบิลใหม่</a>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection