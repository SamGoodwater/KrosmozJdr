/**
 * Utilitaire pour parser une chaîne level en options pour sélecteur.
 *
 * @description
 * Le niveau est la seule caractéristique qui accepte un domaine variable : nombre, fourchette
 * ou dé, éventuellement dans une formule. Cet utilitaire s'appuie sur la grammaire partagée
 * ({@link formulaGrammar}) et reste aligné sur LevelDomainResolver côté serveur.
 *
 * Utilisé par CharacteristicsCard quand le level est variable.
 * Retourne { options: number[], defaultLevel: number|null }.
 *
 * @example
 * useCharacteristicLevel('5')            → { options: [5], defaultLevel: 5 }
 * useCharacteristicLevel('1d4')          → { options: [1,2,3,4], defaultLevel: 1 }
 * useCharacteristicLevel('[5-8]')        → { options: [5,6,7,8], defaultLevel: 5 }
 * useCharacteristicLevel('{8 + [1d4]}')  → { options: [9,10,11,12], defaultLevel: 9 }
 */

import { enumerateFormulaOutcomes } from '@/Utils/characteristic/formulaGrammar';

/** Nombre maximal de niveaux proposés (aligné sur LevelDomainResolver::MAX_LEVELS). */
export const MAX_LEVEL_OPTIONS = 20;

/**
 * Ramène les écritures historiques (`1d4`, `[5-8]`, `5-8`) à la forme canonique `{...}`.
 *
 * @param {string} raw
 * @returns {string}
 */
function normalizeLegacySyntax(raw) {
    if (raw.startsWith('{') || /^-?\d+(\.\d+)?$/.test(raw)) return raw;
    if (/^\[?\s*\d+\s*(?:-|–|\.\.)\s*\d+\s*\]?$/.test(raw)) return `{[${raw.replace(/[[\]]/g, '').trim()}]}`;
    if (/^\d*[dD]\d+$/.test(raw)) return `{${raw}}`;

    return raw;
}

/**
 * @param {string|number|null|undefined} levelStr
 * @param {number} maxOptions
 * @returns {{ options: number[], defaultLevel: number|null }}
 */
export function useCharacteristicLevel(levelStr, maxOptions = MAX_LEVEL_OPTIONS) {
    const raw = String(levelStr ?? '').trim();
    if (raw === '') return { options: [], defaultLevel: null };

    const outcomes = enumerateFormulaOutcomes(normalizeLegacySyntax(raw), {}, maxOptions);
    const options = [...new Set(outcomes.map((value) => Math.round(value)).filter((value) => value >= 0))].sort(
        (a, b) => a - b,
    );

    return { options, defaultLevel: options.length ? options[0] : null };
}
