/**
 * Modèle Panoply pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de panoply côté frontend.
 * 
 * @example
 * const panoply = new Panoply(props.panoply);
 * console.log(panoply.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';
import { buildCharacteristicEffectCell } from '@/Composables/entity/useCharacteristicEffectFormatter';

export class Panoply extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get dofusdbId() {
        return this._data.dofusdb_id || null;
    }

    get name() {
        return this._data.name || '';
    }

    get description() {
        return this._data.description || '';
    }

    get bonus() {
        return this._data.bonus || null;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get createdBy() {
        return this._data.createdBy || null;
    }

    get items() {
        return this._data.items || [];
    }

    get npcs() {
        return this._data.npcs || [];
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

    get itemsCount() {
        return Number(this._data.items_count ?? this.items.length ?? 0);
    }

    get npcsCount() {
        return Number(this._data.npcs_count ?? this.npcs.length ?? 0);
    }

    get campaignsCount() {
        return Number(this._data.campaigns_count ?? this.campaigns.length ?? 0);
    }

    get scenariosCount() {
        return Number(this._data.scenarios_count ?? this.scenarios.length ?? 0);
    }

    get shopsCount() {
        return Number(this._data.shops_count ?? this.shops.length ?? 0);
    }

    // ============================================
    // FORMATAGE DES CELLULES (surcharge pour champs spécifiques)
    // ============================================

    /**
     * Génère une cellule pour un champ (surcharge pour gérer les champs spécifiques à Panoply)
     * @param {string} fieldKey - Clé du champ
     * @param {Object} [options={}] - Options (size, context, config, ctx)
     * @returns {Object|null} Cell object ou null si valeur invalide
     */
    toCell(fieldKey, options = {}) {
        // D'abord, essayer la méthode de base (gère les formatters automatiquement)
        const baseCell = super.toCell(fieldKey, options);
        const overrideFields = new Set(['bonus']);
        
        // Si la méthode de base a trouvé quelque chose (formatter ou valeur par défaut valide), l'utiliser
        if (!overrideFields.has(fieldKey) && baseCell && (baseCell.type !== 'text' || (baseCell.value && baseCell.value !== '-'))) {
            return baseCell;
        }

        // Sinon, gérer les champs spécifiques à Panoply
        const { size = 'md', format = {} } = options;
        
        switch (fieldKey) {
            case 'name':
                return this._toNameCell(format, size, options);
            case 'description':
                return this._toDescriptionCell(format, size, options);
            case 'bonus':
                return this._toBonusCell(format, size, options);
            case 'dofusdb_id':
                return this._toDofusdbIdCell(format, size, options);
            case 'items_count':
                return this._toItemsCountCell(format, size, options);
            case 'panoply_summary_relations':
                return this._toPanoplySummaryRelationsCell(format, size, options);
            case 'created_by':
                return this._toCreatedByCell(format, size, options);
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
     * Génère une cellule pour le nom (lien vers la page de détail)
     * @private
     */
    _toNameCell(format, size, options) {
        const name = this.name || '-';
        const href = options.href || `/panoplies/${this.id}`;
        
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
     * Génère une cellule pour la description
     * @private
     */
    _toDescriptionCell(format, size, options) {
        const description = this.description || '-';
        
        return {
            type: 'text',
            value: description,
            params: {
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 30 : (size === 'md' ? 50 : null)),
                searchValue: description === '-' ? '' : description,
                sortValue: description,
            },
        };
    }

    /**
     * Génère une cellule pour le bonus
     * @private
     */
    _toBonusCell(format, size, options) {
        return buildCharacteristicEffectCell({
            rawValues: [this.bonus],
            options,
            sourceGroups: ['panoply', 'item'],
            format,
            size,
            chipsLayout: { maxRows: 3 },
        });
    }

    /**
     * Génère une cellule pour dofusdb_id
     * @private
     */
    _toDofusdbIdCell(format, size, options) {
        const dofusdbId = this.dofusdbId || '-';
        
        return {
            type: 'text',
            value: dofusdbId,
            params: {
                sortValue: dofusdbId === '-' ? '' : dofusdbId,
                searchValue: dofusdbId === '-' ? '' : dofusdbId,
            },
        };
    }

    /**
     * Génère une cellule pour items_count
     * @private
     */
    _toItemsCountCell(format, size, options) {
        const itemsCount = this.itemsCount;
        
        return {
            type: 'text',
            value: String(itemsCount),
            params: {
                sortValue: Number(itemsCount),
                searchValue: String(itemsCount),
            },
        };
    }

    /**
     * Génère une cellule résumé (chips) des relations métier de la panoplie.
     * @private
     */
    _toPanoplySummaryRelationsCell(_format, _size, _options) {
        const items = [
            {
                icon: 'fa-solid fa-sword',
                value: this.itemsCount > 0 ? `${this.itemsCount} équipement${this.itemsCount > 1 ? 's' : ''}` : null,
                tooltip: this.itemsCount > 0 ? `Équipements: ${this.itemsCount}` : '',
            },
            {
                icon: 'fa-solid fa-user',
                value: this.npcsCount > 0 ? `${this.npcsCount} PNJ` : null,
                tooltip: this.npcsCount > 0 ? `PNJ: ${this.npcsCount}` : '',
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
                value: this.shopsCount > 0 ? `${this.shopsCount} boutique${this.shopsCount > 1 ? 's' : ''}` : null,
                tooltip: this.shopsCount > 0 ? `Boutiques: ${this.shopsCount}` : '',
            },
        ].filter((it) => it.value !== null);

        const searchValue = items.map((it) => String(it.value)).join(' ');

        return {
            type: 'chips',
            value: '',
            params: {
                items,
                sortValue: items.length,
                searchValue,
                filterValue: searchValue,
            },
        };
    }

    /**
     * Génère une cellule pour created_by
     * @private
     */
    _toCreatedByCell(format, size, options) {
        // Utiliser le UserFormatter via la méthode de base
        return super.toCell('created_by', options);
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
            dofusdb_id: this.dofusdbId,
            name: this.name,
            description: this.description,
            bonus: this.bonus,
            state: this.state,
            read_level: this.readLevel,
            write_level: this.writeLevel
        };
    }
}

export default Panoply;
