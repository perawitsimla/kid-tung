<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KidTung - @yield('title', 'แอปพลิเคชันหารค่าใช้จ่าย')</title>
    
    <!-- เรียกใช้ไฟล์ CSS ภายนอก -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @stack('styles')
</head>
<body>

    <nav class="navbar">
        <a href="{{ route('dashboard') }}" class="navbar-brand">KidTung</a>
        <div class="navbar-menu">
            <a href="{{ route('dashboard') }}">หน้าแรก</a>
            <a href="{{ route('groups.store') }}">กลุ่มของฉัน</a>
            
            <form method="POST" action="/logout" style="display: inline; margin-left: 1.5rem;">
                @csrf
                <button type="submit" style="background: none; border: none; cursor: pointer; color: var(--text-main); font-weight: 500; font-family: inherit; font-size: 1rem;">
                    ออกจากระบบ
                </button>
            </form>
        </div>
    </nav>

    <main class="container">
        <div class="main-content">
            @yield('content')
        </div>
    </main>

    <footer class="footer">
        &copy; {{ date('Y') }} KidTung. All rights reserved.
    </footer>

    @stack('scripts')
</body>
</html>