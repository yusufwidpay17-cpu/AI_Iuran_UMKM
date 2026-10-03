<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranPedagang extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_pedagang';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nama_calon',
        'no_ktp',
        'no_telepon',
        'alamat',
        'nama_usaha',
        'kategori_usaha',
        'no_kios',
        'blok_kios',
        'tarif_id',
        'tanggal_daftar',
        'status_pendaftaran',
        'catatan'
    ];

    public function tarif()
    {
        return $this->belongsTo(Tarif::class, 'tarif_id');
    }
}
