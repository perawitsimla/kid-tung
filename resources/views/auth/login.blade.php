<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - KidTung</title>
    <!-- เรียกใช้ auth.css ไฟล์เดียวกับหน้า Register เพื่อให้หน้าตาเหมือนกัน -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <h2>เข้าสู่ระบบ KidTung</h2>
            <p class="subtitle">หมดปัญหาทะเลาะกันเรื่องเงินเงินทองทอง</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="user_name">ชื่อผู้ใช้งาน (Username)</label>
                    <input type="text" id="user_name" name="user_name" value="{{ old('user_name') }}" required autofocus placeholder="กรอกชื่อผู้ใช้งานของคุณ">
            
                    @error('user_name')
                        <span class="error-text">ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">รหัสผ่าน</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                    
                    @error('password')
                        <span class="error-text">ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง</span>
                    @enderror
                </div>

                <button type="submit" class="btn-primary">เข้าสู่ระบบ</button>
            </form>

            <div class="auth-links">
                ยังไม่มีบัญชีใช่ไหม? <a href="{{ route('register') }}">สมัครสมาชิกเลย</a>
            </div>
        </div>
    </div>
</body>
</html>