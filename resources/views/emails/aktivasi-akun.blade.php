<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Undangan Akses & Aktivasi Akun - {{ config('app.name', 'AmanahLedger') }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 24px; color: #1e293b;">
    <div style="max-width: 540px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 32px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        <div style="text-align: center; margin-bottom: 24px;">
            <h2 style="color: #0b5331; margin: 0; font-size: 24px;">{{ config('app.name', 'AmanahLedger') }}</h2>
            <p style="color: #64748b; font-size: 13px; margin-top: 4px;">Sistem Informasi Iuran UMKM Pesantren</p>
        </div>
        
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">
        
        <p style="font-size: 15px; line-height: 1.5;">Assalamu’alaikum Wr. Wb. / Salam Sejahtera,</p>
        <p style="font-size: 15px; line-height: 1.5;">Halo <strong>{{ $user->nama }}</strong>,</p>
        
        <p style="font-size: 14px; line-height: 1.6; color: #334155;">
            Pihak administrator/pengelola telah memberikan izin hak akses kepada Anda untuk menggunakan sistem privat <strong>{{ config('app.name', 'AmanahLedger') }}</strong>
            @if($user->role === 'pedagang' && $user->pedagang)
                sebagai pengelola lapak <strong>{{ $user->pedagang->nama_usaha ?? $user->pedagang->nama_pedagang }}</strong> (Kios: {{ $user->pedagang->no_kios ?? '-' }}).
            @else
                dengan hak akses (role) <strong>{{ ucfirst($user->role) }}</strong>.
            @endif
        </p>

        <p style="font-size: 14px; line-height: 1.6; color: #334155;">
            Demi menjaga keamanan akun privat Anda, silakan klik tombol di bawah ini untuk menentukan kata sandi (password) Anda sendiri sebelum masuk ke sistem:
        </p>

        <div style="text-align: center; margin: 32px 0;">
            <a href="{{ $activationUrl }}" 
               style="background-color: #0b5331; color: #ffffff; padding: 14px 28px; font-size: 15px; font-weight: bold; text-decoration: none; border-radius: 8px; display: inline-block; box-shadow: 0 4px 10px rgba(11, 83, 49, 0.25);">
                Aktivasi Akun & Tentukan Password
            </a>
            <div style="font-size: 12px; color: #64748b; margin-top: 10px;">
                Tautan ini berlaku selama <strong>24 jam</strong> dan bersifat satu kali pakai.
            </div>
        </div>

        <div style="background-color: #f1f5f9; border-radius: 8px; padding: 14px; margin-bottom: 24px; font-size: 12px; color: #475569; line-height: 1.5;">
            <strong>Informasi Login Anda:</strong><br>
            • Username: <strong style="color: #0b5331;">{{ $user->username }}</strong><br>
            • Email Terdaftar: <strong style="color: #0b5331;">{{ $user->email }}</strong><br>
            <em>(Setelah membuat password, Anda dapat login menggunakan username atau email di atas).</em>
        </div>

        <p style="font-size: 12px; line-height: 1.5; color: #64748b;">
            Jika tombol di atas tidak dapat diklik, silakan salin dan tempel alamat tautan berikut pada browser Anda:<br>
            <a href="{{ $activationUrl }}" style="color: #0b5331; word-break: break-all;">{{ $activationUrl }}</a>
        </p>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;">
        <p style="font-size: 12px; color: #94a3b8; text-align: center; margin: 0;">
            &copy; {{ date('Y') }} {{ config('app.name', 'AmanahLedger') }}. Seluruh hak cipta dilindungi.
        </p>
    </div>
</body>
</html>
