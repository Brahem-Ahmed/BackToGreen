<?php

namespace App\Entity;

enum StatutReclamation: string
{
    case NOUVELLE = 'NOUVELLE';
    case EN_COURS = 'EN_COURS';
    case RESOLUT = 'RESOLUT';
    case FERMEE = 'FERMEE';
    case REJECTEE = 'REJECTEE';
  
    /**
     * Get the label for display
     */
    public function label(): string
    {
        return match($this) {
            self::NOUVELLE => 'NOUVELLE',
            self::EN_COURS => 'EN_COURS',
            self::RESOLUT => 'RESOLUT',
            self::FERMEE => 'FERMEE',
            self::REJECTEE => 'REJECTEE',
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