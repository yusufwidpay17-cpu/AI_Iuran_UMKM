<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedagang extends Model
{
    use HasFactory;

    protected $table = 'pedagang';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'nama_pedagang',
        'no_ktp',
        'no_telepon',
        'alamat_lengkap',
        'rt',
        'rw',
        'desa_kelurahan',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
        'kode_pos',
        'nama_usaha',
        'kategori_usaha',
        'kategori_usaha_detail',
        'no_kios',
        'blok_kios',
        'tanggal_daftar',
        'status_pedagang',
        'status_legal',
        'foto_ktp',
        'foto_izin_usaha',
        'catatan',
        'tarif_id',
        'saldo_deposit',
    ];

    public function tarif()
    {
        return $this->belongsTo(Tarif::class, 'tarif_id', 'id');
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'pedagang_id', 'id');
    }

    public function fiturPolaBayar()
    {
        return $this->hasMany(FiturPolaBayar::class, 'pedagang_id', 'id');
    }

    public function segmentasi()
    {
        return $this->hasOne(SegmentasiPedagang::class, 'pedagang_id', 'id');
    }
}
