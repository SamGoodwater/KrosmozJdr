/**
 * Modèle Monster pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de monster côté frontend.
 * 
 * @example
 * const monster = new Monster(props.monster);
 * console.log(monster.creature?.name); // Accès via la relation creature
 */
import { BaseModel } from '../BaseModel';

export class Monster extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get creatureId() {
        return this._data.creature_id || null;
    }

    get officialId() {
        return this._data.official_id || null;
    }

    get dofusdbId() {
        return this._data.dofusdb_id || null;
    }

    get dofusVersion() {
        return this._data.dofus_version || null;
    }

    get autoUpdate() {
        return this._data.auto_update ?? false;
    }

    get size() {
        return this._data.size || null;
    }

    get monsterRaceId() {
        return this._data.monster_race_id || null;
    }

    get isBoss() {
        return this._data.is_boss ?? false;
    }

    get bossPa() {
        return this._data.boss_pa || null;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get creature() {
        return this._data.creature || null;
    }

    get monsterRace() {
        return this._data.monsterRace || null;
    }

    get scenarios() {
        return this._data.scenarios || [];
    }

    get campaigns() {
        return this._data.campaigns || [];
    }

    get spellInvocations() {
        return this._data.spellInvocations || [];
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
            official_id: this.officialId,
            dofusdb_id: this.dofusdbId,
            dofus_version: this.dofusVersion,
            auto_update: this.autoUpdate,
            size: this.size,
            monster_race_id: this.monsterRaceId,
            is_boss: this.isBoss,
            boss_pa: this.bossPa
        };
    }
}

export default Monster;
