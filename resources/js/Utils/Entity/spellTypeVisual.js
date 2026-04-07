/**
 * Thème visuel des types de sort (icônes + couleurs).
 * Icônes PNG : `storage/app/public/images/icons/breed_orientations/{clé}.png`
 * (orientations de classe : dégâts, soin, tank, etc.)
 *
 * @see docs (types de sorts Krosmoz)
 */

/** Base publique (fichiers servis via `/storage/...`) */
export const SPELL_TYPE_ICON_BASE = '/storage/images/icons/breed_orientations';

/** Fichiers présents dans `breed_orientations` (sans extension). */
export const BREED_ORIENTATION_ICON_KEYS = Object.freeze([
    'amelioration',
    'degats',
    'entrave',
    'invocation',
    'placement',
    'protection',
    'soin',
    'tank',
]);

const ORIENTATION_KEY_SET = new Set(BREED_ORIENTATION_ICON_KEYS);

/**
 * Couleurs par clé d’orientation (fichier PNG).
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

/**
 * Libellés / synonymes (normalisés) → clé fichier orientation.
 * Couvre les noms du {@link Database\Seeders\Type\SpellTypeSeeder} et les orientations métier.
 */
const NORMALIZED_LABEL_TO_ORIENTATION = Object.freeze({
    // Orientations (noms directs)
    amelioration: 'amelioration',
    amlioration: 'amelioration',
    degats: 'degats',
    dommages: 'degats',
    dommage: 'degats',
    entrave: 'entrave',
    invocation: 'invocation',
    placement: 'placement',
    protection: 'protection',
    soin: 'soin',
    soins: 'soin',
    tank: 'tank',
    // Types seedés (fr)
    offensif: 'degats',
    offensifs: 'degats',
    defensif: 'protection',
    buff: 'amelioration',
    buffs: 'amelioration',
    debuff: 'entrave',
    debuffs: 'entrave',
    teleportation: 'placement',
    transformation: 'tank',
    // Anglais courants
    offensive: 'degats',
    defensive: 'protection',
    healing: 'soin',
    heal: 'soin',
    damage: 'degats',
    damages: 'degats',
    support: 'amelioration',
    control: 'entrave',
    utility: 'placement',
    summon: 'invocation',
    summoning: 'invocation',
});

/**
 * Anciens slugs `spell_types.icon` (FontAwesome / mot-clé) → orientation.
 */
const LEGACY_DB_ICON_TO_ORIENTATION = Object.freeze({
    sword: 'degats',
    shield: 'protection',
    heart: 'soin',
    arrowup: 'amelioration',
    arrowdown: 'entrave',
    magic: 'invocation',
    locationarrow: 'placement',
    exchangealt: 'entrave',
});

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
 * Résout la clé orientation (nom de fichier sans `.png`).
 *
 * @param {string} nameNorm
 * @param {string} iconNorm - `icon` BDD normalisé (peut être vide)
 * @returns {string|null}
 */
function resolveOrientationFileKey(nameNorm, iconNorm) {
    if (iconNorm) {
        if (ORIENTATION_KEY_SET.has(iconNorm)) {
            return iconNorm;
        }
        const fromLegacy = LEGACY_DB_ICON_TO_ORIENTATION[iconNorm];
        if (fromLegacy) {
            return fromLegacy;
        }
    }
    if (nameNorm) {
        const fromLabel = NORMALIZED_LABEL_TO_ORIENTATION[nameNorm];
        if (fromLabel) {
            return fromLabel;
        }
        if (ORIENTATION_KEY_SET.has(nameNorm)) {
            return nameNorm;
        }
    }
    return null;
}

/**
 * Résout couleur d’affichage + URL PNG orientation pour un type de sort.
 *
 * @param {string} name - Libellé affiché (ex. « Offensif », « Dégâts »)
 * @param {string|null|undefined} [dbColor] - Couleur BDD (#rrggbb)
 * @param {string|null|undefined} [dbIconHint] - Colonne `spell_types.icon` (slug orientation ou ancien mot-clé)
 * @returns {{ hex: string, iconUrl: string|null, orientationKey: string|null }}
 */
export function resolveSpellTypeVisual(name, dbColor = null, dbIconHint = null) {
    const nameNorm = normalizeSpellTypeKey(name);
    const iconNorm = dbIconHint != null && String(dbIconHint).trim() !== ''
        ? normalizeSpellTypeKey(String(dbIconHint))
        : '';
    const orientationKey = resolveOrientationFileKey(nameNorm, iconNorm);
    const themeHex = orientationKey && SPELL_TYPE_THEME_HEX[orientationKey] ? SPELL_TYPE_THEME_HEX[orientationKey] : null;
    const hex = themeHex ? `#${themeHex}` : normalizeDbColor(dbColor) || '#737373';
    const iconUrl = orientationKey ? `${SPELL_TYPE_ICON_BASE}/${orientationKey}.png` : null;

    return { hex, iconUrl, orientationKey };
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
