<?php

namespace App\Entity;

enum TypeDechet: string
{
    case PLASTIQUE = 'PLASTIQUE';
    case PAPIER = 'PAPIER';
    case VERRE = 'VERRE';
    case METAL = 'METAL';
    case ORGANIQUE = 'ORGANIQUE';
    case ELECTRONIQUE = 'ELECTRONIQUE';
    case DANGEREUX = 'DANGEREUX';

    /**
     * Retourne un libellé lisible pour l'affichage.
     */
    public function label(): string
    {
        return match($this) {
            self::PLASTIQUE => 'Déchet plastique',
            self::PAPIER => 'Déchet papier',
            self::VERRE => 'Déchet en verre',
            self::METAL => 'Déchet métallique',
            self::ORGANIQUE => 'Déchet organique',
            self::ELECTRONIQUE => 'Déchet électronique',
            self::DANGEREUX => 'Déchet dangereux',
        };
    }

    /**
     * Retourne toutes les valeurs de l'énumération sous forme de tableau.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
