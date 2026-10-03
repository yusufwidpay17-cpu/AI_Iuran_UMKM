<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'pedagang_id',
        'tarif_id',
        'jenis_iuran',
        'petugas_id',
        'tanggal_tagihan',
        'nominal_tagihan',
        'nominal_dibayar',
        'status_bayar',
        'waktu_bayar',
        'metode_bayar',
        'no_bukti',
        'catatan',
    ];

    protected $casts = [
        'waktu_bayar' => 'datetime',
        'tanggal_tagihan' => 'date',
    ];

    public function pedagang()
    {
        return $this->belongsTo(Pedagang::class, 'pedagang_id', 'id');
    }

    public function tarif()
    {
        return $this->belongsTo(Tarif::class, 'tarif_id', 'id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id', 'id');
    }
}
