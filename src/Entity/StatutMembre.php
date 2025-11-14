<?php

namespace App\Entity;

enum StatutMembre : string
{
    case ADMINISTRATEUR = 'ADMINISTRATEUR';
    case MEMBRE_ACTIF = 'MEMBRE_ACTIF';
    case MEMBRE_INACTIF = 'MEMBRE_INACTIF';
    case EN_ATTENTE = 'EN_ATTENTE';

    /**
     * Get the label for display
     */
    public function label(): string
    {
        return match($this) {
            self::ADMINISTRATEUR => 'ADMINISTRATEUR',
            self::MEMBRE_ACTIF => 'MEMBRE_ACTIF',
            self::MEMBRE_INACTIF=> 'MEMBRE_INACTIF',
            self::EN_ATTENTE => 'EN_ATTENTE',
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
