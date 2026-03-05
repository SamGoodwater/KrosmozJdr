/**
 * Aide unifiée pour les formules dans les formulaires d'entités.
 *
 * Source de vérité unique pour éviter la duplication de texte/UX.
 */
export const FORMULA_PLACEHOLDER = 'Ex: 10, [level], [base] + 2';

export const FORMULA_TOOLTIP_TEXT = [
  'Vous pouvez utiliser une formule dans les champs compatibles.',
  'Syntaxe autorisée : nombres, + - * /, parenthèses et variables entre crochets (ex: [level], [base], [bonus]).',
  'Exemples : 12 | [level] + 2 | ([base] * 1.5) + [bonus].',
].join(' ');

