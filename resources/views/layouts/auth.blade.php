<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Amanah Ledger</title>
    
    <!-- Design System CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;600&display=swap');
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            margin: 0;
            padding: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            min-height: -webkit-fill-available;
            box-sizing: border-box;
        }

        .auth-card {
            width: 100%;
            max-width: 380px;
            background-color: #ffffff;
            padding: 36px 28px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08), 0 4px 10px rgba(0, 0, 0, 0.04);
            border-radius: 20px;
            box-sizing: border-box;
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 28px 20px;
                border-radius: 16px;
            }
        }
        
        .auth-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
        }

        .logo-circle-outer {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
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
            font-size: 24px;
            font-weight: 600;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.5px;
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
    </div>
</body>
</html>
