<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiturPolaBayar extends Model
{
    use HasFactory;

    protected $table = 'fitur_pola_bayar';
    // Autoincrementing bigint id is the default, so no need for custom primary key config

    protected $fillable = [
        'pedagang_id',
        'periode_awal',
        'periode_akhir',
        'total_hari_tagihan',
        'jumlah_lunas',
        'jumlah_terlambat',
        'jumlah_bayar_sebagian',
        'rata_rata_keterlambatan_hari',
        'rasio_ketepatan_bayar',
        'total_nominal_tagihan',
        'total_nominal_dibayar',
        'total_tunggakan',
        'dihitung_pada',
    ];

    protected $casts = [
        'periode_awal' => 'date',
        'periode_akhir' => 'date',
        'dihitung_pada' => 'datetime',
    ];

    public function pedagang()
    {
        return $this->belongsTo(Pedagang::class, 'pedagang_id', 'id');
    }
}
