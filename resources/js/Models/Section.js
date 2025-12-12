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
import { TransformService } from '@/Utils/Services';
import { Page } from './Page';

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

    get title() {
        return this._data.title || null;
    }

    get slug() {
        return this._data.slug || null;
    }

    get template() {
        return this._data.template;
    }

    get settings() {
        return this._data.settings || {};
    }

    get data() {
        return this._data.data || {};
    }

    // Compatibilité avec l'ancien code (type/params)
    get type() {
        return this.template;
    }

    get params() {
        // Fusionner settings et data pour la compatibilité
        return { ...this.settings, ...this.data };
    }

    get isVisible() {
        return this._data.is_visible;
    }

    get state() {
        return this._data.state;
    }


    // ============================================
    // RELATIONS
    // ============================================

    get page() {
        if (!this._data.page) return null;
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
     * Retourne le contenu de la section selon son template
     * @returns {*}
     */
    get content() {
        switch (this.template) {
            case 'text':
                return this.data.content || '';
            case 'image':
                return this.data.src || '';
            case 'gallery':
                return this.data.images || [];
            case 'video':
                return this.data.src || '';
            default:
                return this.data;
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
            title: this.title,
            slug: this.slug,
            order: this.order,
            template: this.template,
            settings: this.settings,
            data: this.data,
            is_visible: this.isVisible,
            can_edit_role: this._data.can_edit_role,
            state: this.state
        };
    }

    /**
     * Génère un slug depuis le titre ou l'ID
     * 
     * @param {Object} options - Options de génération
     * @returns {String} Slug généré
     */
    generateSlug(options = {}) {
        return TransformService.generateSlug(this.title, this.id, {
            prefix: 'section',
            ...options
        });
    }

    /**
     * Retourne le slug de la section, ou en génère un si vide
     * 
     * @param {Object} options - Options de génération
     * @returns {String} Slug (existant ou généré)
     */
    getSlugOrGenerate(options = {}) {
        return this.slug || this.generateSlug(options);
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

