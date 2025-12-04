/**
 * Modèle Classe pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de classe côté frontend.
 * 
 * @example
 * const classe = new Classe(props.classe);
 * console.log(classe.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';

export class Classe extends BaseModel {
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

    get descriptionFast() {
        return this._data.description_fast || null;
    }

    get description() {
        return this._data.description || '';
    }

    get life() {
        return this._data.life || null;
    }

    get lifeDice() {
        return this._data.life_dice || null;
    }

    get specificity() {
        return this._data.specificity || null;
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

    get icon() {
        return this._data.icon || null;
    }

    get autoUpdate() {
        return this._data.auto_update ?? false;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get createdBy() {
        return this._data.createdBy || null;
    }

    get npcs() {
        return this._data.npcs || [];
    }

    get spells() {
        return this._data.spells || [];
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
            description_fast: this.descriptionFast,
            description: this.description,
            life: this.life,
            life_dice: this.lifeDice,
            specificity: this.specificity,
            dofus_version: this.dofusVersion,
            usable: this.usable,
            image: this.image,
            icon: this.icon,
            auto_update: this.autoUpdate
        };
    }
}

export default Classe;
