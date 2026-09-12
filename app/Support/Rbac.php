<?php

namespace App\Support;

use App\Models\Setting;

class Rbac
{
    public const OVERRIDE_KEY = 'rbac.overrides';

    protected static ?array $overrideCache = null;

    public static function permissions(): array
    {
        return config('rbac.permissions', []);
    }

    public static function roles(): array
    {
        return config('rbac.roles', []);
    }

    public static function overrides(): array
    {
        if (static::$overrideCache !== null) {
            return static::$overrideCache;
        }

        try {
            $raw = Setting::get(static::OVERRIDE_KEY);
        } catch (\Throwable $e) {
            $raw = null;
        }

        $data = $raw ? json_decode($raw, true) : [];

        return static::$overrideCache = is_array($data) ? $data : [];
    }

    public static function clearCache(): void
    {
        static::$overrideCache = null;
    }

    public static function rolePermissions(string $role): array
    {
        if ($role === 'super_admin') {
            return ['*'];
        }

        $overrides = static::overrides();
        if (isset($overrides[$role]) && is_array($overrides[$role])) {
            $valid = static::permissions();

            return array_values(array_intersect($overrides[$role], $valid));
        }

        $roles = static::roles();

        return $roles[$role]['permissions'] ?? [];
    }

    public static function saveOverrides(array $matrix): void
    {
        $validRoles = array_keys(static::roles());
        $validPerms = static::permissions();
        $clean = [];

        foreach ($matrix as $role => $perms) {
            if ($role === 'super_admin' || ! in_array($role, $validRoles, true)) {
                continue;
            }
            $clean[$role] = array_values(array_intersect((array) $perms, $validPerms));
        }

        Setting::set(static::OVERRIDE_KEY, json_encode($clean));
        static::clearCache();
    }

    public static function roleLabel(string $role): string
    {
        return static::roles()[$role]['label'] ?? ucfirst(str_replace('_', ' ', $role));
    }

    public static function roleHas(string $role, string $permission): bool
    {
        $perms = static::rolePermissions($role);

        return in_array('*', $perms, true) || in_array($permission, $perms, true);
    }
}
