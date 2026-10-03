<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegmentasiPedagang extends Model
{
    use HasFactory;

    protected $table = 'segmentasi_pedagang';

    protected $fillable = [
        'pedagang_id',
        'ml_model_id',
        'cluster_index',
        'label_segmen',
        'fitur_snapshot',
        'tanggal_analisis',
    ];

    protected $casts = [
        'fitur_snapshot' => 'array',
        'tanggal_analisis' => 'datetime',
    ];

    public function pedagang()
    {
        return $this->belongsTo(Pedagang::class, 'pedagang_id', 'id');
    }

    public function model()
    {
        return $this->belongsTo(MlModel::class, 'ml_model_id', 'id');
    }
}
