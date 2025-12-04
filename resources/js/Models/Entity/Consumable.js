/**
 * Modèle Consumable pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de consumable côté frontend.
 * 
 * @example
 * const consumable = new Consumable(props.consumable);
 * console.log(consumable.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';

export class Consumable extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get officialId() {
        return this._data.official_id || null;
    }

    get dofusdbId() {
        return this._data.dofusdb_id || null;
    }

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

    get recipe() {
        return this._data.recipe || null;
    }

    get price() {
        return this._data.price || null;
    }

    get rarity() {
        return this._data.rarity || null;
    }

    get usable() {
        return this._data.usable ?? false;
    }

    get dofusVersion() {
        return this._data.dofus_version || null;
    }

    get image() {
        return this._data.image || '';
    }

    get autoUpdate() {
        return this._data.auto_update ?? false;
    }

    get consumableTypeId() {
        return this._data.consumable_type_id || null;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get createdBy() {
        return this._data.createdBy || null;
    }

    get consumableType() {
        return this._data.consumableType || [];
    }

    get resources() {
        return this._data.resources || [];
    }

    get creatures() {
        return this._data.creatures || [];
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
            official_id: this.officialId,
            dofusdb_id: this.dofusdbId,
            name: this.name,
            description: this.description,
            effect: this.effect,
            level: this.level,
            recipe: this.recipe,
            price: this.price,
            rarity: this.rarity,
            usable: this.usable,
            dofus_version: this.dofusVersion,
            image: this.image,
            auto_update: this.autoUpdate,
            consumable_type_id: this.consumableTypeId
        };
    }
}

export default Consumable;
