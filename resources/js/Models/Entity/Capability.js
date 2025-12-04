/**
 * Modèle Capability pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de capability côté frontend.
 * 
 * @example
 * const capability = new Capability(props.capability);
 * console.log(capability.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';

export class Capability extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get name() {
        return this._data.name || '';
    }

    get description() {
        return this._data.description || '';
    }

    get effect() {
        return this._data.effect || null;
    }

    get level() {
        return this._data.level || null;
    }

    get pa() {
        return this._data.pa || null;
    }

    get po() {
        return this._data.po || null;
    }

    get poEditable() {
        return this._data.po_editable || null;
    }

    get timeBeforeUseAgain() {
        return this._data.time_before_use_again || null;
    }

    get castingTime() {
        return this._data.casting_time || null;
    }

    get duration() {
        return this._data.duration || null;
    }

    get element() {
        return this._data.element || null;
    }

    get isMagic() {
        return this._data.is_magic || null;
    }

    get ritualAvailable() {
        return this._data.ritual_available || null;
    }

    get powerful() {
        return this._data.powerful || null;
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

    get specializations() {
        return this._data.specializations || [];
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
            effect: this.effect,
            level: this.level,
            pa: this.pa,
            po: this.po,
            po_editable: this.poEditable,
            time_before_use_again: this.timeBeforeUseAgain,
            casting_time: this.castingTime,
            duration: this.duration,
            element: this.element,
            is_magic: this.isMagic,
            ritual_available: this.ritualAvailable,
            powerful: this.powerful,
            usable: this.usable,
            image: this.image
        };
    }
}

export default Capability;
