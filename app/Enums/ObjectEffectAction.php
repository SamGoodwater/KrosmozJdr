<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Action d’un effet d’objet (hors modèle « sort ») : cible caractéristique et/ou monstre selon le cas.
 *
 * @example Régénérer des PV : action regenerate + caractéristique PV + valeur.
 * @example Invoquer : action invoke + monstre, sans valeur.
 * @example Téléporter : action teleport, sans caractéristique ni monstre.
 */
enum ObjectEffectAction: string
{
    /** Régénérer (ex. PV, ressource). */
    case Regenerate = 'regenerate';
    /** Ajouter une valeur à une caractéristique (ou cible liée au monstre selon usage). */
    case Add = 'add';
    /** Retirer une valeur. */
    case Remove = 'remove';
    /** Déplacer la cible (pas de caractéristique ni monstre en cible de l’effet). */
    case Teleport = 'teleport';
    /** Invoquer un monstre (pas de valeur). */
    case Invoke = 'invoke';

    public function label(): string
    {
        return match ($this) {
            self::Regenerate => 'Régénérer',
            self::Add => 'Ajouter',
            self::Remove => 'Retirer',
            self::Teleport => 'Téléporter',
            self::Invoke => 'Invoquer',
        };
    }
}
