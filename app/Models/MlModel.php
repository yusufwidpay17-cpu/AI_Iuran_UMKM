<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlModel extends Model
{
    use HasFactory;

    protected $table = 'ml_model';

    protected $fillable = [
        'nama_model',
        'algoritma',
        'jumlah_cluster',
        'parameter',
        'silhouette_score',
        'path_model_pkl',
        'path_scaler_pkl',
        'periode_data_awal',
        'periode_data_akhir',
        'is_active',
        'trained_at',
    ];

    protected $casts = [
        'parameter' => 'array',
        'periode_data_awal' => 'date',
        'periode_data_akhir' => 'date',
        'is_active' => 'boolean',
        'trained_at' => 'datetime',
    ];

    public function segmentasi()
    {
        return $this->hasMany(SegmentasiPedagang::class, 'ml_model_id', 'id');
    }
}
