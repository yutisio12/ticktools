<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'badge_id',
        'name',
        'email',
        'date_of_birth',
        'role_id',
        'department',
        'position',
        'phone',
        'avatar',
        'is_active',
        'last_login_at',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ─── Relationships ───

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function ticketActivities(): HasMany
    {
        return $this->hasMany(TicketActivity::class);
    }

    // ─── Role Helpers ───

    public function isAdmin(): bool
    {
        return $this->role?->slug === 'admin';
    }

    public function isItLead(): bool
    {
        return $this->role?->slug === 'it-lead';
    }

    public function isItStaff(): bool
    {
        return $this->role?->slug === 'it-staff';
    }

    public function isUser(): bool
    {
        return $this->role?->slug === 'user';
    }

    public function hasRole(string $role): bool
    {
        return $this->role?->slug === $role;
    }

    // ─── Accessors ───

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=1e3a5f&color=06b6d4&bold=true';
    }
}
