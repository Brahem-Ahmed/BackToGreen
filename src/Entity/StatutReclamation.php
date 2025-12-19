<?php

namespace App\Entity;

enum StatutReclamation: string
{
    case EN_ATTENTE = 'EN_ATTENTE';
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
            self::EN_ATTENTE => 'En attente',
            self::NOUVELLE => 'Nouvelle',
            self::EN_COURS => 'En cours',
            self::RESOLUT => 'Résolue',
            self::FERMEE => 'Fermée',
            self::REJECTEE => 'Rejetée',
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