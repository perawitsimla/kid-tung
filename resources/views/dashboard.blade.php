<!-- 1. เรียกใช้โครงสร้างจากไฟล์ resources/views/layouts/app.blade.php -->
@extends('layouts.app')

<!-- 2. ส่งข้อความไปแทรกใน @yield('title') บนแท็ก <title> ของ layout -->
@section('title', 'ภาพรวมบัญชีของฉัน')

<!-- 3. ส่งโค้ด HTML ทั้งหมดนี้ไปแทรกใน @yield('content') -->
@section('content')
    <div class="dashboard-header">
        <h1>ยินดีต้อนรับกลับมา!</h1>
        <p>นี่คือสรุปยอดหนี้และการใช้งานกลุ่มล่าสุดของคุณ</p>
    </div>

    <div class="summary-cards">
        <div class="card card-danger">
            <h3>คุณติดหนี้เพื่อนรวม</h3>
            <!-- number_format ช่วยใส่ลูกน้ำและทศนิยม 2 ตำแหน่ง -->
            <h2>฿ {{ number_format($totalOwed, 2) }}</h2> 
        </div>
        
        <div class="card card-success">
            <h3>เพื่อนติดหนี้คุณรวม</h3>
            <h2>฿ {{ number_format($totalLent, 2) }}</h2>
        </div>
    </div>

    <div class="recent-groups">
        <div class="group-header">
            <h2>กลุ่มล่าสุด</h2>
            <a href="{{ route('groups.create') }}" class="btn-primary">+ สร้างกลุ่มใหม่</a>
        </div>

        @if($recentGroups->isEmpty())
            <div class="empty-state">
                <p>คุณยังไม่ได้เข้าร่วมกลุ่มใดเลย ลองสร้างกลุ่มใหม่เพื่อเริ่มหารค่าใช้จ่ายดูสิ!</p>
            </div>
        @else
            <ul class="group-list">
                <!-- ใช้ foreach วนลูปตัวแปร $recentGroups ที่ส่งมาจาก Controller -->
                @foreach($recentGroups as $group)
                    <li>
                        <a href="{{ route('groups.show', $group->group_id) }}" class="group-item">
                            <strong>{{ $group->group_name }}</strong>
                            <span class="text-muted">เข้าไปดูรายละเอียด &rarr;</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection

<!-- 4. ส่ง CSS เฉพาะหน้านี้ไปแทรกใน @stack('styles') ที่อยู่ใน <head> ของ layout -->
@push('styles')
<style>
    .dashboard-header {
        margin-bottom: 2rem;
    }
    
    .summary-cards {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .card {
        flex: 1;
        padding: 1.5rem;
        border-radius: 10px;
        color: white;
    }

    .card h3 { margin-top: 0; font-size: 1rem; opacity: 0.9; }
    .card h2 { margin: 0; font-size: 2rem; }

    .card-danger { background-color: #EF4444; } /* สีแดง */
    .card-success { background-color: #10B981; } /* สีเขียว */

    .group-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
    }

    .group-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .group-item {
        display: flex;
        justify-content: space-between;
        padding: 1rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        margin-bottom: 0.5rem;
        text-decoration: none;
        color: var(--text-main);
        transition: background-color 0.2s;
    }

    .group-item:hover {
        background-color: #F9FAFB;
        border-color: var(--primary-color);
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        background-color: #F9FAFB;
        border-radius: 8px;
        border: 1px dashed var(--border-color);
    }
</style>
@endpush