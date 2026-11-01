/**
 * Pastilles de couleur TipTap (texte / surlignage).
 *
 * Hex alignés sur la palette Tailwind « default » en nuance **300** —
 * lisibles sur fond sombre, portables en HTML (PDF, export) sans dépendre
 * des `var(--color-*)` du thème.
 *
 * @typedef {{ id: string, label: string, hex: string }} RichTextColorPreset
 */

/** @type {readonly RichTextColorPreset[]} */
export const RICH_TEXT_COLOR_PRESETS = Object.freeze([
    { id: 'slate-200', label: 'Slate 200', hex: '#e2e8f0' },
    { id: 'slate-300', label: 'Slate 300', hex: '#cbd5e1' },
    { id: 'gray-300', label: 'Gray 300', hex: '#d1d5db' },
    { id: 'zinc-300', label: 'Zinc 300', hex: '#d4d4d8' },
    { id: 'neutral-300', label: 'Neutral 300', hex: '#d4d4d4' },
    { id: 'stone-300', label: 'Stone 300', hex: '#d6d3d1' },
    { id: 'red-300', label: 'Red 300', hex: '#fca5a5' },
    { id: 'orange-300', label: 'Orange 300', hex: '#fdba74' },
    { id: 'amber-300', label: 'Amber 300', hex: '#fcd34d' },
    { id: 'yellow-300', label: 'Yellow 300', hex: '#fde047' },
    { id: 'lime-300', label: 'Lime 300', hex: '#bef264' },
    { id: 'green-300', label: 'Green 300', hex: '#86efac' },
    { id: 'emerald-300', label: 'Emerald 300', hex: '#6ee7b7' },
    { id: 'teal-300', label: 'Teal 300', hex: '#5eead4' },
    { id: 'cyan-300', label: 'Cyan 300', hex: '#67e8f9' },
    { id: 'sky-300', label: 'Sky 300', hex: '#7dd3fc' },
    { id: 'blue-300', label: 'Blue 300', hex: '#93c5fd' },
    { id: 'indigo-300', label: 'Indigo 300', hex: '#a5b4fc' },
    { id: 'violet-300', label: 'Violet 300', hex: '#c4b5fd' },
    { id: 'purple-300', label: 'Purple 300', hex: '#d8b4fe' },
    { id: 'fuchsia-300', label: 'Fuchsia 300', hex: '#f0abfc' },
    { id: 'pink-300', label: 'Pink 300', hex: '#f9a8d4' },
    { id: 'rose-300', label: 'Rose 300', hex: '#fda4af' },
]);

/**
 * @param {string} hex
 * @returns {RichTextColorPreset|undefined}
 */
export function findRichTextColorPreset(hex) {
    if (!hex || typeof hex !== 'string') return undefined;
    const normalized = hex.trim().toLowerCase();
    return RICH_TEXT_COLOR_PRESETS.find((preset) => preset.hex.toLowerCase() === normalized);
}
