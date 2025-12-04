/**
 * Modèle Panoply pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de panoply côté frontend.
 * 
 * @example
 * const panoply = new Panoply(props.panoply);
 * console.log(panoply.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';

export class Panoply extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get dofusdbId() {
        return this._data.dofusdb_id || null;
    }

    get name() {
        return this._data.name || '';
    }

    get description() {
        return this._data.description || '';
    }

    get bonus() {
        return this._data.bonus || null;
    }

    get usable() {
        return this._data.usable ?? false;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get createdBy() {
        return this._data.createdBy || null;
    }

    get items() {
        return this._data.items || [];
    }

    get npcs() {
        return this._data.npcs || [];
    }

    get scenarios() {
        return this._data.scenarios || [];
    }

    get campaigns() {
        return this._data.campaigns || [];
    }

    get shops() {
        return this._data.shops || [];
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
            dofusdb_id: this.dofusdbId,
            name: this.name,
            description: this.description,
            bonus: this.bonus,
            usable: this.usable
        };
    }
}

export default Panoply;
