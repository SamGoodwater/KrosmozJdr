/**
 * Helpers purs pour le rendu visuel des lignes SidebarNav (couleurs / ombres).
 *
 * @param {object} item
 * @param {(item: object) => string} getItemCssClasses
 * @returns {{ accent: string, body: string }}
 */
export function splitItemVisualClasses(item, getItemCssClasses) {
    const raw = (getItemCssClasses(item) || '').trim();
    if (!raw) return { accent: '', body: '' };
    const tokens = raw.split(/\s+/).filter(Boolean);
    const body = [];
    const accent = [];
    for (const t of tokens) {
        if (t.startsWith('box-shadow-glass-')) body.push(t);
        else accent.push(t);
    }
    return { accent: accent.join(' '), body: body.join(' ') };
}

/**
 * @param {object} item
 * @param {(item: object) => string|null|undefined} getItemColor
 * @returns {Record<string, string>|{}}
 */
export function accentStripStyleFromItem(item, getItemColor) {
    const hex = getItemColor(item);
    if (hex && typeof hex === 'string') return { '--color': hex };
    return {};
}
