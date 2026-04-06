<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    // --- DAFTAR KONSTANTA ---
    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_ADMIN  = 'admin';
    public const ROLE_USER   = 'user';

    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_FAILED   = 'failed';

    protected $fillable = [
        'name',
        'email',
        'no_hp',
        'password',
        'role',
        'status',
        'payment_proof',
        'referral_code',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // =========================================================
    // HELPER METHODS
    // =========================================================

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isAdmin(): bool
    {
        // Superadmin juga memiliki hak sebagai Admin
        return $this->role === self::ROLE_ADMIN || $this->role === self::ROLE_SUPERADMIN;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    // --- RELASI DATABASE ---

    public function coins()
    {
        return $this->hasMany(Coin::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Relasi ke tabel referral_codes (kode yang dipakai user saat daftar)
    public function referralCodeData()
    {
        return $this->belongsTo(ReferralCode::class, 'referral_code', 'code');
    }

} // <--- KURUNG KURAWAL PENUTUP CLASS USER