<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SsoGroupMapping extends Model
{
    protected $fillable = [
        'sso_group_id',
        'sso_group_name',
        'sso_group_description',
        'local_role',
        'priority',
    ];

    protected $casts = [
        'sso_group_id' => 'integer',
        'priority' => 'integer',
    ];

    /**
     * Roles disponibles par ordre de priorite (du plus eleve au plus bas)
     */
    public const ROLES = [
        'super-admin' => 100,
        'admin' => 80,
        'editor' => 60,
        'viewer' => 40,
    ];

    /**
     * Determine le role le plus prioritaire pour un ensemble de groupes SSO
     */
    public static function getRoleForGroups(array $groupNames): string
    {
        if (empty($groupNames)) {
            return 'viewer';
        }

        $mapping = self::whereIn('sso_group_name', $groupNames)
            ->whereNotNull('local_role')
            ->orderByDesc('priority')
            ->first();

        return $mapping?->local_role ?? 'viewer';
    }

    /**
     * Verifie si un role a une permission superieure ou egale a un autre
     */
    public static function hasMinimumRole(string $userRole, string $requiredRole): bool
    {
        $userPriority = self::ROLES[$userRole] ?? 0;
        $requiredPriority = self::ROLES[$requiredRole] ?? 0;

        return $userPriority >= $requiredPriority;
    }
}
