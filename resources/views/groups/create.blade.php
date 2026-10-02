@extends('layouts.app')

@section('title', 'สร้างกลุ่มใหม่')

@push('styles')
    <!-- เรียกใช้ CSS ของระบบล็อกอิน (พวก form-group, btn-primary) และ CSS ใหม่ที่เราเพิ่งสร้าง -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/group-create.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="group-create-container">
        <h2>สร้างกลุ่มค่าใช้จ่ายใหม่</h2>
        <p class="subtitle">ตั้งชื่อกลุ่มและเพิ่มเพื่อนที่ต้องการหารค่าใช้จ่าย</p>

        <!-- ห้ามให้ฟอร์ม submit เวลากด Enter ในช่องค้นหาสมาชิก -->
        <form method="POST" action="{{ route('groups.store') }}" id="groupForm" onsubmit="return validateForm()">
            @csrf

            <!-- 1. ชื่อกลุ่ม -->
            <div class="form-group">
                <label for="group_name">ชื่อกลุ่ม <span style="color:red">*</span></label>
                <input type="text" id="group_name" name="group_name" required placeholder="เช่น ทริปเที่ยวทะเล, ค่าหอพัก">
            </div>

            <!-- 2. ค้นหาและเพิ่มสมาชิก -->
            <div class="form-group">
                <label for="member_input">เพิ่มสมาชิก (พิมพ์ Username)</label>
                <div class="input-add-group">
                    <input type="text" id="member_input" placeholder="พิมพ์ชื่อผู้ใช้ของเพื่อน...">
                    <button type="button" class="btn-add" id="btnAddMember">เพิ่ม</button>
                </div>
                <span id="member_error" class="error-text" style="display: none; margin-top: 0.5rem;"></span>
            </div>

            <!-- 3. พื้นที่แสดงป้ายชื่อสมาชิกที่เพิ่มสำเร็จ -->
            <div id="member_list" class="member-list">
                <!-- JavaScript จะเอา Badge มาใส่ตรงนี้ -->
            </div>

            <!-- 4. กล่องซ่อน (Hidden) เพื่อเก็บข้อมูลส่งไป Backend -->
            <div id="hidden_members_container"></div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">ยืนยันสร้างกลุ่ม</button>
                <a href="{{ route('dashboard') }}" class="btn-secondary">ยกเลิก</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    const memberInput = document.getElementById('member_input');
    const btnAddMember = document.getElementById('btnAddMember');
    const memberList = document.getElementById('member_list');
    const hiddenContainer = document.getElementById('hidden_members_container');
    const errorSpan = document.getElementById('member_error');
    
    // เก็บรายชื่อคนที่ถูกเพิ่มแล้วกันการแอดซ้ำ
    let addedMembers = [];

    // ดักจับการกดปุ่ม Enter ในช่อง input สมาชิก
    memberInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // ป้องกันไม่ให้ฟอร์มหลักโดน Submit
            checkAndAddMember();
        }
    });

    // ดักจับการกดปุ่ม "เพิ่ม"
    btnAddMember.addEventListener('click', function(e) {
        checkAndAddMember();
    });

    function checkAndAddMember() {
        const username = memberInput.value.trim();
        
        if (username === '') return;

        // เช็คว่าเพิ่มไปแล้วหรือยัง
        if (addedMembers.includes(username)) {
            showError('เพิ่มผู้ใช้งานนี้ไปแล้ว');
            return;
        }

        // ยิงเช็ค Database ผ่าน API
        fetch("{{ route('groups.check-user') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ user_name: username })
        })
        .then(response => response.json())
        .then(data => {
            if (data.found) {
                addMemberToUI(data.user_name);
                memberInput.value = '';
                hideError();
            } else {
                showError(data.message);
            }
        })
        .catch(error => {
            showError('เกิดข้อผิดพลาดในการตรวจสอบ');
        });
    }

    function addMemberToUI(username) {
        addedMembers.push(username);

        // สร้าง Badge แสดงชื่อ
        const badge = document.createElement('div');
        badge.className = 'member-badge';
        badge.innerHTML = `
            ${username}
            <button type="button" class="remove-btn" onclick="removeMember(this, '${username}')">&times;</button>
        `;
        memberList.appendChild(badge);

        // สร้าง Input Hidden ส่งค่าไปให้ Laravel Controller
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'members[]'; // ส่งเป็น Array
        hiddenInput.value = username;
        hiddenInput.id = 'hidden_' + username;
        hiddenContainer.appendChild(hiddenInput);
    }

    // ลบออกจากทั้ง UI และ Hidden Input
    window.removeMember = function(buttonElement, username) {
        // ลบออกจาก UI
        buttonElement.closest('.member-badge').remove();
        // ลบออกจาก Hidden
        document.getElementById('hidden_' + username).remove();
        // ลบออกจาก Array ที่เช็คซ้ำ
        addedMembers = addedMembers.filter(name => name !== username);
    }

    function showError(msg) {
        errorSpan.innerText = msg;
        errorSpan.style.display = 'block';
    }

    function hideError() {
        errorSpan.style.display = 'none';
    }

    // กันเหนียวกรณีคนกดสร้างกลุ่มแต่ไม่ได้กรอกชื่อกลุ่ม
    function validateForm() {
        const groupName = document.getElementById('group_name').value.trim();
        if (groupName === '') {
            alert('กรุณากรอกชื่อกลุ่ม');
            return false;
        }
        return true;
    }
</script>
@endpush