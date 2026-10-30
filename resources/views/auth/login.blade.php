@extends('layouts.auth')

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

    .login-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 26px;
        font-size: 14px;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #64748b;
        cursor: pointer;
    }
    
    .checkbox-group input {
        width: 16px;
        height: 16px;
        accent-color: #2563eb;
        cursor: pointer;
    }

    .forgot-link {
        color: #2563eb;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
    }
    
    .forgot-link:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }

    .btn-login-primary {
        width: 100%;
        background-color: #2563eb;
        color: white;
        border: none;
        border-radius: 12px;
        padding: 13px;
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
        padding: 13px;
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

    @media (max-width: 480px) {
        .login-form-group {
            margin-bottom: 16px;
        }
        
        .login-input {
            padding: 11px 14px 11px 38px;
            font-size: 13.5px;
            border-radius: 10px;
        }

        .login-icon {
            left: 12px;
            width: 15px;
            height: 15px;
        }

        .login-icon-right {
            right: 12px;
            width: 15px;
            height: 15px;
        }

        .login-options {
            margin-bottom: 20px;
            font-size: 12.5px;
        }

        .checkbox-group {
            gap: 6px;
            font-size: 12.5px;
        }

        .checkbox-group input {
            width: 15px;
            height: 15px;
        }

        .forgot-link {
            font-size: 12.5px;
        }

        .btn-login-primary {
            padding: 11px;
            font-size: 14px;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .btn-login-secondary {
            padding: 11px;
            font-size: 14px;
            border-radius: 10px;
        }
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
        <a href="{{ route('password.request') }}" class="forgot-link">Lupa Password?</a>
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
