<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Amanah Ledger</title>
    
    <!-- Google Fonts: Poppins (Clean & Geometric SaaS) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
    
    <!-- Design System CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    
    <style>
        html {
            height: 100%;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            margin: 0;
            padding: 24px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            min-height: 100dvh;
            box-sizing: border-box;
        }

        button, input, select, textarea, .login-input, .btn-login-primary, .auth-title {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        .auth-card {
            width: 100%;
            max-width: 380px;
            background-color: #ffffff;
            padding: 34px 28px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08), 0 4px 10px rgba(0, 0, 0, 0.04);
            border-radius: 20px;
            box-sizing: border-box;
            margin: auto;
        }
        
        .auth-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 26px;
        }

        .logo-circle-outer {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            background-color: #ffffff;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .logo-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .auth-title {
            font-size: 22px;
            font-weight: 600;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.4px;
            text-align: center;
        }

        @media (max-width: 480px) {
            body {
                padding: 16px;
            }

            .auth-card {
                max-width: 350px;
                padding: 24px 20px;
                border-radius: 18px;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            }

            .auth-brand {
                margin-bottom: 20px;
            }

            .logo-circle-outer {
                width: 58px;
                height: 58px;
                margin-bottom: 12px;
            }

            .auth-title {
                font-size: 19px;
                letter-spacing: -0.3px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-brand">
            <div class="logo-circle-outer">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="logo-image">
            </div>
            <h1 class="auth-title">@yield('title', 'Login ke akun Anda')</h1>
        </div>
            
            @if (session('success'))
                <div style="background-color: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                    {{ session('success') }}
                </div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            @yield('content')
    </div>
</body>
</html>
