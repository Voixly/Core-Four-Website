<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'phone', 'role', 'password', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function assignedJobs(): HasMany
    {
        return $this->hasMany(Job::class, 'assigned_to');
    }

    public function jobContacts(): HasMany
    {
        return $this->hasMany(JobContact::class);
    }

    public function isStaffUser(): bool
    {
        return in_array($this->role, ['admin', 'agency', 'owner', 'staff'], true);
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public static function staff()
    {
        return static::query()
            ->where('is_active', true)
            ->whereIn('role', ['admin', 'agency', 'owner', 'staff'])
            ->orderBy('name');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAgency(): bool
    {
        return $this->role === 'agency';
    }

    public function isOwner(): bool
    {
        return in_array($this->role, ['admin', 'agency', 'owner'], true);
    }

    public function canManageReports(): bool
    {
        return $this->isOwner();
    }

    public function canManageEmail(): bool
    {
        return $this->isOwner();
    }

    public function canManageUsers(): bool
    {
        return $this->isOwner();
    }

    public function canManageSettings(): bool
    {
        return $this->isAdmin() || $this->isAgency();
    }
}
