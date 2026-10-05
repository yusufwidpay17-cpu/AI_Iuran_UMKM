@extends('layouts.auth')

@section('content')
<style>
    .login-form-group {
        position: relative;
        margin-bottom: 20px;
    }
    
    .login-input {
        width: 100%;
        padding: 10px 10px 10px 35px;
        border: none;
        border-bottom: 1px solid #d2d6dc;
        font-size: 14px;
        color: #4a5568;
        background-color: transparent;
        outline: none;
        transition: border-color 0.2s;
    }
    
    .login-input:focus {
        border-bottom-color: #3182ce;
    }
    
    .login-icon {
        position: absolute;
        left: 5px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
    }
    
    .login-icon-right {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
        cursor: pointer;
    }

    .login-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        font-size: 13px;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #4a5568;
    }
    
    .checkbox-group input {
        accent-color: #3182ce;
    }

    .forgot-link {
        color: #3182ce;
        text-decoration: none;
    }

    .btn-login-primary {
        width: 100%;
        background-color: #2b77c0;
        color: white;
        border: none;
        padding: 12px;
        font-size: 14px;
        cursor: pointer;
        margin-bottom: 10px;
        transition: background 0.2s;
    }
    
    .btn-login-primary:hover {
        background-color: #2463a0;
    }

    .btn-login-secondary {
        width: 100%;
        background-color: #d2d6dc;
        color: #718096;
        border: none;
        padding: 12px;
        font-size: 14px;
        cursor: pointer;
        transition: background 0.2s;
        text-decoration: none;
        display: block;
        text-align: center;
        box-sizing: border-box;
    }
    
    .btn-login-secondary:hover {
        background-color: #cbd5e1;
    }
</style>

<form action="{{ route('login.post') }}" method="POST">
    @csrf
    
    <div class="login-form-group">
        <svg class="login-icon" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
        </svg>
        <!-- Changed to username to match existing controller, placeholder changed to Email or Username -->
        <input type="text" name="username" class="login-input" placeholder="Email / Username" value="{{ old('username') }}" required autofocus>
        @error('username')
            <div style="color: #e53e3e; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="login-form-group">
        <svg class="login-icon" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
        </svg>
        <input type="password" name="password" id="login-password" class="login-input" placeholder="Password" required>
        <svg class="login-icon-right" id="toggle-password" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
        </svg>
        @error('password')
            <div style="color: #e53e3e; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="login-options">
        <label class="checkbox-group">
            <input type="checkbox" name="remember">
            Biarkan tetap masuk
        </label>
        <a href="#" class="forgot-link">Lupa Password?</a>
    </div>
    
    <button type="submit" class="btn-login-primary">
        Login
    </button>
    
    <a href="mailto:admin@amanahledger.com?subject=Permintaan Pembuatan Akun" class="btn-login-secondary" title="Hubungi admin untuk membuat akun">
        Register
    </a>
</form>

<script>
    document.getElementById('toggle-password').addEventListener('click', function() {
        const passInput = document.getElementById('login-password');
        if (passInput.type === 'password') {
            passInput.type = 'text';
        } else {
            passInput.type = 'password';
        }
    });
</script>
@endsection
