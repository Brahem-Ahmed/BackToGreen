<?php

namespace App\Entity;

enum RoleUser: string
{
    case ADMIN = 'ROLE_ADMIN';
    case MODERATEUR = 'ROLE_MODERATEUR';
    case MEMBRE = 'ROLE_MEMBRE';
    case VISITEUR = 'ROLE_VISITEUR';

    /**
     * Get the label for display
     */
    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::MODERATEUR => 'Moderator',
            self::MEMBRE => 'Member',
            self::VISITEUR => 'Visitor',
        };
    }

    /**
     * Get all role values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
