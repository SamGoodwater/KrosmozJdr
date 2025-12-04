/**
 * Modèle Attribute pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de attribute côté frontend.
 * 
 * @example
 * const attribute = new Attribute(props.attribute);
 * console.log(attribute.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';

export class Attribute extends BaseModel {
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

    get creatures() {
        return this._data.creatures || [];
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

export default Attribute;
