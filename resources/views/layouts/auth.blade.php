<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Amanah Ledger</title>
    
    <!-- Design System CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <style>
        .auth-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: var(--background);
            padding: var(--space-md);
        }
        
        .auth-card {
            width: 100%;
            max-width: 420px;
            background-color: var(--surface-container-lowest);
            border: 1px solid var(--outline-variant);
            border-radius: var(--radius-lg);
            padding: var(--space-xl) var(--space-lg);
            box-shadow: var(--shadow-lg);
        }
        
        .auth-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: var(--space-xl);
            text-align: center;
        }
        
        .auth-logo {
            color: var(--primary);
            margin-bottom: var(--space-sm);
        }
        
        .auth-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--on-surface);
        }
        
        .auth-subtitle {
            font-size: 0.875rem;
            color: var(--outline);
            margin-top: var(--space-xs);
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-brand">
                <svg class="auth-logo" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                <h1 class="auth-title">AmanahLedger</h1>
                <p class="auth-subtitle">SI Iuran UMKM Pasar</p>
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
