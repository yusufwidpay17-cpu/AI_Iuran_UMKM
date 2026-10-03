<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_log';
    protected $keyType = 'string';
    public $incrementing = false;
    
    // Disable default timestamps since we only have created_at
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'aksi',
        'deskripsi',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
