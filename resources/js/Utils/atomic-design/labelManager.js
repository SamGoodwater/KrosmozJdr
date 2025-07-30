/**
 * labelManager.js — Gestionnaire de labels pour les composants d'input
 *
 * @description
 * Module spécialisé pour la gestion des labels dans les composants d'input.
 * Fournit des helpers pour valider, normaliser et traiter les configurations
 * de labels avec leurs positions multiples.
 *
 * @example
 * import { validateLabel, processLabelConfig } from '@/Utils/atomic-design/labelManager';
 * 
 * // Valider une configuration
 * const isValid = validateLabel({ top: 'Nom', inStart: 'M.' });
 * 
 * // Traiter une configuration
 * const config = processLabelConfig('Nom'); // { floating: 'Nom' }
 */

/**
 * Positions de labels valides pour les composants d'input
 */
export const validLabelPositions = ['top', 'bottom', 'start', 'end', 'inStart', 'inEnd', 'floating'];

/**
 * Positions de labels externes (Field)
 */
export const externalLabelPositions = ['top', 'bottom', 'start', 'end'];

/**
 * Positions de labels internes (Core)
 */
export const internalLabelPositions = ['inStart', 'inEnd', 'floating'];

/**
 * Règles d'exclusion entre positions de labels
 */
export const labelExclusionRules = {
    // Floating exclut tous les autres labels internes et externes
    floating: [...internalLabelPositions, ...externalLabelPositions].filter(pos => pos !== 'floating'),
    
    // inStart/inEnd excluent start/end
    inStart: ['start', 'end'],
    inEnd: ['start', 'end'],
    
    // start/end excluent inStart/inEnd
    start: ['inStart', 'inEnd'],
    end: ['inStart', 'inEnd'],
};

/**
 * Valide une configuration de label
 * @param {String|Object} label - Label à valider
 * @returns {Boolean} - True si valide
 */
export function validateLabel(label) {
    // String est toujours valide
    if (typeof label === 'string') {
        return true;
    }
    
    // Objet doit avoir des clés valides
    if (typeof label === 'object' && label !== null) {
        return Object.keys(label).every(key => validLabelPositions.includes(key));
    }
    
    return false;
}

/**
 * Normalise une configuration de label
 * @param {String|Object} label - Label à normaliser
 * @param {String} defaultPosition - Position par défaut pour les strings ('floating', 'top', etc.)
 * @returns {Object} - Configuration normalisée
 */
export function normalizeLabel(label, defaultPosition = 'top') {
    if (typeof label === 'string') {
        return { [defaultPosition]: label };
    }
    
    if (typeof label === 'object' && label !== null) {
        return { ...label };
    }
    
    return {};
}

/**
 * Applique les règles d'exclusion entre positions de labels
 * @param {Object} config - Configuration de labels
 * @returns {Object} - Configuration nettoyée
 */
export function applyExclusionRules(config) {
    const cleaned = { ...config };
    
    // Appliquer les règles d'exclusion
    Object.entries(labelExclusionRules).forEach(([position, excludedPositions]) => {
        if (cleaned[position]) {
            excludedPositions.forEach(excludedPos => {
                delete cleaned[excludedPos];
            });
        }
    });
    
    return cleaned;
}

/**
 * Valide et nettoie une configuration de label (supprime les combinaisons interdites)
 * @param {String|Object} label - Label à traiter
 * @param {String} defaultPosition - Position par défaut pour les strings
 * @returns {Object} - Configuration validée et nettoyée
 */
export function processLabelConfig(label, defaultPosition = 'top') {
    const config = normalizeLabel(label, defaultPosition);
    return applyExclusionRules(config);
}

/**
 * Vérifie si une position de label est valide
 * @param {String} position - Position à vérifier
 * @returns {Boolean} - True si valide
 */
export function isValidLabelPosition(position) {
    return validLabelPositions.includes(position);
}

/**
 * Vérifie si une position de label est externe (Field)
 * @param {String} position - Position à vérifier
 * @returns {Boolean} - True si externe
 */
export function isExternalLabelPosition(position) {
    return externalLabelPositions.includes(position);
}

/**
 * Vérifie si une position de label est interne (Core)
 * @param {String} position - Position à vérifier
 * @returns {Boolean} - True si interne
 */
export function isInternalLabelPosition(position) {
    return internalLabelPositions.includes(position);
}

/**
 * Extrait les positions utilisées dans une configuration
 * @param {String|Object} label - Configuration de label
 * @returns {Array} - Liste des positions utilisées
 */
export function getUsedLabelPositions(label) {
    const config = normalizeLabel(label);
    return Object.keys(config);
}

/**
 * Extrait les positions externes utilisées dans une configuration
 * @param {String|Object} label - Configuration de label
 * @returns {Array} - Liste des positions externes utilisées
 */
export function getUsedExternalLabelPositions(label) {
    const positions = getUsedLabelPositions(label);
    return positions.filter(pos => isExternalLabelPosition(pos));
}

/**
 * Extrait les positions internes utilisées dans une configuration
 * @param {String|Object} label - Configuration de label
 * @returns {Array} - Liste des positions internes utilisées
 */
export function getUsedInternalLabelPositions(label) {
    const positions = getUsedLabelPositions(label);
    return positions.filter(pos => isInternalLabelPosition(pos));
}

/**
 * Vérifie si une configuration contient des positions interdites
 * @param {String|Object} label - Configuration de label
 * @returns {Boolean} - True si contient des positions interdites
 */
export function hasConflictingLabelPositions(label) {
    const config = normalizeLabel(label);
    
    // Vérifier les règles d'exclusion
    for (const [position, excludedPositions] of Object.entries(labelExclusionRules)) {
        if (config[position]) {
            for (const excludedPos of excludedPositions) {
                if (config[excludedPos]) {
                    return true;
                }
            }
        }
    }
    
    return false;
}

/**
 * Vérifie si une configuration a un label flottant
 * @param {String|Object} label - Configuration de label
 * @returns {Boolean} - True si a un label flottant
 */
export function hasFloatingLabel(label) {
    const config = normalizeLabel(label);
    return !!config.floating;
}

/**
 * Vérifie si une configuration a des labels inline
 * @param {String|Object} label - Configuration de label
 * @returns {Boolean} - True si a des labels inline
 */
export function hasInlineLabels(label) {
    const config = normalizeLabel(label);
    return !!(config.inStart || config.inEnd);
}

/**
 * Extrait les labels pour un composant Core
 * @param {String|Object} label - Configuration de label
 * @returns {Object} - Labels pour le Core
 */
export function extractCoreLabels(label) {
    const config = processLabelConfig(label);
    return {
        labelFloating: !!config.floating,
        labelStart: config.inStart || '',
        labelEnd: config.inEnd || '',
    };
}

/**
 * Extrait les labels pour un composant Field
 * @param {String|Object} label - Configuration de label
 * @returns {Object} - Labels pour le Field
 */
export function extractFieldLabels(label) {
    const config = processLabelConfig(label);
    return {
        top: config.top || '',
        bottom: config.bottom || '',
        start: config.start || '',
        end: config.end || '',
        inStart: config.inStart || '',
        inEnd: config.inEnd || '',
        floating: config.floating || '',
    };
}

/**
 * Crée une configuration de label simple
 * @param {String} text - Texte du label
 * @param {String} position - Position du label
 * @returns {Object} - Configuration de label
 */
export function createLabelConfig(text, position = 'top') {
    if (!isValidLabelPosition(position)) {
        console.warn(`Position de label invalide: ${position}. Utilisation de 'top' par défaut.`);
        position = 'top';
    }
    
    return { [position]: text };
}

/**
 * Fusionne deux configurations de labels
 * @param {Object} config1 - Première configuration
 * @param {Object} config2 - Deuxième configuration
 * @returns {Object} - Configuration fusionnée
 */
export function mergeLabelConfigs(config1, config2) {
    const merged = { ...config1, ...config2 };
    return applyExclusionRules(merged);
} 