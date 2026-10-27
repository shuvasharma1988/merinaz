<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if the user has any of the given roles by slug.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles->pluck('slug')->intersect($roles)->isNotEmpty();
    }

    /**
     * The roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Determine if the user has a specific role by slug.
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    /**
     * Determine if the user has admin-level access (Owner, Manager, Staff).
     */
    public function isAdmin(): bool
    {
        return $this->roles()->whereIn('slug', ['owner', 'manager', 'staff'])->exists();
    }

    /**
     * Determine if the user has a specific granular permission.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        // 1. Super-admin override: Owners automatically pass all permission checks
        if ($this->hasRole('owner')) {
            return true;
        }

        // 2. Check direct granular permissions
        if ($this->permissions()->where('slug', $permissionSlug)->exists()) {
            return true;
        }

        // 3. Check role-based permissions
        foreach ($this->roles as $role) {
            if ($role->permissions()->where('slug', $permissionSlug)->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all permissions assigned to the user (directly and via roles).
     */
    public function getAllPermissions()
    {
        // 1. Super-admin override: Owners implicitly hold every permission in the system
        if ($this->hasRole('owner')) {
            return Permission::all();
        }

        $rolePermissions = $this->roles->flatMap->permissions;
        $directPermissions = $this->permissions;

        return $rolePermissions->merge($directPermissions)->unique('id');
    }

    /**
     * The direct granular permissions that belong to the user.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function staff()
    {
        return $this->hasOne(StaffProfile::class, 'user_id');
    }

    /**
     * Get the CRM profile associated with the user.
     */
    public function clientProfile(): HasOne
    {
        return $this->hasOne(ClientProfile::class);
    }

    /**
     * Get the notes written about this user (as a client).
     */
    public function clientNotes(): HasMany
    {
        return $this->hasMany(ClientNote::class, 'client_id');
    }

    /**
     * Get the appointments booked by this user.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'client_id');
    }

    /**
     * The locations where this staff member works.
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'location_staff', 'staff_id', 'location_id');
    }
}
