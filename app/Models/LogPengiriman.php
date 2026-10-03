<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogPengiriman extends Model
{
    use HasFactory;

    protected $table = 'log_pengiriman';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'waktu_kirim',
        'penerima',
        'tipe_channel',
        'status',
        'response_api',
        'catatan',
    ];

    protected $casts = [
        'waktu_kirim' => 'datetime',
    ];
}
