/**
 * Modèle Page pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de page côté frontend.
 * Résout les problèmes d'extraction des données depuis les Proxies Vue/Inertia.
 * 
 * @example
 * const page = new Page(props.page);
 * console.log(page.title); // Accès normalisé
 * console.log(page.canUpdate); // Méthode utilitaire
 * console.log(page.url); // URL de la page
 */
import { BaseModel } from './BaseModel';
import { TransformService } from '@/Utils/Services';

export class Page extends BaseModel {

    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get title() {
        return this._data.title || '';
    }

    get slug() {
        return this._data.slug || '';
    }

    get inMenu() {
        return this._data.in_menu ?? false;
    }

    get state() {
        return this._data.state;
    }

    get readLevel() {
        return this._data.read_level;
    }

    get writeLevel() {
        return this._data.write_level;
    }

    get parentId() {
        return this._data.parent_id;
    }

    get menuOrder() {
        return this._data.menu_order || 0;
    }


    // ============================================
    // RELATIONS
    // ============================================

    get parent() {
        return this._data.parent ? new Page(this._data.parent) : null;
    }

    get children() {
        if (!this._data.children || !Array.isArray(this._data.children)) {
            return [];
        }
        return this._data.children.map(child => new Page(child));
    }

    get sections() {
        if (!this._data.sections || !Array.isArray(this._data.sections)) {
            return [];
        }
        // Les sections seront gérées par la classe Section
        return this._data.sections;
    }


    // ============================================
    // MÉTHODES UTILITAIRES
    // ============================================

    /**
     * Retourne l'URL de la page
     * @returns {string}
     */
    get url() {
        if (!this.slug) return '';
        try {
            // Utiliser route() si disponible (Ziggy)
            if (typeof route !== 'undefined') {
                return route('pages.show', this.slug);
            }
            // Fallback
            return `/pages/${this.slug}`;
        } catch (e) {
            return `/pages/${this.slug}`;
        }
    }

    /**
     * Retourne l'URL complète de la page
     * @returns {string}
     */
    get fullUrl() {
        if (!this.url) return '';
        return window.location.origin + this.url;
    }

    /**
     * Vérifie si la page est jouable
     * @returns {boolean}
     */
    get isPlayable() {
        return this.state === 'playable';
    }

    /**
     * Vérifie si la page est un brouillon
     * @returns {boolean}
     */
    get isDraft() {
        return this.state === 'draft';
    }

    /**
     * Vérifie si la page est visible dans le menu
     * @returns {boolean}
     */
    get isVisibleInMenu() {
        return this.inMenu && this.isPlayable;
    }

    /**
     * Retourne les données pour un formulaire
     * @returns {Object}
     */
    toFormData() {
        return {
            title: this.title,
            slug: this.slug,
            read_level: this.readLevel,
            write_level: this.writeLevel,
            in_menu: this.inMenu,
            state: this.state,
            parent_id: this.parentId,
            menu_order: this.menuOrder
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
            prefix: 'page',
            ...options
        });
    }

    /**
     * Retourne le slug de la page, ou en génère un si vide
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
     * Crée une instance Page depuis des données brutes (méthode statique)
     * @param {*} rawData - Données brutes
     * @returns {Page}
     */
    static from(rawData) {
        return new Page(rawData);
    }

    /**
     * Crée un tableau d'instances Page depuis un tableau de données brutes
     * @param {Array} rawDataArray - Tableau de données brutes
     * @returns {Array<Page>}
     */
    static fromArray(rawDataArray) {
        if (!Array.isArray(rawDataArray)) return [];
        return rawDataArray.map(data => new Page(data));
    }
}

export default Page;

