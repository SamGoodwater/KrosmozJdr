/**
 * Modèle Section pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de section côté frontend.
 * Résout les problèmes d'extraction des données depuis les Proxies Vue/Inertia.
 * 
 * @example
 * const section = new Section(props.section);
 * console.log(section.type); // Accès normalisé
 * console.log(section.params); // Paramètres de la section
 * console.log(section.canUpdate); // Méthode utilitaire
 */
import { BaseModel } from './BaseModel';

export class Section extends BaseModel {

    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get id() {
        return this._data.id;
    }

    get pageId() {
        return this._data.page_id;
    }

    get order() {
        return this._data.order || 0;
    }

    get type() {
        return this._data.type;
    }

    get params() {
        return this._data.params || {};
    }

    get isVisible() {
        return this._data.is_visible;
    }

    get state() {
        return this._data.state;
    }

    get createdBy() {
        return this._data.created_by;
    }

    get createdAt() {
        return this._data.created_at;
    }

    get updatedAt() {
        return this._data.updated_at;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get page() {
        if (!this._data.page) return null;
        // Import dynamique pour éviter les dépendances circulaires
        const { Page } = require('./Page');
        return new Page(this._data.page);
    }

    get createdByUser() {
        return this._data.createdBy || null;
    }

    get files() {
        return this._data.files || [];
    }

    // ============================================
    // PERMISSIONS
    // ============================================

    get can() {
        return this._data.can || {
            update: false,
            delete: false,
            forceDelete: false,
            restore: false
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

    // ============================================
    // MÉTHODES UTILITAIRES
    // ============================================

    /**
     * Retourne le contenu de la section selon son type
     * @returns {*}
     */
    get content() {
        switch (this.type) {
            case 'text':
                return this.params.content || '';
            case 'image':
                return this.params.src || '';
            case 'gallery':
                return this.params.images || [];
            case 'video':
                return this.params.src || '';
            default:
                return this.params;
        }
    }

    /**
     * Vérifie si la section est publiée
     * @returns {boolean}
     */
    get isPublished() {
        return this.state === 'published';
    }

    /**
     * Vérifie si la section est un brouillon
     * @returns {boolean}
     */
    get isDraft() {
        return this.state === 'draft';
    }

    /**
     * Retourne les données pour un formulaire
     * @returns {Object}
     */
    toFormData() {
        return {
            page_id: this.pageId,
            order: this.order,
            type: this.type,
            params: this.params,
            is_visible: this.isVisible,
            state: this.state
        };
    }

    /**
     * Retourne les données brutes (pour compatibilité)
     * @returns {Object}
     */
    toRaw() {
        return this._data;
    }

    /**
     * Crée une instance Section depuis des données brutes (méthode statique)
     * @param {*} rawData - Données brutes
     * @returns {Section}
     */
    static from(rawData) {
        return new Section(rawData);
    }

    /**
     * Crée un tableau d'instances Section depuis un tableau de données brutes
     * @param {Array} rawDataArray - Tableau de données brutes
     * @returns {Array<Section>}
     */
    static fromArray(rawDataArray) {
        if (!Array.isArray(rawDataArray)) return [];
        return rawDataArray.map(data => new Section(data));
    }
}

export default Section;

