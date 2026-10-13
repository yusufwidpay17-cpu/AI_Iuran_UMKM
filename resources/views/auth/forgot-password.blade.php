@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')
<style>
    .login-form-group {
        position: relative;
        margin-bottom: 22px;
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
        padding: 14px;
        font-size: 15px;
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

    .desc-text {
        text-align: center;
        color: #64748b;
        font-size: 14px;
        margin-bottom: 25px;
        line-height: 1.5;
    }
</style>

<div class="desc-text">
    Masukkan alamat email terdaftar Anda. Kode verifikasi 6 digit akan dikirimkan ke email Anda untuk merubah kata sandi akun.
</div>

<form action="{{ route('password.email') }}" method="POST">
    @csrf
    
    <div class="login-form-group">
        <svg class="login-icon" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
        </svg>
        <input type="email" name="email" class="login-input" placeholder="Alamat Email Terdaftar" value="{{ old('email') }}" required autofocus>
        @error('email')
            <div style="color: #e53e3e; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>
    
    <button type="submit" class="btn-login-primary">
        Kirim Kode Verifikasi
    </button>
    
    <a href="{{ route('login') }}" class="btn-login-secondary">
        Kembali ke Login
    </a>
</form>
@endsection
