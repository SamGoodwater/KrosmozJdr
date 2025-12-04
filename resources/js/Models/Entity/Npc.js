/**
 * Modèle Npc pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de npc côté frontend.
 * 
 * @example
 * const npc = new Npc(props.npc);
 * console.log(npc.creature?.name); // Accès via la relation creature
 */
import { BaseModel } from '../BaseModel';

export class Npc extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get creatureId() {
        return this._data.creature_id || null;
    }

    get story() {
        return this._data.story || null;
    }

    get historical() {
        return this._data.historical || null;
    }

    get age() {
        return this._data.age || null;
    }

    get size() {
        return this._data.size || null;
    }

    get classeId() {
        return this._data.classe_id || null;
    }

    get specializationId() {
        return this._data.specialization_id || null;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get creature() {
        return this._data.creature || null;
    }

    get classe() {
        return this._data.classe || null;
    }

    get specialization() {
        return this._data.specialization || null;
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

    get shop() {
        return this._data.shop || null;
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
            creature_id: this.creatureId,
            story: this.story,
            historical: this.historical,
            age: this.age,
            size: this.size,
            classe_id: this.classeId,
            specialization_id: this.specializationId
        };
    }
}

export default Npc;
