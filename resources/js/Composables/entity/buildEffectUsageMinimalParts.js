/**
 * Segmente un chip `effect_usages_chips` pour l’affichage minimal (icônes + texte + zone).
 * Caractéristique : {@link resolveDef} (même chaîne que CharacteristicChip : db_column, clés, computed).
 *
 * @param {object} item - Chip normalisé (Spell._toEffectSummaryCell)
 * @returns {{
 *   elementBlock: { icon: string, label: string, style?: object } | null,
 *   characteristicBlock: { icon: string, name: string, style?: object } | null,
 *   text: string,
 *   summonMonster: { id: number, name: string, image?: string|null } | null,
 *   summonPrefix: string,
 *   area: string|null,
 *   tooltip: string
 * }}
 */
import {
    resolveDef,
    getCharacteristicColorStyle,
} from '@/Composables/entity/useCharacteristicDisplay';
import { getElementLabel, getElementIcon, getElementColor } from '@/Utils/Entity/Elements';

const ELEMENT_SLUGS = new Set(['neutral', 'earth', 'fire', 'air', 'water']);

const SLUG_TO_ELEMENT_ID = Object.freeze({
    neutral: 0,
    earth: 1,
    fire: 2,
    air: 3,
    water: 4,
});

/** Ordre de résolution aligné sur les effets de sort (BDD spell / capacités / créature). */
const EFFECT_CHARACTERISTIC_SOURCE_GROUPS = Object.freeze(['spell', 'capability', 'creature']);

/**
 * Partie du texte résolu située avant le nom du monstre (ex. « Invocation de », « Invocation »).
 *
 * @param {string} textRaw
 * @param {string} monsterName
 * @returns {string}
 */
function summonTextPrefixWithoutMonsterName(textRaw, monsterName) {
    const t = String(textRaw ?? '').trim();
    const nm = String(monsterName ?? '').trim();
    if (!t || !nm) {
        return '';
    }
    const lowerT = t.toLowerCase();
    const lowerN = nm.toLowerCase();
    const idx = lowerT.lastIndexOf(lowerN);
    if (idx < 0) {
        return '';
    }
    return t
        .slice(0, idx)
        .replace(/\s+$/u, '')
        .replace(/[.,;:!?]+$/u, '')
        .trim();
}

/**
 * Préfixe affiché avant la vue texte monstre (espace final pour séparer du lien).
 *
 * @param {string} strippedPrefix
 * @returns {string}
 */
function normalizeSummonActionPrefix(strippedPrefix) {
    const s = String(strippedPrefix ?? '').trim();
    if (s === '') {
        return 'Invocation de ';
    }
    if (/^invocation$/iu.test(s)) {
        return 'Invocation de ';
    }
    return /\s$/u.test(s) ? s : `${s} `;
}

export function buildEffectUsageMinimalParts(item) {
    const i = item && typeof item === 'object' ? item : {};
    const charKeyTrimmed = i.characteristic != null ? String(i.characteristic).trim() : '';
    const charKeyNorm = charKeyTrimmed.toLowerCase();
    const elNum = Number(i.element);
    const hasNumericElement = Number.isFinite(elNum);

    let elementBlock = null;
    if (hasNumericElement && elNum > 0) {
        const hex = getElementColor(elNum);
        elementBlock = {
            icon: getElementIcon(elNum),
            label: getElementLabel(elNum),
            style: hex ? getCharacteristicColorStyle(hex) : undefined,
        };
    } else if (charKeyNorm && ELEMENT_SLUGS.has(charKeyNorm)) {
        const id = SLUG_TO_ELEMENT_ID[charKeyNorm] ?? 0;
        const hex = getElementColor(id);
        elementBlock = {
            icon: getElementIcon(id),
            label: getElementLabel(id),
            style: hex ? getCharacteristicColorStyle(hex) : undefined,
        };
    }

    let characteristicBlock = null;
    if (charKeyTrimmed && !ELEMENT_SLUGS.has(charKeyNorm)) {
        const def = resolveDef(charKeyTrimmed, undefined, {
            sourceGroups: [...EFFECT_CHARACTERISTIC_SOURCE_GROUPS],
        });
        const icon = def?._resolvedIcon ?? def?.icon ?? '';
        const color = def?._resolvedColor ?? def?.color;
        const name = def?.short_name || def?.name;
        if (def && (icon || name)) {
            characteristicBlock = {
                icon,
                name: name || charKeyTrimmed,
                style: color ? getCharacteristicColorStyle(color) : undefined,
            };
        }
    }

    const sm = i.summon_monster;
    const summonMonster =
        sm &&
        typeof sm === 'object' &&
        sm.id != null &&
        Number.isFinite(Number(sm.id))
            ? {
                  id: Number(sm.id),
                  name: sm.name != null ? String(sm.name) : `Monstre #${sm.id}`,
                  image: sm.image ?? null,
              }
            : null;

    const textRaw = i.value != null && String(i.value).trim() !== '' ? String(i.value) : '';
    const summonPrefix = summonMonster
        ? normalizeSummonActionPrefix(summonTextPrefixWithoutMonsterName(textRaw, summonMonster.name))
        : '';
    const text = summonMonster ? '' : textRaw;
    const area = i.area != null && String(i.area).trim() !== '' ? String(i.area).trim() : null;
    const tipRaw = i.tooltip != null ? String(i.tooltip).trim() : '';
    const tooltip = tipRaw !== '' ? tipRaw : '';

    return {
        elementBlock,
        characteristicBlock,
        text,
        summonMonster,
        summonPrefix,
        area,
        tooltip,
    };
}
