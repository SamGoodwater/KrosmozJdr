/**
 * Modèle Item pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données d'item côté frontend.
 * 
 * @example
 * const item = new Item(props.item);
 * console.log(item.name); // Accès normalisé
 * console.log(item.canUpdate); // Permissions
 */
import { BaseModel } from '../BaseModel';

export class Item extends BaseModel {
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

    get level() {
        return this._data.level || null;
    }

    get description() {
        return this._data.description || '';
    }

    get effect() {
        return this._data.effect || null;
    }

    get bonus() {
        return this._data.bonus || null;
    }

    get recipe() {
        return this._data.recipe || null;
    }

    get price() {
        return this._data.price || null;
    }

    get rarity() {
        return this._data.rarity || null;
    }

    get dofusVersion() {
        return this._data.dofus_version || null;
    }

    get image() {
        return this._data.image || '';
    }

    get autoUpdate() {
        return this._data.auto_update ?? false;
    }

    get itemTypeId() {
        return this._data.item_type_id || null;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get createdBy() {
        return this._data.createdBy || null;
    }

    get itemType() {
        return this._data.itemType || null;
    }

    get resources() {
        return this._data.resources || [];
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

    get shops() {
        return this._data.shops || [];
    }

    get resourcesCount() {
        return Number(this._data.resources_count ?? this.resources.length ?? 0);
    }

    get panopliesCount() {
        return Number(this._data.panoplies_count ?? this.panoplies.length ?? 0);
    }

    get shopsCount() {
        return Number(this._data.shops_count ?? this.shops.length ?? 0);
    }

    get campaignsCount() {
        return Number(this._data.campaigns_count ?? this.campaigns.length ?? 0);
    }

    get scenariosCount() {
        return Number(this._data.scenarios_count ?? this.scenarios.length ?? 0);
    }

    /**
     * Retourne les métadonnées des caractéristiques item indexées par db_column.
     * @private
     */
    _getItemCharacteristicsByColumn(options = {}) {
        return options?.ctx?.characteristics?.item?.byDbColumn || {};
    }

    /**
     * Tente de parser une valeur JSON (objet/array), sinon null.
     * @private
     */
    _parseJsonPayload(value) {
        if (value && typeof value === 'object') return value;
        if (typeof value !== 'string') return null;
        const trimmed = value.trim();
        if (!trimmed) return null;
        if (!(trimmed.startsWith('{') || trimmed.startsWith('['))) return null;
        try {
            return JSON.parse(trimmed);
        } catch {
            return null;
        }
    }

    /**
     * Extrait des entrées clé/valeur depuis un payload d'effet (objet ou tableau).
     * @private
     */
    _extractEffectEntries(payload) {
        if (!payload) return [];

        if (!Array.isArray(payload) && typeof payload === 'object') {
            return Object.entries(payload).map(([key, value]) => ({ key: String(key), value }));
        }

        if (Array.isArray(payload)) {
            return payload
                .map((row) => {
                    if (!row || typeof row !== 'object') return null;
                    const key = row.db_column ?? row.key ?? row.characteristic ?? row.stat ?? row.name ?? row.label ?? null;
                    const value = row.value ?? row.amount ?? row.val ?? row.to ?? row.max ?? row.min ?? null;
                    if (!key || value === null || typeof value === 'undefined') return null;
                    return { key: String(key), value };
                })
                .filter(Boolean);
        }

        return [];
    }

    /**
     * Construit un rendu chips (icône/couleur) depuis effect/bonus si possible.
     * @private
     */
    _buildEffectChips(options = {}) {
        const byDb = this._getItemCharacteristicsByColumn(options);
        const effectPayload = this._parseJsonPayload(this.effect);
        const bonusPayload = this._parseJsonPayload(this.bonus);
        const rawEffectText = this.effect ? String(this.effect).trim() : '';
        const rawBonusText = this.bonus ? String(this.bonus).trim() : '';

        const entries = [
            ...this._extractEffectEntries(effectPayload),
            ...this._extractEffectEntries(bonusPayload),
        ];

        if (entries.length === 0) {
            return null;
        }

        const items = entries.map(({ key, value }) => {
            const def = byDb?.[key] || byDb?.[key.replace(/_object$/, '')];
            const renderedValue = String(value);
            const label = def?.short_name || def?.name || key;
            return {
                icon: def?.icon || 'fa-solid fa-circle-info',
                color: def?.color || null,
                value: renderedValue,
                tooltip: `${label}: ${renderedValue}`,
            };
        });

        const searchValue = items.map((it) => `${it.tooltip} ${it.value}`).join(' ').trim();
        const filterValue = [rawEffectText, rawBonusText, searchValue].filter(Boolean).join(' ').trim();

        return {
            type: 'chips',
            value: '',
            params: {
                items,
                sortValue: filterValue,
                searchValue: filterValue,
                filterValue,
            },
        };
    }

    // ============================================
    // FORMATAGE DES CELLULES (surcharge pour champs spécifiques)
    // ============================================

    /**
     * Génère une cellule pour un champ (surcharge pour gérer les champs spécifiques à Item)
     * @param {string} fieldKey - Clé du champ
     * @param {Object} [options={}] - Options (size, context, config, ctx)
     * @returns {Object|null} Cell object ou null si valeur invalide
     */
    toCell(fieldKey, options = {}) {
        // D'abord, essayer la méthode de base (gère les formatters automatiquement)
        const baseCell = super.toCell(fieldKey, options);
        const overrideFields = new Set(['effect']);
        
        // Si la méthode de base a trouvé quelque chose (formatter ou valeur par défaut valide), l'utiliser
        if (!overrideFields.has(fieldKey) && baseCell && (baseCell.type !== 'text' || (baseCell.value && baseCell.value !== '-'))) {
            return baseCell;
        }

        // Sinon, gérer les champs spécifiques à Item
        const { size = 'md', format = {} } = options;
        
        switch (fieldKey) {
            case 'name':
                return this._toNameCell(format, size, options);
            case 'description':
                return this._toDescriptionCell(format, size, options);
            case 'effect':
                return this._toEffectCell(format, size, options);
            case 'bonus':
                return this._toBonusCell(format, size, options);
            case 'recipe':
                return this._toRecipeCell(format, size, options);
            case 'image':
                return this._toImageCell(format, size, options);
            case 'item_type':
            case 'itemType':
                return this._toItemTypeCell(format, size, options);
            case 'item_summary_meta':
                return this._toItemSummaryMetaCell(format, size, options);
            case 'created_by':
            case 'createdBy':
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
        const href = options.href || `/items/${this.id}`;
        
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
     * Génère une cellule pour la description (texte tronqué)
     * @private
     */
    _toDescriptionCell(format, size, options) {
        const description = this.description || '';
        const maxLength = format.truncate || (size === 'xs' || size === 'sm' ? 30 : 50);
        const truncated = description.length > maxLength 
            ? description.slice(0, maxLength - 1) + '…'
            : description;
        
        return {
            type: 'text',
            value: truncated || '-',
            params: {
                tooltip: description || '',
                sortValue: description,
                searchValue: description,
            },
        };
    }

    /**
     * Génère une cellule pour l'effet
     * @private
     */
    _toEffectCell(format, size, options) {
        const chipsCell = this._buildEffectChips(options);
        if (chipsCell) return chipsCell;

        const effect = this.effect || '';
        const maxLength = format.truncate || (size === 'xs' || size === 'sm' ? 20 : 40);
        const truncated = effect.length > maxLength 
            ? effect.slice(0, maxLength - 1) + '…'
            : effect;
        
        return {
            type: 'text',
            value: truncated || '-',
            params: {
                tooltip: effect || '',
                sortValue: effect,
                searchValue: effect,
            },
        };
    }

    /**
     * Génère une cellule pour le bonus
     * @private
     */
    _toBonusCell(format, size, options) {
        const bonus = this.bonus || '';
        const maxLength = format.truncate || (size === 'xs' || size === 'sm' ? 20 : 40);
        const truncated = bonus.length > maxLength 
            ? bonus.slice(0, maxLength - 1) + '…'
            : bonus;
        
        return {
            type: 'text',
            value: truncated || '-',
            params: {
                tooltip: bonus || '',
                sortValue: bonus,
                searchValue: bonus,
            },
        };
    }

    /**
     * Génère une cellule pour la recette
     * @private
     */
    _toRecipeCell(format, size, options) {
        const recipe = this.recipe || '';
        const maxLength = format.truncate || (size === 'xs' || size === 'sm' ? 20 : 40);
        const truncated = recipe.length > maxLength 
            ? recipe.slice(0, maxLength - 1) + '…'
            : recipe;
        
        return {
            type: 'text',
            value: truncated || '-',
            params: {
                tooltip: recipe || '',
                sortValue: recipe,
                searchValue: recipe,
            },
        };
    }

    /**
     * Génère une cellule pour l'image (miniature)
     * @private
     */
    _toImageCell(format, size, options) {
        const imageUrl = this.image || '';
        
        if (!imageUrl) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                },
            };
        }

        const imageSize = size === 'xs' || size === 'sm' ? 32 : 48;
        
        return {
            type: 'image',
            value: imageUrl,
            params: {
                alt: this.name || 'Item image',
                width: imageSize,
                height: imageSize,
                sortValue: imageUrl,
                searchValue: imageUrl,
            },
        };
    }

    /**
     * Génère une cellule pour le type d'item
     * @private
     */
    _toItemTypeCell(format, size, options) {
        const itemType = this.itemType;
        
        if (!itemType) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                },
            };
        }

        const typeName = itemType.name || itemType.label || '-';

        return {
            type: 'text',
            value: typeName,
            params: {
                tooltip: typeName === '-' ? '' : typeName,
                sortValue: typeName,
                searchValue: typeName,
            },
        };
    }

    /**
     * Génère une cellule résumé (type chips) pour les métadonnées clés de l'item.
     * @private
     */
    _toItemSummaryMetaCell(format, size, options) {
        const itemTypeName = this.itemType?.name || this.itemType?.label || null;
        const rarityMap = {
            0: 'Commun',
            1: 'Peu commun',
            2: 'Rare',
            3: 'Très rare',
            4: 'Légendaire',
            5: 'Unique',
        };
        const rarityValue = this.rarity;
        const rarityLabel = Number.isFinite(Number(rarityValue)) ? (rarityMap[Number(rarityValue)] || String(rarityValue)) : null;
        const levelValue = this.level != null ? String(this.level) : null;
        const priceValue = this.price != null && String(this.price) !== '' ? String(this.price) : null;
        const versionValue = this.dofusVersion ? String(this.dofusVersion) : null;
        const dofusdbValue = this.dofusdbId ? `#${this.dofusdbId}` : null;
        const resourcesValue = this.resourcesCount > 0 ? `${this.resourcesCount} ingr.` : null;
        const panopliesValue = this.panopliesCount > 0 ? `${this.panopliesCount} pano` : null;
        const shopsValue = this.shopsCount > 0 ? `${this.shopsCount} boutique` : null;
        const campaignsValue = this.campaignsCount > 0 ? `${this.campaignsCount} campagne` : null;
        const scenariosValue = this.scenariosCount > 0 ? `${this.scenariosCount} scénario` : null;

        const items = [
            { icon: 'fa-solid fa-tags', value: itemTypeName, tooltip: itemTypeName ? `Type: ${itemTypeName}` : '' },
            { icon: 'fa-solid fa-level-up-alt', value: levelValue, tooltip: levelValue ? `Niveau: ${levelValue}` : '' },
            { icon: 'fa-solid fa-star', value: rarityLabel, tooltip: rarityLabel ? `Rareté: ${rarityLabel}` : '' },
            { icon: 'fa-solid fa-coins', value: priceValue, tooltip: priceValue ? `Prix: ${priceValue}` : '' },
            { icon: 'fa-solid fa-code-branch', value: versionValue, tooltip: versionValue ? `Version: ${versionValue}` : '' },
            { icon: 'fa-solid fa-up-right-from-square', value: dofusdbValue, tooltip: dofusdbValue ? `DofusDB: ${dofusdbValue}` : '' },
            { icon: 'fa-solid fa-flask', value: resourcesValue, tooltip: resourcesValue ? `Ressources liées: ${this.resourcesCount}` : '' },
            { icon: 'fa-solid fa-layer-group', value: panopliesValue, tooltip: panopliesValue ? `Panoplies liées: ${this.panopliesCount}` : '' },
            { icon: 'fa-solid fa-store', value: shopsValue, tooltip: shopsValue ? `Boutiques liées: ${this.shopsCount}` : '' },
            { icon: 'fa-solid fa-flag', value: campaignsValue, tooltip: campaignsValue ? `Campagnes liées: ${this.campaignsCount}` : '' },
            { icon: 'fa-solid fa-scroll', value: scenariosValue, tooltip: scenariosValue ? `Scénarios liés: ${this.scenariosCount}` : '' },
        ].filter((it) => it.value !== null && it.value !== undefined && String(it.value) !== '');

        const searchValue = items.map((it) => String(it.value)).join(' ');

        return {
            type: 'chips',
            value: '',
            params: {
                items,
                sortValue: Number(this.level) || 0,
                searchValue,
                filterValue: searchValue,
            },
        };
    }

    /**
     * Génère une cellule pour le créateur
     * @private
     */
    _toCreatedByCell(format, size, options) {
        const createdBy = this.createdBy;
        
        if (!createdBy) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                },
            };
        }

        const userName = createdBy.name || createdBy.email || '-';

        return {
            type: 'text',
            value: userName,
            params: {
                tooltip: userName === '-' ? '' : userName,
                sortValue: userName,
                searchValue: userName,
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
            official_id: this.officialId,
            dofusdb_id: this.dofusdbId,
            name: this.name,
            level: this.level,
            description: this.description,
            effect: this.effect,
            bonus: this.bonus,
            recipe: this.recipe,
            price: this.price,
            rarity: this.rarity,
            dofus_version: this.dofusVersion,
            state: this.state,
            read_level: this.readLevel,
            write_level: this.writeLevel,
            image: this.image,
            auto_update: this.autoUpdate,
            item_type_id: this.itemTypeId
        };
    }
}

export default Item;
