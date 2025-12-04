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

    get isVisible() {
        return this._data.is_visible;
    }

    get canEditRole() {
        return this._data.can_edit_role;
    }

    get inMenu() {
        return this._data.in_menu ?? false;
    }

    get state() {
        return this._data.state;
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
     * Vérifie si la page est publiée
     * @returns {boolean}
     */
    get isPublished() {
        return this.state === 'published';
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
        return this.inMenu && this.isPublished;
    }

    /**
     * Retourne les données pour un formulaire
     * @returns {Object}
     */
    toFormData() {
        return {
            title: this.title,
            slug: this.slug,
            is_visible: this.isVisible,
            can_edit_role: this.canEditRole,
            in_menu: this.inMenu,
            state: this.state,
            parent_id: this.parentId,
            menu_order: this.menuOrder
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

