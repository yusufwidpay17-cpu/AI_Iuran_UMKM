<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    use HasFactory;

    protected $table = 'tarif';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'nama_tarif',
        'kategori',
        'nominal_harian',
        'berlaku_mulai',
        'aktif',
    ];

    public function pedagang()
    {
        return $this->hasMany(Pedagang::class, 'tarif_id', 'id');
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'tarif_id', 'id');
    }
}
