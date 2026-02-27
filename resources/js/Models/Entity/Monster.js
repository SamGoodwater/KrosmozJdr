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
import CreatureSummaryCell from '@/Pages/Molecules/entity/creature/CreatureSummaryCell.vue';

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
            case 'creature_summary_resistance':
                return this._toSummaryResistanceCell(options);
            case 'creature_summary_damage':
                return this._toSummaryDamageCell(options);
            case 'creature_summary_stats':
                return this._toSummaryStatsCell(options);
            case 'creature_summary_combat':
                return this._toSummaryCombatCell(options);
            case 'creature_summary_control':
                return this._toSummaryControlCell(options);
            default:
                if (fieldKey.startsWith('creature_')) {
                    const creatureKey = fieldKey.slice(9);
                    return this._toCreatureFieldCell(creatureKey, options);
                }
                return baseCell;
        }
    }

    /**
     * Génère une cellule pour un champ de la créature (level, life, pa, etc.)
     * @private
     * @param {string} creatureKey - Clé du champ sur l'objet creature (ex: level, life, pa)
     * @param {Object} [_options] - Options passées à toCell
     * @returns {Object} Cell object
     */
    _toCreatureFieldCell(creatureKey, _options) {
        const creature = this.creature;
        if (!creature || typeof creature !== 'object') {
            return creatureKey === 'image'
                ? { type: 'image', value: '', params: { sortValue: '', searchValue: '' } }
                : { type: 'text', value: '-', params: { sortValue: '', searchValue: '', filterValue: null } };
        }
        let raw = creature[creatureKey];
        if (creatureKey === 'image') {
            const url = raw && String(raw).trim() ? String(raw) : '';
            return {
                type: 'image',
                value: url,
                params: { sortValue: url ? 1 : 0, searchValue: '', alt: this.creature?.name || 'Créature' },
            };
        }
        const rawForFilter = raw !== null && raw !== undefined && raw !== '' ? String(raw) : null;
        if (raw === null || raw === undefined || raw === '') {
            return {
                type: 'text',
                value: '-',
                params: { sortValue: '', searchValue: '', filterValue: null },
            };
        }
        if (creatureKey === 'hostility') {
            const labels = { 0: 'Amical', 1: 'Curieux', 2: 'Neutre', 3: 'Hostile', 4: 'Agressif' };
            const displayRaw = labels[Number(raw)] ?? String(raw);
            return {
                type: 'text',
                value: displayRaw,
                params: {
                    sortValue: Number(raw),
                    searchValue: displayRaw,
                    filterValue: String(raw),
                },
            };
        }
        const value = String(raw);
        const sortValue = Number(raw);
        return {
            type: 'text',
            value,
            params: {
                sortValue: Number.isFinite(sortValue) ? sortValue : value,
                searchValue: value,
                filterValue: rawForFilter ?? value,
            },
        };
    }

    /**
     * Colonne résumée : résistances fixes + % (neutre, terre, feu, air, eau)
     * @private
     */
    _toSummaryResistanceCell(_options) {
        const c = this.creature;
        const ctx = _options?.ctx || _options?.context || null;
        const byDb = ctx?.characteristics?.creature?.byDbColumn || {};

        // filterValue: toutes les valeurs de résistances fixes + %
        const elements = ['neutre', 'terre', 'feu', 'air', 'eau'];
        const filterParts = [];
        if (c && typeof c === 'object') {
            for (const el of elements) {
                const fixed = c[`res_fixe_${el}`];
                const percent = c[`res_${el}`];
                if (fixed !== null && typeof fixed !== 'undefined' && String(fixed) !== '') {
                    filterParts.push(String(fixed));
                }
                if (percent !== null && typeof percent !== 'undefined' && String(percent) !== '') {
                    filterParts.push(String(percent));
                }
            }
        }
        const filterValue = filterParts.join(' ');

        return {
            type: 'chips',
            value: '',
            params: {
                component: CreatureSummaryCell,
                componentProps: {
                    variant: 'resistance',
                    creature: this.creature,
                    characteristicsByDbColumn: byDb,
                },
                passValue: false,
                sortValue: 0,
                searchValue: filterValue,
                filterValue,
            },
        };
    }

    /**
     * Colonne résumée : bonus de touche + dommages fixes (neutre, terre, feu, air, eau)
     * @private
     */
    _toSummaryDamageCell(_options) {
        const c = this.creature;
        const ctx = _options?.ctx || _options?.context || null;
        const byDb = ctx?.characteristics?.creature?.byDbColumn || {};

        const elements = ['neutre', 'terre', 'feu', 'air', 'eau'];
        const filterParts = [];
        if (c && typeof c === 'object') {
            if (c.touch !== null && typeof c.touch !== 'undefined' && String(c.touch) !== '') {
                filterParts.push(String(c.touch));
            }
            for (const el of elements) {
                const v = c[`do_fixe_${el}`];
                if (v !== null && typeof v !== 'undefined' && String(v) !== '') {
                    filterParts.push(String(v));
                }
            }
        }
        const filterValue = filterParts.join(' ');

        return {
            type: 'chips',
            value: '',
            params: {
                component: CreatureSummaryCell,
                componentProps: {
                    variant: 'damage',
                    creature: this.creature,
                    characteristicsByDbColumn: byDb,
                },
                passValue: false,
                sortValue: 0,
                searchValue: filterValue,
                filterValue,
            },
        };
    }

    /**
     * Colonne résumée : Force, Intel, Agi, Chance, Vitalité, Sagesse
     * @private
     */
    _toSummaryStatsCell(_options) {
        const c = this.creature;
        const ctx = _options?.ctx || _options?.context || null;
        const byDb = ctx?.characteristics?.creature?.byDbColumn || {};

        const statKeys = ['strong', 'intel', 'agi', 'chance', 'vitality', 'sagesse'];
        const filterParts = [];
        if (c && typeof c === 'object') {
            for (const key of statKeys) {
                const v = c[key];
                if (v !== null && typeof v !== 'undefined' && String(v) !== '') {
                    filterParts.push(String(v));
                }
            }
        }
        const filterValue = filterParts.join(' ');

        return {
            type: 'chips',
            value: '',
            params: {
                component: CreatureSummaryCell,
                componentProps: {
                    variant: 'stats',
                    creature: this.creature,
                    characteristicsByDbColumn: byDb,
                },
                passValue: false,
                sortValue: 0,
                searchValue: filterValue,
                filterValue,
            },
        };
    }

    /**
     * Colonne résumée : PA, PM, PO, PV, Initiative, Invocation
     * @private
     */
    _toSummaryCombatCell(_options) {
        const c = this.creature;
        const ctx = _options?.ctx || _options?.context || null;
        const byDb = ctx?.characteristics?.creature?.byDbColumn || {};

        const combatKeys = ['pa', 'pm', 'po', 'life', 'ini', 'invocation'];
        const filterParts = [];
        if (c && typeof c === 'object') {
            for (const key of combatKeys) {
                const v = c[key];
                if (v !== null && typeof v !== 'undefined' && String(v) !== '') {
                    filterParts.push(String(v));
                }
            }
        }
        const filterValue = filterParts.join(' ');

        return {
            type: 'chips',
            value: '',
            params: {
                component: CreatureSummaryCell,
                componentProps: {
                    variant: 'combat',
                    creature: this.creature,
                    characteristicsByDbColumn: byDb,
                },
                passValue: false,
                sortValue: 0,
                searchValue: filterValue,
                filterValue,
            },
        };
    }

    /**
     * Colonne résumée : contrôle (CA + esquive PA/PM + fuite + tacle)
     * @private
     */
    _toSummaryControlCell(_options) {
        const c = this.creature;
        const ctx = _options?.ctx || _options?.context || null;
        const byDb = ctx?.characteristics?.creature?.byDbColumn || {};

        const ctrlKeys = ['ca', 'dodge_pa', 'dodge_pm', 'fuite', 'tacle'];
        const filterParts = [];
        if (c && typeof c === 'object') {
            for (const key of ctrlKeys) {
                const v = c[key];
                if (v !== null && typeof v !== 'undefined' && String(v) !== '') {
                    filterParts.push(String(v));
                }
            }
        }
        const filterValue = filterParts.join(' ');

        return {
            type: 'chips',
            value: '',
            params: {
                component: CreatureSummaryCell,
                componentProps: {
                    variant: 'control',
                    creature: this.creature,
                    characteristicsByDbColumn: byDb,
                },
                passValue: false,
                sortValue: 0,
                searchValue: filterValue,
                filterValue,
            },
        };
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
    _toMonsterRaceCell(_format, _size, _options) {
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
    _toSizeCell(_format, _size, _options) {
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
                filterValue: sizeValue !== null ? String(sizeValue) : null,
            },
        };
    }

    /**
     * Génère une cellule pour is_boss
     * @private
     */
    _toIsBossCell(_format, _size, _options) {
        const isBoss = !!this.isBoss;
        const rawPa = this.bossPa;
        const paNumber = rawPa !== null && rawPa !== undefined && rawPa !== '' ? Number(rawPa) : null;

        let label = '';
        if (isBoss) {
            if (paNumber !== null && !Number.isNaN(paNumber) && paNumber > 0) {
                label = `Boss +${paNumber} PA`;
            } else if (rawPa && String(rawPa) !== '0') {
                label = `Boss +${rawPa} PA`;
            } else {
                label = 'Boss';
            }
        }

        const tooltip =
            "Les boss ont des PA supplémentaires qu'ils peuvent utiliser entre leurs tours à n'importe quel moment.";

        return {
            type: 'badge',
            value: label,
            params: {
                color: isBoss ? 'error' : 'base',
                sortValue: isBoss ? (paNumber || 0) + 1 : 0,
                searchValue: label,
                filterValue: isBoss ? '1' : '0',
                tooltip: isBoss ? tooltip : '',
            },
        };
    }

    /**
     * Génère une cellule pour boss_pa
     * @private
     */
    _toBossPaCell(_format, _size, _options) {
        const rawPa = this.bossPa;
        const paNumber = rawPa !== null && Number(rawPa) > 0 && rawPa !== undefined && rawPa !== '' ? Number(rawPa) : null;

        let display = '-';
        if (paNumber !== null && !Number.isNaN(paNumber) && paNumber > 0) {
            display = `${paNumber} PA`;
        } else if (rawPa && String(rawPa) !== '0') {
            display = `${rawPa} PA`;
        }

        return {
            type: 'text',
            value: display,
            params: {
                sortValue: paNumber && !Number.isNaN(paNumber) ? paNumber : 0,
                searchValue: display === '-' ? '' : display,
            },
        };
    }

    /**
     * Génère une cellule pour la date de création
     * @private
     */
    _toCreatedAtCell(_format, _size, options) {
        return super.toCell('created_at', options);
    }

    /**
     * Génère une cellule pour la date de modification
     * @private
     */
    _toUpdatedAtCell(_format, _size, options) {
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
