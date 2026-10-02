@extends('layouts.app')

@section('title', 'กลุ่มค่าใช้จ่ายทั้งหมดของฉัน')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/groups-index.css') }}">
@endpush

@section('content')
<div class="groups-container">
    <!-- ส่วนหัว Header และปุ่มสร้างกลุ่ม -->
    <div class="groups-header">
        <div class="header-title">
            <h1>กลุ่มค่าใช้จ่ายทั้งหมดของฉัน</h1>
            <p>จัดการกลุ่มทริป ทานข้าว และค่าใช้จ่ายส่วนกลางกับเพื่อนๆ</p>
        </div>
        <a href="{{ route('groups.create') }}" class="btn-create">+ สร้างกลุ่มใหม่</a>
    </div>

    <!-- ส่วนตารางการ์ด (Grid) -->
    <div class="groups-grid">
        @forelse($groups as $group)
            <div class="group-card">
                <div class="card-top">
                    <!-- สถานะจำลอง (สามารถปรับเปลี่ยนด้วย if-else ในอนาคต) -->
                    <span class="member-count">{{ $group->members_count }} คน</span>
                </div>
                
                <h2 class="group-name">{{ $group->group_name }}</h2>
                
                <!-- ยอดรวมจำลอง -->
                <p class="group-total">ยอดรวมกลุ่ม ฿ 0.00</p>
                
                <div class="card-bottom">
                    <!-- ยอดหนี้จำลอง -->
                    <span class="debt-status text-red">คุณติดหนี้ ฿0.00</span>
                    <!-- เปลี่ยน href ไปหน้าโชว์รายละเอียดกลุ่มในอนาคต -->
                    <a href="{{ route('groups.show', $group->group_id) }}" class="btn-view">เปิดดู &rarr;</a>
                </div>
            </div>
        @empty
            <div class="empty-state" style="grid-column: 1 / -1; text-align: center; padding: 3rem; background: white; border-radius: 16px;">
                <p>ยังไม่มีกลุ่มค่าใช้จ่าย คลิก + สร้างกลุ่มใหม่ เพื่อเริ่มต้น!</p>
            </div>
        @endforelse
    </div>
</div>
@endsection