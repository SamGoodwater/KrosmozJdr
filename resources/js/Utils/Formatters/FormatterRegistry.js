/**
 * FormatterRegistry — Registre centralisé des formatters
 *
 * @description
 * Registre qui permet d'enregistrer et de récupérer les formatters par clé de champ.
 * Permet la résolution automatique du formatter approprié pour un champ donné.
 */

/**
 * Registre des formatters (clé de champ → Formatter class)
 * @type {Map<string, typeof BaseFormatter>}
 */
const formatterRegistry = new Map();

/**
 * Enregistre un formatter dans le registre
 *
 * @param {typeof BaseFormatter} FormatterClass - Classe du formatter (doit hériter de BaseFormatter)
 * @throws {Error} Si le formatter n'a pas de name ou de fieldKeys
 */
export function registerFormatter(FormatterClass) {
  if (!FormatterClass.name) {
    throw new Error(`Formatter must have a static 'name' property: ${FormatterClass.name || 'Unknown'}`);
  }

  if (!Array.isArray(FormatterClass.fieldKeys) || FormatterClass.fieldKeys.length === 0) {
    throw new Error(`Formatter must have a static 'fieldKeys' array: ${FormatterClass.name}`);
  }

  // Enregistrer le formatter pour chaque clé de champ
  FormatterClass.fieldKeys.forEach((fieldKey) => {
    if (formatterRegistry.has(fieldKey)) {
      console.warn(
        `FormatterRegistry: Field key '${fieldKey}' is already registered by '${formatterRegistry.get(fieldKey).name}'. Overriding with '${FormatterClass.name}'.`
      );
    }
    formatterRegistry.set(fieldKey, FormatterClass);
  });
}

/**
 * Récupère le formatter pour une clé de champ donnée
 *
 * @param {string} fieldKey - Clé du champ (ex: 'rarity', 'level', 'visibility')
 * @returns {typeof BaseFormatter|null} Classe du formatter ou null si non trouvé
 */
export function getFormatter(fieldKey) {
  if (!fieldKey || typeof fieldKey !== 'string') {
    return null;
  }
  return formatterRegistry.get(fieldKey) || null;
}

/**
 * Vérifie si un formatter existe pour une clé de champ donnée
 *
 * @param {string} fieldKey - Clé du champ
 * @returns {boolean} true si un formatter existe
 */
export function hasFormatter(fieldKey) {
  return formatterRegistry.has(fieldKey);
}

/**
 * Récupère tous les formatters enregistrés
 *
 * @returns {Map<string, typeof BaseFormatter>} Map de tous les formatters
 */
export function getAllFormatters() {
  return new Map(formatterRegistry);
}

/**
 * Réinitialise le registre (utile pour les tests)
 */
export function clearRegistry() {
  formatterRegistry.clear();
}
