<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str; 
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        // ตรวจสอบข้อมูลจากฟอร์ม
       Validator::make($input, [
            'user_name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z]+$/'],
            'user_email' => [
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique(User::class, 'user_email'), 
            ],
            'password' => $this->passwordRules(),
        ], [
            'user_name.required' => 'กรุณากรอกชื่อผู้ใช้งาน',
            'user_name.max' => 'ชื่อผู้ใช้งานต้องไม่เกิน 100 ตัวอักษร',
            'user_name.regex' => 'กรอกเป็นภาษาอังกฤษล้วนเท่านั้น',
            
            'user_email.required' => 'กรุณากรอกอีเมล',
            'user_email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'user_email.unique' => 'อีเมลนี้ถูกสมัครสมาชิกไปแล้ว',
            
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.confirmed' => 'รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน',
            'password.min' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร',
        ])->validate();

        // บันทึกลงฐานข้อมูล
        return User::create([
            'user_id' => Str::random(8),
            'user_name' => $input['user_name'],
            'user_email' => $input['user_email'],
            'user_password' => Hash::make($input['password']),
        ]);
    }
}