<?php

namespace App\Entity;

enum StatutMembre : string
{
    case MEMBRE_ACTIF = 'MEMBRE_ACTIF';
    case MEMBRE_INACTIF = 'MEMBRE_INACTIF';

    /**
     */
    public function label(): string
    {
        return match($this) {
            self::MEMBRE_ACTIF => 'MEMBRE_ACTIF',
            self::MEMBRE_INACTIF=> 'MEMBRE_INACTIF',
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
