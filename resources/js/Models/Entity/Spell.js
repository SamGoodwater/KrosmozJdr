/**
 * Modèle Spell pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de spell côté frontend.
 * 
 * @example
 * const spell = new Spell(props.spell);
 * console.log(spell.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';

export class Spell extends BaseModel {
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

    get area() {
        return this._data.area || null;
    }

    get level() {
        return this._data.level || null;
    }

    get po() {
        return this._data.po || null;
    }

    get poEditable() {
        return this._data.po_editable || null;
    }

    get pa() {
        return this._data.pa || null;
    }

    get castPerTurn() {
        return this._data.cast_per_turn || null;
    }

    get castPerTarget() {
        return this._data.cast_per_target || null;
    }

    get sightLine() {
        return this._data.sight_line || null;
    }

    get numberBetweenTwoCast() {
        return this._data.number_between_two_cast || null;
    }

    get numberBetweenTwoCastEditable() {
        return this._data.number_between_two_cast_editable || null;
    }

    get element() {
        return this._data.element || null;
    }

    get category() {
        return this._data.category || null;
    }

    get isMagic() {
        return this._data.is_magic || null;
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

    get autoUpdate() {
        return this._data.auto_update ?? false;
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

    get classes() {
        return this._data.classes || [];
    }

    get scenarios() {
        return this._data.scenarios || [];
    }

    get campaigns() {
        return this._data.campaigns || [];
    }

    get spellTypes() {
        return this._data.spellTypes || [];
    }

    get monsters() {
        return this._data.monsters || [];
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
            area: this.area,
            level: this.level,
            po: this.po,
            po_editable: this.poEditable,
            pa: this.pa,
            cast_per_turn: this.castPerTurn,
            cast_per_target: this.castPerTarget,
            sight_line: this.sightLine,
            number_between_two_cast: this.numberBetweenTwoCast,
            number_between_two_cast_editable: this.numberBetweenTwoCastEditable,
            element: this.element,
            category: this.category,
            is_magic: this.isMagic,
            powerful: this.powerful,
            usable: this.usable,
            image: this.image,
            auto_update: this.autoUpdate
        };
    }
}

export default Spell;
