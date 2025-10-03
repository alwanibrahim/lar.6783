<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'referral_code',
        'referred_by',
        'balance',
        'is_verified',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'balance' => 'decimal:2',
        'is_verified' => 'boolean',
        'role' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (!$user->referral_code) {
                $user->referral_code = Str::upper(Str::random(8));
            }
        });
    }

    // Relationships
    public function otpCodes()
    {
        return $this->hasMany(OtpCode::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function referredUsers()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function commissions()
    {
        return $this->hasMany(AffiliateCommission::class, 'referrer_id');
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Role helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
}
