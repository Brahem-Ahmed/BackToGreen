<?php

namespace App\Entity;

enum StatutCollecte: string
{
    case EN_ATTENTE = 'EN_ATTENTE';
    case VALIDEE = 'VALIDEE';
    case TRAITEE = 'TRAITEE';
    case REJETEE = 'REJETEE';

    /**
     * Retourne un libellé lisible pour l'affichage.
     */
    public function label(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'En attente',
            self::VALIDEE => 'Validée',
            self::TRAITEE => 'Traitée',
            self::REJETEE => 'Rejetée',
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
