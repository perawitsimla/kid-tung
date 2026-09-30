<!-- resources/views/auth/login.blade.php -->

<form method="POST" action="{{ route('login') }}">
    <!-- 1. ต้องมี @csrf เสมอ ป้องกันการโจมตี -->
    @csrf

    <div>
        <label>Email (หรือ Username)</label>
        <!-- 2. name="email" ต้องตรงกับที่ตั้งไว้ใน config/fortify.php -->
        <input type="email" name="user_email" value="{{ old('email') }}" required autofocus>
        
        <!-- 3. ส่วนแสดง Error ถ้ากรอกผิด หรือไม่มีในระบบ -->
        @error('email')
            <span style="color: red;">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label>Password</label>
        <!-- 4. name="password" บังคับใช้ชื่อนี้ -->
        <input type="password" name="password" required>
        
        @error('password')
            <span style="color: red;">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <button type="submit">Login</button>
    </div>
</form>