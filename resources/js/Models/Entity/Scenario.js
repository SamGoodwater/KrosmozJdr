/**
 * Modèle Scenario pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de scenario côté frontend.
 * 
 * @example
 * const scenario = new Scenario(props.scenario);
 * console.log(scenario.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';

export class Scenario extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get name() {
        return this._data.name || '';
    }

    get description() {
        return this._data.description || '';
    }

    get slug() {
        return this._data.slug || null;
    }

    get keyword() {
        return this._data.keyword || null;
    }

    get isPublic() {
        return this._data.is_public ?? false;
    }

    get state() {
        return this._data.state || null;
    }

    get usable() {
        return this._data.usable ?? false;
    }

    get image() {
        return this._data.image || '';
    }

    // ============================================
    // RELATIONS
    // ============================================

    get createdBy() {
        return this._data.createdBy || null;
    }

    get users() {
        return this._data.users || [];
    }

    get pages() {
        return this._data.pages || [];
    }

    get campaigns() {
        return this._data.campaigns || [];
    }

    get npcs() {
        return this._data.npcs || [];
    }

    get monsters() {
        return this._data.monsters || [];
    }

    get items() {
        return this._data.items || [];
    }

    get consumables() {
        return this._data.consumables || [];
    }

    get resources() {
        return this._data.resources || [];
    }

    get shops() {
        return this._data.shops || [];
    }

    get spells() {
        return this._data.spells || [];
    }

    get panoplies() {
        return this._data.panoplies || [];
    }

    get files() {
        return this._data.files || [];
    }

    get scenarioLinks() {
        return this._data.scenarioLinks || [];
    }

    /**
     * Retourne l'URL de l'entité
     * @returns {string}
     */
    get url() {
        if (!this.slug) return '';
        try {
            if (typeof route !== 'undefined') {
                return route('entities.scenarios.show', this.slug);
            }
            return `/entities/scenarios/${this.slug}`;
        } catch (e) {
            return `/entities/scenarios/${this.slug}`;
        }
    }

    /**
     * Retourne l'URL complète de l'entité
     * @returns {string}
     */
    get fullUrl() {
        if (!this.url) return '';
        return window.location.origin + this.url;
    }
    // ============================================
    // MÉTHODES UTILITAIRES
    // ============================================

    /**
     * Retourne les données pour un formulaire
     * @returns {Object}
     */
    toFormData() {
        return {
            name: this.name,
            description: this.description,
            slug: this.slug,
            keyword: this.keyword,
            is_public: this.isPublic,
            state: this.state,
            usable: this.usable,
            image: this.image
        };
    }
}

export default Scenario;
