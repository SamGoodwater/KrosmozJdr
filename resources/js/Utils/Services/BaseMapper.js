/**
 * Classe de base pour tous les mappers
 * 
 * @description
 * Classe abstraite qui fournit les fonctionnalités communes à tous les mappers :
 * - Normalisation des données (gestion des Proxies Vue/Inertia)
 * - Conversion des types/enums
 * - Gestion des relations pivots
 * - Méthodes utilitaires communes
 * 
 * @abstract
 * 
 * @example
 * class MyMapper extends BaseMapper {
 *   static mapToModel(rawData) {
 *     const normalized = this.normalize(rawData);
 *     return new MyModel(normalized);
 *   }
 * }
 */
import { toRaw } from 'vue';
import { TransformService } from './TransformService';

export class BaseMapper {
    /**
     * Normalise les données brutes (désenveloppe les Proxies, extrait .data, etc.)
     * 
     * @param {*} rawData - Données brutes (peut être un Proxy Vue/Inertia)
     * @returns {Object} Données normalisées
     * 
     * @protected
     */
    static normalize(rawData) {
        if (!rawData) {
            return null;
        }

        // Si c'est déjà une instance du modèle, retourner ses données
        if (rawData._data) {
            return rawData._data;
        }

        // Désenvelopper les Proxies Vue/Inertia
        let unwrapped;
        try {
            unwrapped = toRaw(rawData);
        } catch (e) {
            unwrapped = rawData;
        }

        // Extraire les données si elles sont dans .data (structure Inertia Resource)
        if (unwrapped && unwrapped.data && typeof unwrapped.data === 'object') {
            // Fusionner avec les propriétés au niveau racine (comme 'can')
            return {
                ...unwrapped.data,
                can: unwrapped.can || unwrapped.data.can,
            };
        }

        return unwrapped;
    }

    /**
     * Normalise les permissions (can.update, can.delete, etc.)
     * 
     * @param {Object} rawData - Données brutes
     * @returns {Object} Permissions normalisées
     * 
     * @protected
     */
    static normalizePermissions(rawData) {
        const normalized = this.normalize(rawData);
        
        if (!normalized) {
            return {
                update: false,
                delete: false,
                forceDelete: false,
                restore: false,
                view: false
            };
        }

        // Extraire les permissions depuis can (peut être au niveau racine ou dans .data)
        const can = normalized.can || rawData.can;

        if (!can || typeof can !== 'object') {
            return {
                update: false,
                delete: false,
                forceDelete: false,
                restore: false,
                view: false
            };
        }

        // Normaliser les valeurs booléennes
        return {
            update: can.update === true || can.update === 1,
            delete: can.delete === true || can.delete === 1,
            forceDelete: can.forceDelete === true || can.forceDelete === 1,
            restore: can.restore === true || can.restore === 1,
            view: can.view === true || can.view === 1,
        };
    }

    /**
     * Convertit une valeur en enum/type
     * 
     * @param {*} value - Valeur à convertir
     * @param {Object|Function} enumClass - Classe enum ou objet de mapping
     * @param {*} defaultValue - Valeur par défaut
     * @returns {*} Valeur convertie
     * 
     * @protected
     */
    static toEnum(value, enumClass, defaultValue = null) {
        return TransformService.toEnum(value, enumClass, defaultValue);
    }

    /**
     * Extrait la valeur d'un enum pour l'envoyer au backend
     * 
     * @param {*} enumValue - Valeur enum
     * @returns {*} Valeur primitive
     * 
     * @protected
     */
    static fromEnum(enumValue) {
        return TransformService.fromEnum(enumValue);
    }

    /**
     * Normalise les relations pivots (many-to-many)
     * 
     * @param {Array|Object} pivotData - Données de pivot
     * @param {Object} options - Options de normalisation
     * @returns {Array} Tableau normalisé
     * 
     * @protected
     */
    static normalizePivot(pivotData, options = {}) {
        return TransformService.normalizePivot(pivotData, options);
    }

    /**
     * Normalise une relation (one-to-one, one-to-many)
     * 
     * @param {Object|Array} relationData - Données de relation
     * @param {Function} mapperClass - Classe mapper pour mapper la relation
     * @param {Boolean} isArray - Si la relation est un tableau
     * @returns {Object|Array|null} Relation normalisée
     * 
     * @protected
     */
    static normalizeRelation(relationData, mapperClass, isArray = false) {
        if (!relationData) {
            return isArray ? [] : null;
        }

        if (!mapperClass || typeof mapperClass.mapToModel !== 'function') {
            return relationData;
        }

        if (isArray) {
            if (!Array.isArray(relationData)) {
                return [];
            }
            return relationData.map(item => mapperClass.mapToModel(item));
        }

        return mapperClass.mapToModel(relationData);
    }

    /**
     * Extrait une valeur depuis un objet avec fallback
     * 
     * @param {Object} obj - Objet source
     * @param {String|Array} keys - Clé(s) à extraire (peut être un chemin avec points)
     * @param {*} defaultValue - Valeur par défaut
     * @returns {*} Valeur extraite ou valeur par défaut
     * 
     * @protected
     */
    static extractValue(obj, keys, defaultValue = null) {
        if (!obj || typeof obj !== 'object') {
            return defaultValue;
        }

        // Si keys est un tableau, essayer chaque clé jusqu'à trouver une valeur
        if (Array.isArray(keys)) {
            for (const key of keys) {
                const value = this.extractValue(obj, key, undefined);
                if (value !== undefined) {
                    return value;
                }
            }
            return defaultValue;
        }

        // Si keys contient des points, naviguer dans l'objet
        if (typeof keys === 'string' && keys.includes('.')) {
            const parts = keys.split('.');
            let current = obj;
            for (const part of parts) {
                if (current === null || current === undefined || typeof current !== 'object') {
                    return defaultValue;
                }
                current = current[part];
            }
            return current !== undefined ? current : defaultValue;
        }

        // Accès direct
        return obj[keys] !== undefined ? obj[keys] : defaultValue;
    }

    /**
     * Mappe un tableau de données brutes en modèles
     * 
     * @param {Array} rawDataArray - Tableau de données brutes
     * @returns {Array} Tableau de modèles
     * 
     * @static
     */
    static mapToModels(rawDataArray) {
        if (!Array.isArray(rawDataArray)) {
            return [];
        }

        return rawDataArray.map(item => this.mapToModel(item)).filter(Boolean);
    }

    /**
     * Mappe les données brutes en modèle (à implémenter dans les classes filles)
     * 
     * @param {*} rawData - Données brutes
     * @returns {*} Instance du modèle
     * 
     * @abstract
     * @static
     */
    static mapToModel(rawData) {
        throw new Error('mapToModel must be implemented in child class');
    }
}

export default BaseMapper;

