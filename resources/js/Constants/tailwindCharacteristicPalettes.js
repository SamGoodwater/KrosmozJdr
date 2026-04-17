/**
 * Noms de palettes Tailwind autorisés pour les couleurs de caractéristiques (aligné sur
 * {@see Database\Seeders\Data\CharacteristicPaletteResolver::ALLOWED_PALETTES}).
 *
 * @type {readonly string[]}
 */
export const TAILWIND_CHARACTERISTIC_PALETTES = Object.freeze([
    'amber',
    'blue',
    'brown',
    'cyan',
    'emerald',
    'fuchsia',
    'gray',
    'green',
    'indigo',
    'lime',
    'neutral',
    'orange',
    'pink',
    'purple',
    'red',
    'rose',
    'sky',
    'slate',
    'stone',
    'teal',
    'violet',
    'yellow',
    'zinc',
]);

/**
 * @param {string} name
 * @returns {string} Libellé pour le select (capitalisé)
 */
export function formatPaletteLabel(name) {
    return name.charAt(0).toUpperCase() + name.slice(1);
}
