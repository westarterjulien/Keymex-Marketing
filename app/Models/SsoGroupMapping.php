<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SsoGroupMapping extends Model
{
    protected $fillable = [
        'sso_group_id',
        'sso_group_name',
        'sso_group_description',
        'is_allowed',
    ];

    protected $casts = [
        'sso_group_id' => 'integer',
        'is_allowed' => 'boolean',
    ];

    /**
     * Verifie si au moins un des groupes SSO est autorise a se connecter
     */
    public static function isGroupAllowed(array $groupNames): bool
    {
        if (empty($groupNames)) {
            return false;
        }

        return self::whereIn('sso_group_name', $groupNames)
            ->where('is_allowed', true)
            ->exists();
    }

    /**
     * Retourne les noms des groupes autorises
     */
    public static function getAllowedGroupNames(): array
    {
        return self::where('is_allowed', true)
            ->pluck('sso_group_name')
            ->toArray();
    }
}
