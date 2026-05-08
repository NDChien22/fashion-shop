<?php

namespace App\Support;

use App\Models\User;

class AdminPermission
{
    /** @var array<string, array<int, string>> */
    protected const ABILITY_ROLES = [
        'dashboard' => ['admin', 'productmanager', 'servicescustomer'],
        'profile' => ['admin', 'productmanager', 'servicescustomer'],
        'account-manager' => ['admin'],

        'products' => ['admin', 'productmanager'],
        'orders' => ['admin', 'servicescustomer'],
        'customers' => ['admin', 'servicescustomer'],
        'revenue' => ['admin'],
        'support' => ['admin', 'servicescustomer'],
        'employees' => ['admin'],
    ];

    /** @var array<int, string> */
    protected const ADMIN_ROLES = ['admin', 'productmanager', 'servicescustomer'];

    public static function isAdminStaff(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return in_array(self::normalizeRole($user->role), self::ADMIN_ROLES, true);
    }

    public static function canAccess(?User $user, string $ability): bool
    {
        if (! self::isAdminStaff($user)) {
            return false;
        }

        $allowedRoles = self::ABILITY_ROLES[$ability] ?? ['admin'];

        return in_array(self::normalizeRole($user?->role), $allowedRoles, true);
    }

    public static function normalizeRole(?string $role): string
    {
        return strtolower((string) $role);
    }
}
