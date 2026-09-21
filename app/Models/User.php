<?php

namespace App\Models;

use App\Support\Rbac;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_RECEPTIONIST = 'receptionist';

    public const ROLE_OPERATOR = 'operator';

    public const ROLE_STAFF = 'staff';

    public const ROLE_DISPLAY_OPERATOR = 'display_operator';

    public const ROLES = [
        self::ROLE_SUPER_ADMIN,
        self::ROLE_ADMIN,
        self::ROLE_RECEPTIONIST,
        self::ROLE_OPERATOR,
        self::ROLE_STAFF,
        self::ROLE_DISPLAY_OPERATOR,
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'counter_id',
        'service_id',
        'doctor_id',
        'is_active',
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
            'is_active' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN], true);
    }

    public function hasPermission(string $permission): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return Rbac::roleHas($this->role ?? '', $permission);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public static function assignableRoles(?User $actor): array
    {
        if ($actor?->isSuperAdmin()) {
            return self::ROLES;
        }

        return array_values(array_diff(self::ROLES, [self::ROLE_SUPER_ADMIN]));
    }

    public function counter()
    {
        return $this->belongsTo(Counter::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function assignedDoctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
}
