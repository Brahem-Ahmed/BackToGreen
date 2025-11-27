<?php

namespace App\Entity;

enum StatutParticipation: string
{
    case INSCRIT = 'INSCRIT';
    case CONFIRME = 'CONFIRME';
    case PRESENT = 'PRESENT';
    case ABSENT = 'ABSENT';
    case ANNULE = 'ANNULE';
  
    /**
     * Get the label for display
     */
    public function label(): string
    {
        return match($this) {
            self::INSCRIT => 'INSCRIT',
            self::CONFIRME => 'CONFIRME',
            self::PRESENT => 'PRESENT',
            self::ABSENT => 'ABSENT',
            self::ANNULE => 'ANNULE',
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
