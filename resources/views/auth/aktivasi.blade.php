@extends('layouts.auth')

@section('title', 'Aktivasi Akun & Pembuatan Kata Sandi')

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
        border-color: #0b5331;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(11, 83, 49, 0.15);
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
        background-color: #0b5331;
        color: white;
        border: none;
        border-radius: 12px;
        padding: 14px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        margin-bottom: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(11, 83, 49, 0.2);
    }
    
    .btn-login-primary:hover {
        background-color: #083c23;
        box-shadow: 0 6px 16px rgba(11, 83, 49, 0.3);
        transform: translateY(-1px);
    }

    .info-card-activation {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
        font-size: 13px;
        padding: 14px;
        border-radius: 12px;
        margin-bottom: 20px;
        line-height: 1.5;
    }
</style>

<div class="info-card-activation">
    <div style="font-weight: 700; font-size: 14px; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0110 0v4"></path></svg>
        Hak Akses Telah Diberikan
    </div>
    <div>
        Halo <strong>{{ $user->nama }}</strong>, Anda telah disetujui untuk mengakses sistem privat <strong>{{ config('app.name', 'AmanahLedger') }}</strong>
        @if($user->role === 'pedagang' && $user->pedagang)
            (Lapak: <strong>{{ $user->pedagang->nama_usaha ?? $user->pedagang->nama_pedagang }}</strong>).
        @else
            sebagai <strong>{{ ucfirst($user->role) }}</strong>.
        @endif
    </div>
    <div style="font-size: 12px; color: #15803d; margin-top: 6px;">
        Silakan tentukan kata sandi baru Anda sendiri di bawah ini untuk mengaktifkan akun.
    </div>
</div>

<form action="{{ route('aktivasi.process') }}" method="POST">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <!-- Info Akun (Read-only) -->
    <div class="login-form-group">
        <svg class="login-icon" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
        </svg>
        <input type="text" class="login-input" value="{{ $user->username }} ({{ $user->email }})" readonly style="background-color: #e2e8f0; color: #475569; font-weight: 600;">
    </div>

    <!-- Password Baru -->
    <div class="login-form-group">
        <svg class="login-icon" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
        </svg>
        <input type="password" name="password" id="act-password" class="login-input" placeholder="Buat Kata Sandi Baru (Min. 6 Karakter)" required autofocus>
        <svg class="login-icon-right" onclick="togglePassword('act-password')" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
        </svg>
        @error('password')
            <div style="color: #e53e3e; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <!-- Konfirmasi Password -->
    <div class="login-form-group">
        <svg class="login-icon" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
        </svg>
        <input type="password" name="password_confirmation" id="act-confirm-password" class="login-input" placeholder="Ulangi Kata Sandi Baru" required>
        <svg class="login-icon-right" onclick="togglePassword('act-confirm-password')" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
        </svg>
    </div>

    <button type="submit" class="btn-login-primary">
        Simpan Kata Sandi & Masuk ke Sistem
    </button>

    <div style="text-align: center; margin-top: 12px;">
        <a href="{{ route('login') }}" style="color: #64748b; font-size: 13px; text-decoration: none;">
            Sudah pernah mengaktifkan akun? Masuk di sini
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
