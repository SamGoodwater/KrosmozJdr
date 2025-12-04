/**
 * Modèle Specialization pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de specialization côté frontend.
 * 
 * @example
 * const specialization = new Specialization(props.specialization);
 * console.log(specialization.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';

export class Specialization extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get name() {
        return this._data.name || '';
    }

    get description() {
        return this._data.description || '';
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

    get capabilities() {
        return this._data.capabilities || [];
    }

    get npcs() {
        return this._data.npcs || [];
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
            usable: this.usable,
            image: this.image
        };
    }
}

export default Specialization;
