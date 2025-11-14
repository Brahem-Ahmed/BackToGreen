<?php

namespace App\Entity;

enum PrioriteReclamation: string
{
    case BASSE = 'BASSE';
    case MOYENNE = 'MOYENNE';
    case HAUTE= 'HAUTE';
    case URGENTE = 'URGENTE';
    
  
    /**
     * Get the label for display
     */
    public function label(): string
    {
        return match($this) {
            self::BASSE => 'BASSE',
            self::MOYENNE => 'MOYENNE',
            self::HAUTE => 'HAUTE',
            self::URGENTE => 'URGENTE',
            
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