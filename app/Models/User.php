<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    const ROLE_ADMIN = 'admin';
    const ROLE_COMPANY = 'company';
    const ROLE_CLIENT = 'client';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'phone',
        'company_name',
        'company_address',
        'vat_number',
        'cr',
        'approval_status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'reviewed_at' => 'datetime',
    ];

    public function isAdmin()
    {
        return $this->getRawOriginal('type') === self::ROLE_ADMIN;
    }
    public function isCompany()
    {
        return $this->getRawOriginal('type') === self::ROLE_COMPANY;
    }
    public function isClient()
    {
        return $this->getRawOriginal('type') === self::ROLE_CLIENT;
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'client_id');
    }

    public function screens()
    {
        return $this->hasMany(Screen::class, 'company_id');
    }
}
