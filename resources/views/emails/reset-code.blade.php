<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kode Verifikasi Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 24px; color: #1e293b;">
    <div style="max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 32px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        <div style="text-align: center; margin-bottom: 24px;">
            <h2 style="color: #003d9b; margin: 0; font-size: 22px;">{{ config('app.name', 'AmanahLedger') }}</h2>
            <p style="color: #64748b; font-size: 13px; margin-top: 4px;">Sistem Informasi Iuran UMKM</p>
        </div>
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">
        <p style="font-size: 15px; line-height: 1.5;">Halo <strong>{{ $nama }}</strong>,</p>
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
            Kami menerima permintaan untuk mereset kata sandi akun Anda. Gunakan kode verifikasi di bawah ini untuk melanjutkan:
        </p>
        <div style="text-align: center; margin: 28px 0; background: #eff6ff; border: 1.5px dashed #3b82f6; border-radius: 10px; padding: 18px;">
            <div style="font-size: 11px; font-weight: bold; color: #1e40af; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;">KODE VERIFIKASI ANDA</div>
            <div style="font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #1d4ed8; font-family: monospace;">{{ $code }}</div>
            <div style="font-size: 12px; color: #64748b; margin-top: 6px;">Kode ini berlaku selama <strong>15 menit</strong></div>
        </div>
        <p style="font-size: 13px; line-height: 1.5; color: #64748b;">
            Kode verifikasi ini bersifat rahasia dan hanya dapat digunakan satu kali. Jangan berikan kode ini kepada pihak mana pun, termasuk petugas atau administrator.
        </p>
        <p style="font-size: 13px; line-height: 1.5; color: #64748b;">
            Jika Anda tidak meminta perubahan kata sandi, Anda dapat mengabaikan email ini dengan aman.
        </p>
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;">
        <p style="font-size: 12px; color: #94a3b8; text-align: center; margin: 0;">
            &copy; {{ date('Y') }} {{ config('app.name', 'AmanahLedger') }}. Seluruh hak cipta dilindungi.
        </p>
    </div>
</body>
</html>
