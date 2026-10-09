<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KidTung - @yield('title')</title>
    <!-- นำเข้า Google Font: Prompt -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    @stack('styles')
</head>
<body>
    @guest
        <!-- หน้า Login / Register (ไม่มี Sidebar) -->
        <div class="auth-wrapper">
            @yield('content')
        </div>
    @else
        <!-- หน้าจัดการระบบ (มี Sidebar ด้านซ้าย) -->
        <div class="app-layout">
            
            <!-- Sidebar ด้านซ้าย -->
            <aside class="sidebar">
                <div class="sidebar-logo">
                    <h2>KidTung</h2>
                </div>
                
                <nav class="sidebar-menu">
                    <a href="{{ route('dashboard') ?? '#' }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    ภาพรวมสรุปยอด
                    </a>
                    <a href="{{ route('groups.index') }}" class="menu-item {{ request()->routeIs('groups.*') ? 'active' : '' }}">
                    กลุ่มของฉัน
                    </a>
                    <a href="" class="menu-item">
                    รายละเอียดกลุ่ม & บิล
                    </a>
                    <a href="#" class="menu-item">
                    รายการรออนุมัติสลิป <span class="badge">3</span>
                    </a>
                    <a href="#" class="menu-item">
                    รายการกิจกรรม
                    </a>
                </nav>

                <div class="sidebar-footer">
                    <div class="user-profile">
                        <div class="avatar">{{ strtoupper(substr(Auth::user()->user_name, 0, 1)) }}</div>
                        <div class="user-info">
                            <strong>{{ Auth::user()->user_name }}</strong>
                            <small>บัญชีผู้ใช้</small>
                        </div>
                    </div>
                    <form action="{{ route('logout') ?? '#' }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-logout">ออกจากระบบ</button>
                    </form>
                </div>
            </aside>

            <!-- พื้นที่เนื้อหาหลักด้านขวา -->
            <main class="main-content">
                @yield('content')
            </main>
            
        </div>
    @endguest

    @stack('scripts')
</body>
</html>