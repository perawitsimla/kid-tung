<!-- 1. เรียกใช้โครงสร้างจากไฟล์ resources/views/layouts/app.blade.php -->
@extends('layouts.app')

<!-- 2. ส่งข้อความไปแทรกใน @yield('title') บนแท็ก <title> ของ layout -->
@section('title', 'ภาพรวมบัญชีของฉัน')

<!-- 3. ส่งโค้ด HTML ทั้งหมดนี้ไปแทรกใน @yield('content') -->
 <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@section('content')
    <div class="dashboard-header">
        <h1>ยินดีต้อนรับกลับมา!</h1>
        <p>นี่คือสรุปยอดหนี้และการใช้งานกลุ่มล่าสุดของคุณ</p>
    </div>

    <div class="summary-cards">
        <div class="card card-danger">
            <h3>คุณติดหนี้เพื่อนรวม</h3>
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