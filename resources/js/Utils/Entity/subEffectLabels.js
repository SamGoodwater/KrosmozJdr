/**
 * Libellés français des actions d’effet (select d’édition).
 *
 * @example
 * getSubEffectActionLabel('frapper') // → 'Frapper'
 */

/** @type {Readonly<Record<string, string>>} */
export const SUB_EFFECT_ACTION_LABELS = Object.freeze({
    frapper: 'Frapper',
    soigner: 'Soigner',
    protéger: 'Protéger',
    booster: 'Booster',
    retirer: 'Retirer',
    'voler-caracteristiques': 'Voler des caractéristiques',
    invoquer: 'Invoquer',
    déplacer: 'Déplacer',
    'appliquer-etat': 'Appliquer un état',
    's-appliquer-etat': 'Appliquer un état (soi)',
    autre: 'Autre',
    'donner-pv-temporaires': 'Donner des PV temporaires',
});

/**
 * @param {string|null|undefined} slug
 * @returns {string}
 */
export function getSubEffectActionLabel(slug) {
    const key = String(slug || '').trim();
    if (!key) return '';
    return SUB_EFFECT_ACTION_LABELS[key] || key;
}

/**
 * @param {{ id: number|string, slug?: string }} sub
 * @returns {string}
 */
export function formatSubEffectSelectLabel(sub) {
    const slug = sub?.slug != null ? String(sub.slug) : '';
    const label = getSubEffectActionLabel(slug);
    return label || `Effet #${sub?.id ?? '?'}`;
}
