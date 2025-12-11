/**
 * Classe de base pour tous les modèles frontend
 * 
 * @description
 * Classe abstraite qui fournit les fonctionnalités communes à tous les modèles :
 * - Extraction normalisée des données (gère les Proxies Vue/Inertia)
 * - Propriétés communes (id, created_by, created_at, updated_at)
 * - Permissions communes (can.update, can.delete, etc.)
 * - Méthodes utilitaires communes
 * 
 * @abstract
 */
export class BaseModel {
    /**
     * @param {Object} rawData - Données brutes (peut être un Proxy, un objet avec .data, etc.)
     */
    constructor(rawData) {
        // Normaliser l'extraction des données
        this._raw = rawData;
        this._data = this._extractData(rawData);
    }

    /**
     * Extrait les données normalisées depuis différentes structures possibles
     * Gère les Proxies Vue/Inertia en désenveloppant si nécessaire
     * @protected
     * @param {*} raw - Données brutes (peut être un Proxy Vue/Inertia)
     * @returns {Object} Données normalisées
     */
    _extractData(raw) {
        if (!raw) return {};
        
        // Si c'est déjà une instance du même modèle, retourner ses données
        if (raw instanceof this.constructor) {
            return raw._data;
        }
        
        // Désenvelopper les Proxies Vue/Inertia si nécessaire
        // Les Proxies peuvent masquer certaines propriétés comme 'can' au niveau racine
        let unwrapped = raw;
        try {
            // Vérifier si c'est un Proxy en essayant d'accéder à une propriété clé
            // Si 'can' existe au niveau racine, on doit préserver cette structure
            const hasCanAtRoot = 'can' in raw || raw.can !== undefined;
            const hasDataProperty = raw.data && typeof raw.data === 'object';
            
            // Si 'can' est au niveau racine ET qu'il y a une propriété .data,
            // on doit fusionner les deux pour ne pas perdre 'can'
            if (hasCanAtRoot && hasDataProperty) {
                // Fusionner : données de .data + propriétés au niveau racine (comme 'can')
                return {
                    ...raw.data,
                    can: raw.can, // Préserver 'can' au niveau racine
                };
            }
            
            // Si les données sont dans .data (structure Inertia Resource)
            if (hasDataProperty && !hasCanAtRoot) {
                return raw.data;
            }
        } catch (e) {
            // En cas d'erreur avec les Proxies, continuer avec raw
        }
        
        // Sinon, utiliser raw directement
        return unwrapped;
    }

    // ============================================
    // PROPRIÉTÉS COMMUNES
    // ============================================

    get id() {
        return this._data.id;
    }

    get createdById() {
        return this._data.created_by;
    }

    get createdAt() {
        return this._data.created_at;
    }

    get updatedAt() {
        return this._data.updated_at;
    }

    get deletedAt() {
        return this._data.deleted_at;
    }

    get isVisible() {
        return this._data.is_visible;
    }

    // ============================================
    // RELATIONS COMMUNES
    // ============================================

    /**
     * Retourne l'utilisateur créateur (relation)
     * Peut être surchargé dans les classes filles
     */
    get createdBy() {
        return this._data.createdBy || null;
    }

    /**
     * Alias pour createdBy (pour compatibilité)
     */
    get createdByUser() {
        return this.createdBy;
    }

    // ============================================
    // PERMISSIONS COMMUNES
    // ============================================

    get can() {
        return this._data.can || {
            update: false,
            delete: false,
            forceDelete: false,
            restore: false,
            view: false
        };
    }

    get canUpdate() {
        return this.can.update || false;
    }

    get canDelete() {
        return this.can.delete || false;
    }

    get canForceDelete() {
        return this.can.forceDelete || false;
    }

    get canRestore() {
        return this.can.restore || false;
    }

    get canView() {
        return this.can.view || false;
    }

    // ============================================
    // MÉTHODES UTILITAIRES COMMUNES
    // ============================================

    /**
     * Retourne les données brutes (pour compatibilité)
     * @returns {Object}
     */
    toRaw() {
        return this._data;
    }

    /**
     * Retourne les données pour un formulaire (à surcharger dans les classes filles)
     * @returns {Object}
     */
    toFormData() {
        return {
            ...this._data
        };
    }

    /**
     * Vérifie si l'entité est supprimée (soft delete)
     * @returns {boolean}
     */
    get isDeleted() {
        return !!this.deletedAt;
    }

    /**
     * Crée une instance depuis des données brutes (méthode statique)
     * @param {*} rawData - Données brutes
     * @returns {BaseModel}
     */
    static from(rawData) {
        return new this(rawData);
    }

    /**
     * Crée un tableau d'instances depuis un tableau de données brutes
     * @param {Array} rawDataArray - Tableau de données brutes
     * @returns {Array<BaseModel>}
     */
    static fromArray(rawDataArray) {
        if (!Array.isArray(rawDataArray)) return [];
        return rawDataArray.map(data => new this(data));
    }
}

export default BaseModel;

