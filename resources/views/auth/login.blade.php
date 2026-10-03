@extends('layouts.auth')

@section('content')
<form action="{{ route('login.post') }}" method="POST">
    @csrf
    
    <div class="form-group">
        <label for="username" class="form-label">Username</label>
        <input type="text" 
               name="username" 
               id="username" 
               class="form-control" 
               placeholder="Masukkan username Anda" 
               value="{{ old('username') }}" 
               required 
               autofocus>
        @error('username')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>
    
    <div class="form-group" style="margin-bottom: var(--space-lg);">
        <label for="password" class="form-label">Password</label>
        <input type="password" 
               name="password" 
               id="password" 
               class="form-control" 
               placeholder="Masukkan password Anda" 
               required>
        @error('password')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>
    
    <button type="submit" class="btn btn-primary btn-full" style="min-height: 48px; box-shadow: var(--shadow-btn);">
        Masuk Aplikasi
    </button>
</form>
@endsection
