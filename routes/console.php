<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mail:test {recipient?}', function ($recipient = null) {
    $host = config('mail.mailers.smtp.host');
    $port = config('mail.mailers.smtp.port');
    $username = config('mail.mailers.smtp.username');
    $fromAddress = config('mail.from.address');
    $to = $recipient ?: $username;

    $this->info("=== PENGUJIAN KONEKSI SMTP GMAIL ===");
    $this->line("Host: {$host}");
    $this->line("Port: {$port}");
    $this->line("Username: {$username}");
    $this->line("From: {$fromAddress}");
    $this->line("Tujuan Uji Coba: {$to}");
    $this->line("------------------------------------");

    if (empty($username) || $username === 'email_anda@gmail.com') {
        $this->error("[PERINGATAN] MAIL_USERNAME di file .env masih berupa nilai contoh.");
        $this->line("Silakan masukkan alamat Gmail Anda pada variabel MAIL_USERNAME di file .env.");
        return 1;
    }

    try {
        $this->comment("Menghubungi server SMTP Gmail dan mengirim email uji coba...");
        \Illuminate\Support\Facades\Mail::raw(
            "Halo,\n\nIni adalah email uji coba dari sistem AmanahLedger di localhost Anda.\nKonfigurasi SMTP Gmail Anda telah berhasil terhubung dengan sempurna!\n\nWaktu pengujian: " . date('d/m/Y H:i:s') . "\n\nSalam,\nTim AmanahLedger",
            function ($message) use ($to) {
                $message->to($to)->subject('Uji Coba Pengiriman Email - AmanahLedger');
            }
        );
        $this->info("[SUKSES] Email uji coba berhasil dikirimkan ke {$to}!");
        $this->info("Silakan cek kotak masuk atau folder spam di Gmail Anda.");
        return 0;
    } catch (\Exception $e) {
        $this->error("[GAGAL] Pengiriman email tidak berhasil:");
        $this->line($e->getMessage());
        return 1;
    }
})->purpose('Menguji koneksi dan pengiriman email SMTP Gmail dari localhost');
