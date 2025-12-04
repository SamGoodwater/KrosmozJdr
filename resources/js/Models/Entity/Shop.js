/**
 * Modèle Shop pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de shop côté frontend.
 * 
 * @example
 * const shop = new Shop(props.shop);
 * console.log(shop.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';

export class Shop extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get name() {
        return this._data.name || '';
    }

    get description() {
        return this._data.description || '';
    }

    get location() {
        return this._data.location || null;
    }

    get price() {
        return this._data.price || null;
    }

    get usable() {
        return this._data.usable ?? false;
    }

    get image() {
        return this._data.image || '';
    }

    get npcId() {
        return this._data.npc_id || null;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get createdBy() {
        return this._data.createdBy || null;
    }

    get npc() {
        return this._data.npc || null;
    }

    get items() {
        return this._data.items || [];
    }

    get panoplies() {
        return this._data.panoplies || [];
    }

    get consumables() {
        return this._data.consumables || [];
    }

    get resources() {
        return this._data.resources || [];
    }

    get scenarios() {
        return this._data.scenarios || [];
    }

    get campaigns() {
        return this._data.campaigns || [];
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
            location: this.location,
            price: this.price,
            usable: this.usable,
            image: this.image,
            npc_id: this.npcId
        };
    }
}

export default Shop;
