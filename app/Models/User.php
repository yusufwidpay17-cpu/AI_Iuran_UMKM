<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'nama',
        'username',
        'email',
        'password',
        'password_plain',
        'role',
        'pedagang_id',
        'foto_profil',
        'status_aktivasi',
        'activation_token',
        'activation_expires_at',
    ];

    public function getFotoProfilUrlAttribute()
    {
        if (!empty($this->foto_profil)) {
            if (str_starts_with($this->foto_profil, 'data:image') || str_starts_with($this->foto_profil, 'http://') || str_starts_with($this->foto_profil, 'https://')) {
                return $this->foto_profil;
            }
            return asset($this->foto_profil);
        }

        if ($this->pedagang && !empty($this->pedagang->foto_profil)) {
            return $this->pedagang->foto_profil_url;
        }

        return null;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'activation_token',
    ];

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'petugas_id', 'id');
    }

    public function pedagang()
    {
        return $this->belongsTo(Pedagang::class, 'pedagang_id', 'id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id', 'id');
    }

    public function isSuperadmin()
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPetugas()
    {
        return $this->role === 'petugas';
    }

    public function isPedagang()
    {
        return $this->role === 'pedagang';
    }
}
