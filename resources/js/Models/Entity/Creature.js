/**
 * Modèle Creature pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de creature côté frontend.
 * 
 * @example
 * const creature = new Creature(props.creature);
 * console.log(creature.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';

export class Creature extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get name() {
        return this._data.name || '';
    }

    get description() {
        return this._data.description || '';
    }

    get hostility() {
        return this._data.hostility || null;
    }

    get location() {
        return this._data.location || null;
    }

    get level() {
        return this._data.level || null;
    }

    get life() {
        return this._data.life || null;
    }

    get pa() {
        return this._data.pa || null;
    }

    get pm() {
        return this._data.pm || null;
    }

    get po() {
        return this._data.po || null;
    }

    get ini() {
        return this._data.ini || null;
    }

    get invocation() {
        return this._data.invocation || null;
    }

    get touch() {
        return this._data.touch || null;
    }

    get ca() {
        return this._data.ca || null;
    }

    get dodgePa() {
        return this._data.dodge_pa || null;
    }

    get dodgePm() {
        return this._data.dodge_pm || null;
    }

    get fuite() {
        return this._data.fuite || null;
    }

    get tacle() {
        return this._data.tacle || null;
    }

    get vitality() {
        return this._data.vitality || null;
    }

    get sagesse() {
        return this._data.sagesse || null;
    }

    get strong() {
        return this._data.strong || null;
    }

    get intel() {
        return this._data.intel || null;
    }

    get agi() {
        return this._data.agi || null;
    }

    get chance() {
        return this._data.chance || null;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get createdBy() {
        return this._data.createdBy || null;
    }

    get attributes() {
        return this._data.attributes || [];
    }

    get capabilities() {
        return this._data.capabilities || [];
    }

    get items() {
        return this._data.items || [];
    }

    get resources() {
        return this._data.resources || [];
    }

    get spells() {
        return this._data.spells || [];
    }

    get consumables() {
        return this._data.consumables || [];
    }

    get npc() {
        return this._data.npc || null;
    }

    get monster() {
        return this._data.monster || [];
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
            hostility: this.hostility,
            location: this.location,
            level: this.level,
            life: this.life,
            pa: this.pa,
            pm: this.pm,
            po: this.po,
            ini: this.ini,
            invocation: this.invocation,
            touch: this.touch,
            ca: this.ca,
            dodge_pa: this.dodgePa,
            dodge_pm: this.dodgePm,
            fuite: this.fuite,
            tacle: this.tacle,
            vitality: this.vitality,
            sagesse: this.sagesse,
            strong: this.strong,
            intel: this.intel,
            agi: this.agi,
            chance: this.chance
        };
    }
}

export default Creature;
