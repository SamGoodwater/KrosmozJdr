/**
 * Modèle Resource pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de resource côté frontend.
 * 
 * @example
 * const resource = new Resource(props.resource);
 * console.log(resource.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';

export class Resource extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get dofusdbId() {
        return this._data.dofusdb_id || null;
    }

    get officialId() {
        return this._data.official_id || null;
    }

    get name() {
        return this._data.name || '';
    }

    get description() {
        return this._data.description || '';
    }

    get level() {
        return this._data.level ?? null;
    }

    get price() {
        return this._data.price ?? null;
    }

    get weight() {
        return this._data.weight ?? null;
    }

    get rarity() {
        // La colonne est NOT NULL en base : on garantit un int.
        return this._data.rarity ?? 0;
    }

    get dofusVersion() {
        return this._data.dofus_version ?? null;
    }

    get usable() {
        return Boolean(this._data.usable);
    }

    get image() {
        return this._data.image || '';
    }

    get autoUpdate() {
        return Boolean(this._data.auto_update);
    }

    get resourceTypeId() {
        return this._data.resource_type_id || null;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get createdBy() {
        return this._data.createdBy || null;
    }

    get resourceType() {
        return this._data.resourceType || null;
    }

    get consumables() {
        return this._data.consumables || [];
    }

    get creatures() {
        return this._data.creatures || [];
    }

    get items() {
        return this._data.items || [];
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
            official_id: this.officialId,
            name: this.name,
            description: this.description,
            level: this.level,
            price: this.price,
            weight: this.weight,
            rarity: this.rarity,
            dofus_version: this.dofusVersion,
            usable: this.usable,
            is_visible: this._data.is_visible ?? null,
            image: this.image,
            auto_update: this.autoUpdate,
            resource_type_id: this.resourceTypeId
        };
    }
}

export default Resource;
