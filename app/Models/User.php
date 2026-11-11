<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'business_id',
        'is_platform_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function business() { return $this->belongsTo(Business::class); }
    public function assignedBookings() { return $this->hasMany(Booking::class, 'assigned_user_id'); }
    public function isOwner(): bool { return $this->role === 'owner'; }
    public function isDemo(): bool { return $this->email === 'demo@cleanflow.invalid' && $this->business?->slug === 'cleanflow-demo'; }
    public function canManageBusiness(): bool { return $this->is_platform_admin || in_array($this->role, ['owner', 'admin'], true); }
}
