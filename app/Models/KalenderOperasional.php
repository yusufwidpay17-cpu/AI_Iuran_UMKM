<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KalenderOperasional extends Model
{
    use HasFactory;

    protected $table = 'kalender_operasional';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'tanggal_mulai',
        'tanggal_selesai',
        'kategori',
        'alasan',
        'kebijakan_iuran',
        'status',
        'created_by',
        'updated_by',
        'catatan_audit',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * User yang mencatat
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User yang terakhir mengubah
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope hanya catatan yang aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope untuk tanggal tertentu
     */
    public function scopeForDate($query, $date)
    {
        $d = Carbon::parse($date)->format('Y-m-d');
        return $query->where('tanggal_mulai', '<=', $d)
                     ->where('tanggal_selesai', '>=', $d);
    }

    /**
     * Label kategori berbahasa Indonesia yang ramah pengguna
     */
    public function getKategoriLabelAttribute()
    {
        $labels = [
            'libur_nasional_pasar' => 'Libur Nasional / Libur Pasar',
            'kegiatan_pesantren' => 'Kegiatan Pesantren / Pengajian Akbar',
            'petugas_tugas_lain' => 'Petugas Mendapat Tugas Lain',
            'penagihan_dihentikan_sementara' => 'Penagihan Dihentikan Sementara',
            'keadaan_darurat' => 'Keadaan Darurat / Cuaca Ekstrem',
            'lainnya' => 'Alasan Lainnya',
        ];

        return $labels[$this->kategori] ?? ucwords(str_replace('_', ' ', $this->kategori));
    }

    /**
     * Label kebijakan iuran
     */
    public function getKebijakanLabelAttribute()
    {
        if ($this->kebijakan_iuran === 'bebas_iuran') {
            return 'Iuran Tidak Dikenakan (Bebas Iuran)';
        }
        return 'Penagihan Tertunda (Kewajiban Tetap Berlaku)';
    }

    /**
     * Cek apakah kebijakan iuran bebas
     */
    public function isBebasIuran()
    {
        return $this->kebijakan_iuran === 'bebas_iuran';
    }

    /**
     * Cek apakah aktif
     */
    public function isAktif()
    {
        return $this->status === 'aktif';
    }
}
