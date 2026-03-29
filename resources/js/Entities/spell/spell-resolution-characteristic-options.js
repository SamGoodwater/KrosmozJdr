/**
 * Options des selects « caractéristique d’attaque / sauvegarde » (résolution au combat).
 * Libellés, icônes et couleurs issus du share Inertia `characteristics.spell.byDbColumn` (BDD).
 *
 * @module Entities/spell/spell-resolution-characteristic-options
 */

import { getByDbColumn } from '@/Composables/store/useCharacteristicsStore';
import { isValidColor } from '@/Utils/color/Color';
import { SAVE_ABILITY_OPTIONS } from '@/Entities/spell/spell-descriptors';

/**
 * @param {string} raw
 * @returns {{ iconFa: string|null, iconUrl: string|null }}
 */
function splitIconFromMeta(raw) {
    if (!raw || typeof raw !== 'string' || raw.trim() === '') {
        return { iconFa: null, iconUrl: null };
    }
    const s = raw.trim();
    if (s.startsWith('fa-') || s.includes('fa-solid')) {
        const hasStyle = /\bfa-(solid|regular|brands)\b/u.test(s);
        const iconFa = hasStyle ? s : `fa-solid ${s}`;
        return { iconFa, iconUrl: null };
    }
    const name = s.includes('/') ? s.split('/').pop() : s;
    return {
        iconFa: null,
        iconUrl: `/storage/images/icons/caracteristics/${name}`,
    };
}

/**
 * @returns {Array<{ value: string, label: string, iconFa?: string|null, iconUrl?: string|null, badgeColor?: string|null }>}
 */
export function resolveSpellAttackSaveCharacteristicOptions() {
    return SAVE_ABILITY_OPTIONS().map((o) => {
        if (!o.value) {
            return { ...o };
        }
        const meta = getByDbColumn('spell', String(o.value));
        const { iconFa, iconUrl } = splitIconFromMeta(meta?.icon ?? '');
        const label = meta?.short_name || meta?.name || o.label;
        const c = meta?.color;
        const badgeColor =
            typeof c === 'string' && c.startsWith('#') && isValidColor(c) ? c : null;
        return {
            ...o,
            label,
            iconFa,
            iconUrl,
            badgeColor,
        };
    });
}
