<?php

namespace App\Support;

use App\Models\Setting;

class Rbac
{
    public const OVERRIDE_KEY = 'rbac.overrides';

    /**
     * Container key for the memoised override list.
     *
     * This used to be a static property. A static survives for the lifetime of
     * the PHP process, so under a long-lived worker (Octane, queue workers, and
     * every `php artisan test` process) a stale override list could outlive the
     * request that populated it, and a test could inherit permissions from a
     * previous test. Binding it as a scoped instance ties the memo to a single
     * request instead: the container flushes it between requests and rebuilds
     * the app per test.
     */
    protected const OVERRIDE_SCOPED = 'rbac.overrides.memo';

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
        if (! app()->bound(static::OVERRIDE_SCOPED)) {
            app()->scoped(static::OVERRIDE_SCOPED, fn (): array => static::loadOverrides());
        }

        return app(static::OVERRIDE_SCOPED);
    }

    protected static function loadOverrides(): array
    {
        try {
            $raw = Setting::get(static::OVERRIDE_KEY);
        } catch (\Throwable $e) {
            $raw = null;
        }

        $data = $raw ? json_decode($raw, true) : [];

        return is_array($data) ? $data : [];
    }

    public static function clearCache(): void
    {
        if (app()->bound(static::OVERRIDE_SCOPED)) {
            app()->forgetInstance(static::OVERRIDE_SCOPED);
        }
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
