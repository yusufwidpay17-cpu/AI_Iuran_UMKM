<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lapak extends Model
{
    use HasFactory;

    protected $table = 'lapak';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'kode_lapak',
        'blok_kios',
        'pedagang_id',
        'tarif_id',
        'kategori',
        'status_lapak',
        'tanggal_mulai',
        'keterangan'
    ];

    public function pedagang()
    {
        return $this->belongsTo(Pedagang::class, 'pedagang_id');
    }

    public function tarif()
    {
        return $this->belongsTo(Tarif::class, 'tarif_id');
    }
}
