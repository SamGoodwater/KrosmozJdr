/**
 * Thème visuel des types de sort (icônes + couleurs).
 * Fichiers : storage/app/public/images/icons/spell_type/*.svg
 *
 * @see docs (types de sorts Krosmoz)
 */

/** Base publique (lien storage) */
export const SPELL_TYPE_ICON_BASE = '/storage/images/icons/spell_type';

/**
 * Couleurs par clé normalisée du nom (sans accent, minuscules).
 * Aligné sur les assets fournis (8 familles).
 */
export const SPELL_TYPE_THEME_HEX = Object.freeze({
    amelioration: '42bffd',
    degats: 'd90410',
    invocation: 'fdd901',
    entrave: 'eeede9',
    placement: 'aad12a',
    protection: '8805fb',
    soin: 'f65db0',
    tank: 'f36702',
});

/** Nom de fichier sans .svg (identique à la clé normalisée pour ces 8 types). */
const THEME_KEYS = new Set(Object.keys(SPELL_TYPE_THEME_HEX));

/**
 * @param {string|null|undefined} name
 * @returns {string}
 */
export function normalizeSpellTypeKey(name) {
    if (name == null || typeof name !== 'string') return '';
    return name
        .normalize('NFD')
        .replace(/\p{M}/gu, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '');
}

/**
 * @param {string|null|undefined} raw
 * @returns {string|null} #rrggbb ou null
 */
function normalizeDbColor(raw) {
    if (raw == null || typeof raw !== 'string') return null;
    const s = raw.trim();
    if (!s) return null;
    if (/^#[0-9a-fA-F]{6}$/.test(s)) return s.toLowerCase();
    if (/^[0-9a-fA-F]{6}$/.test(s)) return `#${s.toLowerCase()}`;
    return null;
}

/**
 * Résout couleur d’affichage + URL d’icône pour un type de sort.
 *
 * @param {string} name - Libellé (ex. « Dégâts », « Amélioration »)
 * @param {string|null|undefined} [dbColor] - Couleur éventuelle en base (#rrggbb)
 * @returns {{ hex: string, iconUrl: string|null }}
 */
export function resolveSpellTypeVisual(name, dbColor = null) {
    const key = normalizeSpellTypeKey(name);
    const themeHex = key && THEME_KEYS.has(key) ? SPELL_TYPE_THEME_HEX[key] : null;
    const hex = themeHex ? `#${themeHex}` : normalizeDbColor(dbColor) || '#737373';
    const iconUrl = key && THEME_KEYS.has(key) ? `${SPELL_TYPE_ICON_BASE}/${key}.svg` : null;

    return { hex, iconUrl };
}

/**
 * Indique si une cellule tableau `spell_types` doit être affichée (compat types `text` / `spell_types`).
 *
 * @param {{ type?: string, value?: unknown, params?: { items?: unknown[] } }}|null|undefined} cell
 * @returns {boolean}
 */
export function spellTypesCellHasRenderableContent(cell) {
    if (!cell || typeof cell !== 'object') return false;
    if (cell.type === 'spell_types') {
        return Array.isArray(cell.params?.items) && cell.params.items.length > 0;
    }
    const v = cell.value;
    return v != null && v !== '' && v !== '-' && v !== '—';
}
