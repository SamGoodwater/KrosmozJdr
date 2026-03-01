/**
 * Utilitaire pour parser une chaîne level (1d4, [5-8], 12) en options pour sélecteur.
 *
 * @description
 * Utilisé par CharacteristicsCard quand le level est variable.
 * Retourne { options: number[], defaultLevel: number|null }.
 *
 * @example
 * useCharacteristicLevel('5')       → { options: [5], defaultLevel: 5 }
 * useCharacteristicLevel('1d4')     → { options: [1,2,3,4], defaultLevel: 1 }
 * useCharacteristicLevel('[5-8]')   → { options: [5,6,7,8], defaultLevel: 5 }
 */

/**
 * @param {string|null|undefined} levelStr
 * @returns {{ options: number[], defaultLevel: number|null }}
 */
export function useCharacteristicLevel(levelStr) {
    if (levelStr === null || levelStr === undefined || String(levelStr).trim() === '') {
        return { options: [], defaultLevel: null };
    }
    const s = String(levelStr).trim();

    // Valeur fixe numérique
    const fixed = Number.parseInt(s, 10);
    if (Number.isFinite(fixed) && String(fixed) === s) {
        return { options: [fixed], defaultLevel: fixed };
    }

    // Plage [min-max]
    const rangeMatch = s.match(/^\[?\s*(\d+)\s*[-–]\s*(\d+)\s*\]?$/);
    if (rangeMatch) {
        const min = Math.max(0, parseInt(rangeMatch[1], 10));
        const max = Math.max(min, parseInt(rangeMatch[2], 10));
        const options = [];
        for (let i = min; i <= max; i++) options.push(i);
        return { options, defaultLevel: options.length ? options[0] : null };
    }

    // Dés 1dN
    const diceMatch = s.match(/^(\d*)d(\d+)$/i);
    if (diceMatch) {
        const count = diceMatch[1] ? parseInt(diceMatch[1], 10) : 1;
        const faces = parseInt(diceMatch[2], 10);
        if (count === 1 && faces >= 1 && faces <= 100) {
            const options = [];
            for (let i = 1; i <= faces; i++) options.push(i);
            return { options, defaultLevel: options[0] };
        }
    }

    return { options: [], defaultLevel: null };
}
