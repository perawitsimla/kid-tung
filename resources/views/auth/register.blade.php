<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - KidTung</title>
    <!-- เรียกใช้งานไฟล์ CSS ที่เราแยกไว้ -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <h2>สร้างบัญชีใหม่</h2>
            <p class="subtitle">สมัครเลยถ้าไม่อยากมีปัญหากับเพื่อนเรื่องเงิน</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <label for="user_name">ชื่อผู้ใช้งาน</label>
                    <input type="text" id="user_name" name="user_name" value="{{ old('user_name') }}" required autofocus placeholder="กรอกชื่อของคุณ">
                    @error('user_name')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="user_email">อีเมล</label>
                    <input type="email" id="user_email" name="user_email" value="{{ old('user_email') }}" required placeholder="eiei@gmail.com">
                    @error('user_email')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">รหัสผ่าน</label>
                    <input type="password" id="password" name="password" required placeholder="ตั้งรหัสผ่าน 8 ตัวอักษรขึ้นไป">
                    @error('password')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">ยืนยันรหัสผ่านอีกครั้ง</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="กรอกรหัสผ่านอีกครั้งให้ตรงกัน">
                </div>

                <button type="submit" class="btn-primary">สมัครสมาชิก</button>
            </form>

            <div class="auth-links">
                มีบัญชีอยู่แล้ว? <a href="{{ route('login') }}">เข้าสู่ระบบที่นี่</a>
            </div>
        </div>
    </div>
</body>
</html>