/**
 * Modèle Item pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données d'item côté frontend.
 * 
 * @example
 * const item = new Item(props.item);
 * console.log(item.name); // Accès normalisé
 * console.log(item.canUpdate); // Permissions
 */
import { BaseModel } from '../BaseModel';

export class Item extends BaseModel {
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

    get level() {
        return this._data.level || null;
    }

    get description() {
        return this._data.description || '';
    }

    get effect() {
        return this._data.effect || null;
    }

    get bonus() {
        return this._data.bonus || null;
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

    get dofusVersion() {
        return this._data.dofus_version || null;
    }

    get usable() {
        return this._data.usable ?? false;
    }

    get image() {
        return this._data.image || '';
    }

    get autoUpdate() {
        return this._data.auto_update ?? false;
    }

    get itemTypeId() {
        return this._data.item_type_id || null;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get createdBy() {
        return this._data.createdBy || null;
    }

    get itemType() {
        return this._data.itemType || null;
    }

    get resources() {
        return this._data.resources || [];
    }

    get panoplies() {
        return this._data.panoplies || [];
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
            level: this.level,
            description: this.description,
            effect: this.effect,
            bonus: this.bonus,
            recipe: this.recipe,
            price: this.price,
            rarity: this.rarity,
            dofus_version: this.dofusVersion,
            usable: this.usable,
            image: this.image,
            auto_update: this.autoUpdate,
            item_type_id: this.itemTypeId
        };
    }
}

export default Item;
