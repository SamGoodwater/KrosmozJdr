/**
 * Modèle Npc pour le frontend
 *
 * @description
 * Classe pour normaliser et manipuler les données de npc côté frontend.
 * Réutilise les colonnes résumé créature (Combat, Résistances, Stats, Dommages, Contrôle) comme Monster.
 *
 * @example
 * const npc = new Npc(props.npc);
 * console.log(npc.creature?.name); // Accès via la relation creature
 */
import { BaseModel } from '../BaseModel';
import CharacteristicsCard from '@/Pages/Organismes/data-display/CharacteristicsCard.vue';
import { buildCreatureCharacteristicGroups } from '@/Utils/Entity/buildCreatureCharacteristicGroups';

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

    get breedId() {
        return this._data.breed_id || null;
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

    get breed() {
        return this._data.breed || null;
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

    get panopliesCount() {
        return Number(this._data.panoplies_count ?? this.panoplies.length ?? 0);
    }

    get campaignsCount() {
        return Number(this._data.campaigns_count ?? this.campaigns.length ?? 0);
    }

    get scenariosCount() {
        return Number(this._data.scenarios_count ?? this.scenarios.length ?? 0);
    }

    get hasShop() {
        return Boolean(this._data.has_shop ?? this.shop);
    }

    // ============================================
    // FORMATAGE DES CELLULES (surcharge pour champs spécifiques)
    // ============================================

    /**
     * Génère une cellule pour un champ (surcharge pour gérer les champs spécifiques à NPC)
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

        // Sinon, gérer les champs spécifiques à NPC
        const { size = 'md', format = {} } = options;
        
        switch (fieldKey) {
            case 'creature_name':
            case 'creatureName':
                return this._toCreatureNameCell(format, size, options);
            case 'breed':
            case 'breed_id':
                return this._toBreedCell(format, size, options);
            case 'specialization':
            case 'specialization_id':
                return this._toSpecializationCell(format, size, options);
            case 'creature_level':
                return this._toCreatureLevelCell(options);
            case 'creature_state':
                return this._toCreatureStateCell(options);
            case 'story':
                return this._toStoryCell(format, size, options);
            case 'historical':
                return this._toHistoricalCell(format, size, options);
            case 'age':
                return this._toAgeCell(format, size, options);
            case 'size':
                return this._toSizeCell(format, size, options);
            case 'created_at':
                return this._toCreatedAtCell(format, size, options);
            case 'updated_at':
                return this._toUpdatedAtCell(format, size, options);
            case 'creature_characteristics':
                return this._toCreatureCharacteristicsCell(options);
            case 'creature_summary_combat':
                return this._toSummaryCombatCell(options);
            case 'creature_summary_resistance':
                return this._toSummaryResistanceCell(options);
            case 'creature_summary_damage':
                return this._toSummaryDamageCell(options);
            case 'creature_summary_stats':
                return this._toSummaryStatsCell(options);
            case 'creature_summary_control':
                return this._toSummaryControlCell(options);
            case 'npc_summary_relations':
                return this._toNpcSummaryRelationsCell(options);
            default:
                if (fieldKey.startsWith('creature_')) {
                    const creatureKey = fieldKey.slice(9);
                    return this._toCreatureFieldCell(creatureKey, options);
                }
                // Fallback vers la méthode de base
                return baseCell;
        }
    }

    /** @private Données brutes de la créature (pour buildCreatureCharacteristicGroups). */
    _getCreatureData() {
        return this._data?.creature ?? null;
    }

    /**
     * Retourne la map des caractéristiques créature indexées par db_column.
     * @private
     */
    _getCreatureCharacteristicsByColumn(options = {}) {
        return options?.ctx?.characteristics?.creature?.byDbColumn || {};
    }

    /**
     * Résout une caractéristique par ses colonnes candidates (ex: pa, po, life).
     * @private
     */
    _getCreatureCharacteristicDef(options = {}, candidates = []) {
        const byColumn = this._getCreatureCharacteristicsByColumn(options);
        for (const key of candidates) {
            const found = byColumn?.[key];
            if (found) return found;
        }
        return null;
    }

    /**
     * Génère une cellule pour un champ de la créature (pa, pm, po, life, etc.).
     * @private
     */
    _toCreatureFieldCell(creatureKey, options = {}) {
        const creature = this._getCreatureData();
        if (!creature || typeof creature !== 'object') {
            return creatureKey === 'image'
                ? { type: 'image', value: '', params: { sortValue: '', searchValue: '' } }
                : { type: 'text', value: '-', params: { sortValue: '', searchValue: '', filterValue: null } };
        }

        const raw = creature[creatureKey];
        if (creatureKey === 'image') {
            const url = raw && String(raw).trim() ? String(raw) : '';
            return {
                type: 'image',
                value: url,
                params: { sortValue: url ? 1 : 0, searchValue: '', alt: creature?.name || 'Créature' },
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

        const characteristicDef = this._getCreatureCharacteristicDef(options, [creatureKey]);
        if (characteristicDef) {
            const value = String(raw);
            const label = characteristicDef?.short_name || characteristicDef?.name || creatureKey.toUpperCase();
            const numericSort = Number(raw);
            return {
                type: 'chips',
                value: '',
                params: {
                    items: [
                        {
                            icon: characteristicDef.icon || null,
                            color: characteristicDef.color || null,
                            value,
                            tooltip: `${label}: ${value}`,
                        },
                    ],
                    sortValue: Number.isFinite(numericSort) ? numericSort : value,
                    searchValue: value,
                    filterValue: rawForFilter ?? value,
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

    /** @private Cellule niveau (créature). */
    _toCreatureLevelCell(_options) {
        const raw = this._data?.creature_level ?? this._data?.creature?.level ?? null;
        const v = raw !== null && raw !== undefined && raw !== '' ? String(raw) : '—';
        return {
            type: 'badge',
            value: v,
            params: {
                filterValue: raw !== null && raw !== undefined && raw !== '' ? String(raw) : '',
                sortValue: typeof raw === 'number' ? raw : (typeof raw === 'string' && /^\d+$/.test(raw) ? parseInt(raw, 10) : raw ?? ''),
                searchValue: v,
            },
        };
    }

    /** @private Cellule état (créature). */
    _toCreatureStateCell(_options) {
        const raw = this._data?.creature_state ?? this._data?.creature?.state ?? null;
        const v = raw !== null && raw !== undefined && raw !== '' ? String(raw) : '—';
        return {
            type: 'text',
            value: v,
            params: {
                filterValue: raw !== null && raw !== undefined && raw !== '' ? String(raw) : '',
                sortValue: v,
                searchValue: v,
            },
        };
    }

    /** @private Cellule « Caractéristiques (tout) ». */
    _toCreatureCharacteristicsCell(_options) {
        const ctx = _options?.ctx ?? _options?.context ?? null;
        const byDb = ctx?.characteristics?.creature?.byDbColumn ?? {};
        const creatureData = this._getCreatureData();
        const groups = buildCreatureCharacteristicGroups(creatureData, byDb);
        const c = creatureData;
        const elements = ['neutre', 'terre', 'feu', 'air', 'eau'];
        const filterParts = [];
        if (c && typeof c === 'object') {
            for (const db of ['pa', 'pm', 'po', 'life', 'ini', 'invocation', 'strong', 'intel', 'agi', 'chance', 'vitality', 'sagesse', 'ca', 'dodge_pa', 'dodge_pm', 'fuite', 'tacle', 'touch']) {
                const v = c[db];
                if (v != null && String(v) !== '') filterParts.push(String(v));
            }
            for (const el of elements) {
                ['res_fixe_' + el, 'res_' + el, 'do_fixe_' + el].forEach((db) => {
                    const v = c[db];
                    if (v != null && String(v) !== '') filterParts.push(String(v));
                });
            }
        }
        const filterValue = filterParts.join(' ');
        return {
            type: 'chips',
            value: '',
            params: {
                component: CharacteristicsCard,
                componentProps: { entity: creatureData, groups, dense: true, passValue: false },
                passValue: false,
                sortValue: 0,
                searchValue: filterValue,
                filterValue,
            },
        };
    }

    /** @private */
    _toSummaryResistanceCell(_options) {
        return this._toSummaryGroupCell(_options, 'Résistances', ['res_fixe_neutre', 'res_neutre', 'res_fixe_terre', 'res_terre', 'res_fixe_feu', 'res_feu', 'res_fixe_air', 'res_air', 'res_fixe_eau', 'res_eau']);
    }

    /** @private */
    _toSummaryDamageCell(_options) {
        return this._toSummaryGroupCell(_options, 'Dommages', ['touch', 'do_fixe_neutre', 'do_fixe_terre', 'do_fixe_feu', 'do_fixe_air', 'do_fixe_eau']);
    }

    /** @private */
    _toSummaryStatsCell(_options) {
        return this._toSummaryGroupCell(_options, 'Stats', ['strong', 'intel', 'agi', 'chance', 'vitality', 'sagesse']);
    }

    /** @private */
    _toSummaryCombatCell(_options) {
        return this._toSummaryGroupCell(_options, 'Combat', ['pa', 'pm', 'po', 'life', 'ini', 'invocation']);
    }

    /** @private */
    _toSummaryControlCell(_options) {
        return this._toSummaryGroupCell(_options, 'Contrôle', ['ca', 'dodge_pa', 'dodge_pm', 'fuite', 'tacle']);
    }

    /**
     * Colonne résumée : relations gameplay du PNJ.
     * @private
     */
    _toNpcSummaryRelationsCell(_options) {
        const items = [
            {
                icon: 'fa-solid fa-layer-group',
                value: this.panopliesCount > 0 ? `${this.panopliesCount} panoplie${this.panopliesCount > 1 ? 's' : ''}` : null,
                tooltip: this.panopliesCount > 0 ? `Panoplies: ${this.panopliesCount}` : '',
            },
            {
                icon: 'fa-solid fa-flag',
                value: this.campaignsCount > 0 ? `${this.campaignsCount} campagne${this.campaignsCount > 1 ? 's' : ''}` : null,
                tooltip: this.campaignsCount > 0 ? `Campagnes: ${this.campaignsCount}` : '',
            },
            {
                icon: 'fa-solid fa-scroll',
                value: this.scenariosCount > 0 ? `${this.scenariosCount} scénario${this.scenariosCount > 1 ? 's' : ''}` : null,
                tooltip: this.scenariosCount > 0 ? `Scénarios: ${this.scenariosCount}` : '',
            },
            {
                icon: 'fa-solid fa-store',
                value: this.hasShop ? 'Boutique' : null,
                tooltip: this.hasShop ? 'Ce PNJ possède une boutique' : '',
            },
        ].filter((it) => it.value !== null);

        const searchValue = items.map((it) => String(it.value)).join(' ');

        return {
            type: 'chips',
            value: '',
            params: {
                items,
                sortValue:
                    this.panopliesCount +
                    this.campaignsCount +
                    this.scenariosCount +
                    (this.hasShop ? 1 : 0),
                searchValue,
                filterValue: searchValue,
            },
        };
    }

    /**
     * Génère une cellule résumé avec CharacteristicsCard pour un groupe (Combat, Résistances, etc.).
     * @private
     */
    _toSummaryGroupCell(_options, groupTitle, dbColumnsForFilter) {
        const ctx = _options?.ctx ?? _options?.context ?? null;
        const byDb = ctx?.characteristics?.creature?.byDbColumn ?? {};
        const creatureData = this._getCreatureData();
        const allGroups = buildCreatureCharacteristicGroups(creatureData, byDb);
        const groups = allGroups.filter((g) => g.title === groupTitle);
        const c = creatureData;
        const filterParts = [];
        if (c && typeof c === 'object') {
            for (const db of dbColumnsForFilter) {
                const v = c[db];
                if (v != null && String(v) !== '') filterParts.push(String(v));
            }
        }
        const filterValue = filterParts.join(' ');
        return {
            type: 'chips',
            value: '',
            params: {
                component: CharacteristicsCard,
                componentProps: { entity: creatureData, groups, dense: true, passValue: false },
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
    _toCreatureNameCell(format, size, _options) {
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
        const truncate = format.truncate || (size === 'xs' || size === 'sm' ? 20 : null);

        return {
            type: 'text',
            value: name,
            params: {
                tooltip: name === '-' ? '' : name,
                truncate,
                searchValue: name === '-' ? '' : name,
                sortValue: name,
            },
        };
    }

    /**
     * Génère une cellule pour la breed (affichée « Classe »)
     * @private
     */
    _toBreedCell(format, size, options) {
        const breed = this.breed;

        if (!breed) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                },
            };
        }

        const breedName = breed.name || breed.label || '-';

        return {
            type: 'text',
            value: breedName,
            params: {
                tooltip: breedName === '-' ? '' : breedName,
                sortValue: breedName,
                searchValue: breedName,
            },
        };
    }

    /**
     * Génère une cellule pour la spécialisation
     * @private
     */
    _toSpecializationCell(format, size, options) {
        const specialization = this.specialization;
        
        if (!specialization) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                },
            };
        }

        const specializationName = specialization.name || specialization.label || '-';

        return {
            type: 'text',
            value: specializationName,
            params: {
                tooltip: specializationName === '-' ? '' : specializationName,
                sortValue: specializationName,
                searchValue: specializationName,
            },
        };
    }

    /**
     * Génère une cellule pour l'histoire
     * @private
     */
    _toStoryCell(format, size, options) {
        const story = this.story || '-';
        
        return {
            type: 'text',
            value: story,
            params: {
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 30 : (size === 'md' ? 50 : null)),
                searchValue: story === '-' ? '' : story,
                sortValue: story,
            },
        };
    }

    /**
     * Génère une cellule pour l'historique
     * @private
     */
    _toHistoricalCell(format, size, options) {
        const historical = this.historical || '-';
        
        return {
            type: 'text',
            value: historical,
            params: {
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 30 : (size === 'md' ? 50 : null)),
                searchValue: historical === '-' ? '' : historical,
                sortValue: historical,
            },
        };
    }

    /**
     * Génère une cellule pour l'âge
     * @private
     */
    _toAgeCell(format, size, options) {
        const age = this.age || '-';
        
        return {
            type: 'text',
            value: age,
            params: {
                sortValue: age === '-' ? '' : age,
                searchValue: age === '-' ? '' : age,
            },
        };
    }

    /**
     * Génère une cellule pour la taille
     * @private
     */
    _toSizeCell(format, size, options) {
        const sizeValue = this.size || '-';
        
        return {
            type: 'text',
            value: sizeValue,
            params: {
                sortValue: sizeValue === '-' ? '' : sizeValue,
                searchValue: sizeValue === '-' ? '' : sizeValue,
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
            story: this.story,
            historical: this.historical,
            age: this.age,
            size: this.size,
            breed_id: this.breedId,
            specialization_id: this.specializationId
        };
    }
}

export default Npc;
