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
    // FORMATAGE DES CELLULES (surcharge pour champs spécifiques)
    // ============================================

    /**
     * Génère une cellule pour un champ (surcharge pour gérer les champs spécifiques à Monster)
     * @param {string} fieldKey - Clé du champ
     * @param {Object} [options={}] - Options (size, context, config, ctx)
     * @returns {Object|null} Cell object ou null si valeur invalide
     */
    toCell(fieldKey, options = {}) {
        // D'abord, essayer la méthode de base (gère les formatters automatiquement)
        const baseCell = super.toCell(fieldKey, options);
        
        // Si la méthode de base a trouvé quelque chose (formatter ou valeur par défaut valide), l'utiliser
        if (baseCell && (baseCell.type !== 'text' || (baseCell.value && baseCell.value !== '-'))) {
            return baseCell;
        }

        // Sinon, gérer les champs spécifiques à Monster
        const { size = 'md', format = {} } = options;
        
        switch (fieldKey) {
            case 'creature_name':
            case 'creatureName':
                return this._toCreatureNameCell(format, size, options);
            case 'monster_race':
            case 'monsterRace':
                return this._toMonsterRaceCell(format, size, options);
            case 'size':
                return this._toSizeCell(format, size, options);
            case 'is_boss':
            case 'isBoss':
                return this._toIsBossCell(format, size, options);
            case 'boss_pa':
            case 'bossPa':
                return this._toBossPaCell(format, size, options);
            case 'created_at':
                return this._toCreatedAtCell(format, size, options);
            case 'updated_at':
                return this._toUpdatedAtCell(format, size, options);
            default:
                // Fallback vers la méthode de base
                return baseCell;
        }
    }

    /**
     * Génère une cellule pour le nom de la créature (lien vers la page de détail)
     * @private
     */
    _toCreatureNameCell(format, size, options) {
        const creature = this.creature;
        if (!creature) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                },
            };
        }

        const name = creature.name || '-';
        const href = options.href || `/creatures/${creature.id}`;
        
        return {
            type: 'route',
            value: name,
            params: {
                href,
                tooltip: name === '-' ? '' : name,
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 20 : null),
                searchValue: name === '-' ? '' : name,
                sortValue: name,
            },
        };
    }

    /**
     * Génère une cellule pour la race du monstre
     * @private
     */
    _toMonsterRaceCell(format, size, options) {
        const monsterRace = this.monsterRace;
        
        if (!monsterRace) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                },
            };
        }

        const raceName = monsterRace.name || monsterRace.label || '-';
        
        return {
            type: 'text',
            value: raceName,
            params: {
                sortValue: raceName,
                searchValue: raceName,
            },
        };
    }

    /**
     * Génère une cellule pour la taille
     * @private
     */
    _toSizeCell(format, size, options) {
        // Utiliser le formatter via la méthode de base si disponible
        const cell = super.toCell('size', options);
        if (cell && cell.type !== 'text') {
            return cell;
        }

        const sizeValue = this.size ?? null;
        const sizeLabels = {
            0: 'Minuscule',
            1: 'Petit',
            2: 'Moyen',
            3: 'Grand',
            4: 'Colossal',
            5: 'Gigantesque',
        };
        const label = sizeValue !== null && sizeLabels[sizeValue] ? sizeLabels[sizeValue] : (sizeValue !== null ? String(sizeValue) : '-');
        
        return {
            type: 'text',
            value: label,
            params: {
                sortValue: sizeValue ?? 0,
                searchValue: label === '-' ? '' : label,
            },
        };
    }

    /**
     * Génère une cellule pour is_boss
     * @private
     */
    _toIsBossCell(format, size, options) {
        // Utiliser le BooleanFormatter via la méthode de base
        return super.toCell('is_boss', options);
    }

    /**
     * Génère une cellule pour boss_pa
     * @private
     */
    _toBossPaCell(format, size, options) {
        const bossPa = this.bossPa || '-';
        
        return {
            type: 'text',
            value: bossPa,
            params: {
                sortValue: bossPa === '-' ? '' : bossPa,
                searchValue: bossPa === '-' ? '' : bossPa,
            },
        };
    }

    /**
     * Génère une cellule pour la date de création
     * @private
     */
    _toCreatedAtCell(format, size, options) {
        // Utiliser le DateFormatter via la méthode de base
        return super.toCell('created_at', options);
    }

    /**
     * Génère une cellule pour la date de modification
     * @private
     */
    _toUpdatedAtCell(format, size, options) {
        // Utiliser le DateFormatter via la méthode de base
        return super.toCell('updated_at', options);
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
