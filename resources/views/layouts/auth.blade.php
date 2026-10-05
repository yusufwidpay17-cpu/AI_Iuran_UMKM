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
            background-color: #353f4b; /* Dark slate background from mockup */
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .auth-card {
            width: 100%;
            max-width: 360px;
            background-color: #ffffff;
            padding: 40px 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            border-radius: 2px;
        }
        
        .auth-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 25px;
        }

        .logo-circle-outer {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            background-color: #ffffff;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .logo-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 8px;
        }
        
        .auth-title {
            font-size: 22px;
            font-weight: 300;
            color: #4a5568;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-brand">
            <div class="logo-circle-outer">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="logo-image">
            </div>
            <h1 class="auth-title">Login ke akun Anda</h1>
        </div>
            
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
