<?php

namespace App\Entity;

enum CategorieEvenement: string
{
    case NETTOYAGE = 'NETTOYAGE';
    case PLANTATION = 'PLANTATION';
    case SENSIBILISATION = 'SENSIBILISATION';
    case RECYCLAGE = 'RECYCLAGE';
    case CONFERENCE = 'CONFERENCE';
    case ATELIER = 'ATELIER';
    /**
     * Get the label for display
     */
    public function label(): string
    {
        return match($this) {
            self::NETTOYAGE => 'NETTOYAGE',
            self::PLANTATION => 'PLANTATION',
            self::SENSIBILISATION => 'SENSIBILISATION',
            self::RECYCLAGE => 'RECYCLAGE',
            self::CONFERENCE => 'CONFERENCE',
            self::ATELIER => 'ATELIER',
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
