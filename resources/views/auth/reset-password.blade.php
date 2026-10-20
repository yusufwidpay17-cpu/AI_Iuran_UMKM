@extends('layouts.auth')

@section('title', 'Verifikasi & Reset Password')

@section('content')
<style>
    .login-form-group {
        position: relative;
        margin-bottom: 20px;
    }
    
    .login-input {
        width: 100%;
        padding: 12px 16px 12px 42px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 14px;
        color: #4a5568;
        background-color: #f8fafc;
        outline: none;
        box-sizing: border-box;
        transition: all 0.3s ease;
    }
    
    .login-input:focus {
        border-color: #3182ce;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.15);
    }
    
    .login-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }
    
    .login-icon-right {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .login-icon-right:hover {
        color: #64748b;
    }

    .btn-login-primary {
        width: 100%;
        background-color: #2563eb;
        color: white;
        border: none;
        border-radius: 12px;
        padding: 14px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        margin-bottom: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }
    
    .btn-login-primary:hover {
        background-color: #1d4ed8;
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
        transform: translateY(-1px);
    }

    .btn-login-secondary {
        width: 100%;
        background-color: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
        text-align: center;
        box-sizing: border-box;
    }
    
    .btn-login-secondary:hover {
        background-color: #f1f5f9;
        color: #1e293b;
    }
</style>

@if(session('success'))
    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; font-size: 13px; padding: 12px 14px; border-radius: 10px; margin-bottom: 18px; line-height: 1.5;">
        {{ session('success') }}
    </div>
@endif

@if(session('demo_code'))
    <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; font-size: 13px; padding: 12px 14px; border-radius: 10px; margin-bottom: 18px; line-height: 1.5;">
        <div style="font-weight: 700; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0;"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Mode Simulasi Pengujian Aktif:</span>
        </div>
        <div style="margin: 4px 0;">Kode verifikasi OTP 6-digit Anda adalah: <strong style="font-size: 1.25rem; letter-spacing: 2px; font-family: monospace; background: #ffffff; padding: 2px 8px; border-radius: 6px; border: 1px solid #93c5fd; color: #1d4ed8;">{{ session('demo_code') }}</strong></div>
        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">(Ketikkan kode di atas ke kolom isian kode verifikasi di bawah)</div>
    </div>
@endif

@if(session('warning'))
    <div style="background: #fffbeb; border: 1px solid #fde68a; color: #92400e; font-size: 13px; padding: 10px 14px; border-radius: 10px; margin-bottom: 18px; line-height: 1.5;">
        {{ session('warning') }}
    </div>
@endif

<form action="{{ route('password.update') }}" method="POST">
    @csrf
    
    <!-- Email Terdaftar -->
    <div class="login-form-group">
        <svg class="login-icon" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
        </svg>
        <input type="email" name="email" class="login-input" value="{{ old('email', $email) }}" placeholder="Alamat Email Terdaftar" required {{ $email ? 'readonly style=background-color:#e2e8f0;color:#64748b;' : '' }}>
        @error('email')
            <div style="color: #e53e3e; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>
    
    <!-- Kode Verifikasi (6 Digit) -->
    <div class="login-form-group">
        <svg class="login-icon" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v-2l2-2v-1.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"></path>
        </svg>
        <input type="text" name="token" id="token" class="login-input" placeholder="Masukkan 6 Digit Kode Verifikasi" value="{{ old('token') }}" maxlength="6" inputmode="numeric" required autofocus style="font-weight: 700; letter-spacing: 2px; font-family: monospace;">
        @error('token')
            <div style="color: #e53e3e; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>
    
    <!-- Password Baru -->
    <div class="login-form-group">
        <svg class="login-icon" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
        </svg>
        <input type="password" name="password" id="new-password" class="login-input" placeholder="Kata Sandi Baru (Min. 6 Karakter)" required>
        <svg class="login-icon-right" onclick="togglePassword('new-password')" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
        </svg>
        @error('password')
            <div style="color: #e53e3e; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <!-- Konfirmasi Password Baru -->
    <div class="login-form-group">
        <svg class="login-icon" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
        </svg>
        <input type="password" name="password_confirmation" id="confirm-password" class="login-input" placeholder="Ulangi Kata Sandi Baru" required>
        <svg class="login-icon-right" onclick="togglePassword('confirm-password')" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
        </svg>
    </div>
    
    <button type="submit" class="btn-login-primary">
        Simpan Sandi Baru
    </button>
    
    <div style="display: flex; gap: 8px; margin-top: 8px;">
        <a href="{{ route('password.request') }}" class="btn-login-secondary" style="flex: 1;">
            Kirim Ulang Kode
        </a>
        <a href="{{ route('login') }}" class="btn-login-secondary" style="flex: 1;">
            Kembali ke Login
        </a>
    </div>
</form>

<script>
    function togglePassword(inputId) {
        const passInput = document.getElementById(inputId);
        if (passInput.type === 'password') {
            passInput.type = 'text';
        } else {
            passInput.type = 'password';
        }
    }
</script>
@endsection
