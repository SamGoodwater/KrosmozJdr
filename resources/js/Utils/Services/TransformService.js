/**
 * Service de transformations communes
 * 
 * @description
 * Service centralisé pour toutes les transformations de données réutilisables :
 * - Génération de slugs
 * - Normalisation de texte
 * - Conversions de types
 * - Formatage de données
 * 
 * @example
 * import { TransformService } from '@/Utils/Services/TransformService';
 * const slug = TransformService.generateSlug('Mon Titre', 123);
 */
export class TransformService {
    /**
     * Génère un slug depuis un titre
     * 
     * @param {String} title - Titre à transformer en slug
     * @param {Object} options - Options de génération
     * @param {String} options.separator - Séparateur (défaut: '-')
     * @param {Boolean} options.lowercase - Mettre en minuscules (défaut: true)
     * @param {Number} options.maxLength - Longueur maximale (défaut: null)
     * @returns {String} Slug généré
     * 
     * @example
     * TransformService.generateSlugFromTitle('Mon Super Titre');
     * // => 'mon-super-titre'
     */
    static generateSlugFromTitle(title, options = {}) {
        if (!title || typeof title !== 'string') {
            return '';
        }

        const {
            separator = '-',
            lowercase = true,
            maxLength = null
        } = options;

        let slug = title;

        // Normaliser les caractères Unicode (enlever les accents)
        slug = slug.normalize('NFD').replace(/[\u0300-\u036f]/g, '');

        // Mettre en minuscules si demandé
        if (lowercase) {
            slug = slug.toLowerCase();
        }

        // Remplacer les caractères non alphanumériques par le séparateur
        slug = slug.replace(/[^a-z0-9]+/gi, separator);

        // Enlever les séparateurs en début et fin
        slug = slug.replace(new RegExp(`^${separator}+|${separator}+$`, 'g'), '');

        // Limiter la longueur si demandé
        if (maxLength && slug.length > maxLength) {
            slug = slug.substring(0, maxLength);
            // Enlever le séparateur final si présent
            slug = slug.replace(new RegExp(`${separator}+$`), '');
        }

        return slug;
    }

    /**
     * Génère un slug depuis un titre ou un ID (avec élément aléatoire optionnel)
     * 
     * @param {String|null} title - Titre à transformer (optionnel)
     * @param {Number|String|null} id - ID à utiliser si pas de titre
     * @param {Object} options - Options de génération
     * @param {String} options.prefix - Préfixe pour les slugs générés depuis l'ID (défaut: 'item')
     * @param {Boolean} options.addRandom - Ajouter un élément aléatoire si généré depuis l'ID (défaut: true)
     * @param {Number} options.randomLength - Longueur de l'élément aléatoire (défaut: 6)
     * @returns {String} Slug généré
     * 
     * @example
     * TransformService.generateSlug('Mon Titre', 123);
     * // => 'mon-titre'
     * 
     * TransformService.generateSlug(null, 123);
     * // => 'item-123-a1b2c3'
     */
    static generateSlug(title, id = null, options = {}) {
        const {
            prefix = 'item',
            addRandom = true,
            randomLength = 6
        } = options;

        // Si on a un titre, l'utiliser
        if (title) {
            return this.generateSlugFromTitle(title);
        }

        // Sinon, générer depuis l'ID
        if (id) {
            let slug = `${prefix}-${id}`;
            
            // Ajouter un élément aléatoire pour éviter les collisions
            if (addRandom) {
                const random = this.generateRandomString(randomLength);
                slug = `${slug}-${random}`;
            }
            
            return slug;
        }

        return '';
    }

    /**
     * Génère une chaîne aléatoire
     * 
     * @param {Number} length - Longueur de la chaîne
     * @param {String} charset - Caractères autorisés (défaut: alphanumériques minuscules)
     * @returns {String} Chaîne aléatoire
     * 
     * @example
     * TransformService.generateRandomString(6);
     * // => 'a1b2c3'
     */
    static generateRandomString(length = 6, charset = 'abcdefghijklmnopqrstuvwxyz0123456789') {
        let result = '';
        for (let i = 0; i < length; i++) {
            result += charset.charAt(Math.floor(Math.random() * charset.length));
        }
        return result;
    }

    /**
     * Normalise un texte (enlève les accents, met en minuscules, etc.)
     * 
     * @param {String} text - Texte à normaliser
     * @param {Object} options - Options de normalisation
     * @param {Boolean} options.lowercase - Mettre en minuscules (défaut: true)
     * @param {Boolean} options.removeAccents - Enlever les accents (défaut: true)
     * @param {Boolean} options.trim - Enlever les espaces en début/fin (défaut: true)
     * @returns {String} Texte normalisé
     * 
     * @example
     * TransformService.normalizeText('  Mon Super Titre  ');
     * // => 'mon super titre'
     */
    static normalizeText(text, options = {}) {
        if (!text || typeof text !== 'string') {
            return '';
        }

        const {
            lowercase = true,
            removeAccents = true,
            trim = true
        } = options;

        let normalized = text;

        // Enlever les accents
        if (removeAccents) {
            normalized = normalized.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        }

        // Mettre en minuscules
        if (lowercase) {
            normalized = normalized.toLowerCase();
        }

        // Enlever les espaces en début/fin
        if (trim) {
            normalized = normalized.trim();
        }

        return normalized;
    }

    /**
     * Convertit une valeur en enum/type si nécessaire
     * 
     * @param {*} value - Valeur à convertir
     * @param {Object|Function} enumClass - Classe enum ou objet de mapping
     * @param {*} defaultValue - Valeur par défaut si la conversion échoue
     * @returns {*} Valeur convertie ou valeur par défaut
     * 
     * @example
     * TransformService.toEnum('draft', PageState, 'draft');
     */
    static toEnum(value, enumClass, defaultValue = null) {
        if (value === null || value === undefined) {
            return defaultValue;
        }

        // Si c'est déjà une instance de l'enum, la retourner
        if (enumClass && typeof enumClass === 'function' && value instanceof enumClass) {
            return value;
        }

        // Si l'enum a une méthode tryFrom (comme les enums PHP)
        if (enumClass && typeof enumClass.tryFrom === 'function') {
            const result = enumClass.tryFrom(value);
            return result !== null ? result : defaultValue;
        }

        // Si c'est un objet de mapping
        if (enumClass && typeof enumClass === 'object') {
            return enumClass[value] !== undefined ? enumClass[value] : defaultValue;
        }

        return value || defaultValue;
    }

    /**
     * Extrait la valeur d'un enum (pour l'envoyer au backend)
     * 
     * @param {*} enumValue - Valeur enum à extraire
     * @returns {*} Valeur primitive (string, number, etc.)
     * 
     * @example
     * TransformService.fromEnum(PageState.DRAFT);
     * // => 'draft'
     */
    static fromEnum(enumValue) {
        if (enumValue === null || enumValue === undefined) {
            return null;
        }

        // Si c'est un objet avec une propriété 'value'
        if (enumValue && typeof enumValue === 'object' && 'value' in enumValue) {
            return enumValue.value;
        }

        // Si c'est une chaîne ou un nombre, la retourner telle quelle
        if (typeof enumValue === 'string' || typeof enumValue === 'number') {
            return enumValue;
        }

        // Sinon, convertir en string
        return String(enumValue);
    }

    /**
     * Normalise les relations pivots (many-to-many)
     * 
     * @param {Array|Object} pivotData - Données de pivot (peut être un tableau d'IDs ou un objet avec relations)
     * @param {Object} options - Options de normalisation
     * @param {String} options.idKey - Clé pour l'ID dans les objets pivot (défaut: 'id')
     * @param {Boolean} options.extractIds - Extraire uniquement les IDs (défaut: false)
     * @returns {Array} Tableau normalisé d'IDs ou d'objets pivot
     * 
     * @example
     * // Extraire les IDs
     * TransformService.normalizePivot([{id: 1, pivot: {role: 'admin'}}, {id: 2}], {extractIds: true});
     * // => [1, 2]
     * 
     * // Garder les données pivot
     * TransformService.normalizePivot([{id: 1, pivot: {role: 'admin'}}]);
     * // => [{id: 1, role: 'admin'}]
     */
    static normalizePivot(pivotData, options = {}) {
        if (!pivotData) {
            return [];
        }

        const {
            idKey = 'id',
            extractIds = false
        } = options;

        // Si c'est déjà un tableau d'IDs simples
        if (Array.isArray(pivotData) && pivotData.every(item => typeof item === 'number' || typeof item === 'string')) {
            return pivotData;
        }

        // Si c'est un tableau d'objets
        if (Array.isArray(pivotData)) {
            if (extractIds) {
                return pivotData.map(item => {
                    if (typeof item === 'object' && item !== null) {
                        return item[idKey] || item.id || item;
                    }
                    return item;
                }).filter(id => id !== null && id !== undefined);
            }

            // Extraire les données pivot
            return pivotData.map(item => {
                if (typeof item === 'object' && item !== null) {
                    const id = item[idKey] || item.id;
                    const pivot = item.pivot || item;
                    
                    return {
                        [idKey]: id,
                        ...pivot
                    };
                }
                return item;
            });
        }

        // Si c'est un objet unique
        if (typeof pivotData === 'object' && pivotData !== null) {
            if (extractIds) {
                return [pivotData[idKey] || pivotData.id];
            }
            return [pivotData];
        }

        return [];
    }

    /**
     * Formate une date pour l'affichage
     * 
     * @param {String|Date} date - Date à formater
     * @param {String} format - Format de sortie (défaut: 'DD/MM/YYYY')
     * @returns {String} Date formatée
     */
    static formatDate(date, format = 'DD/MM/YYYY') {
        if (!date) {
            return '';
        }

        const d = new Date(date);
        if (isNaN(d.getTime())) {
            return '';
        }

        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();

        return format
            .replace('DD', day)
            .replace('MM', month)
            .replace('YYYY', year);
    }
}

export default TransformService;

