/**
 * Modèle Consumable pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de consumable côté frontend.
 * 
 * @example
 * const consumable = new Consumable(props.consumable);
 * console.log(consumable.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';

export class Consumable extends BaseModel {
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

    get level() {
        return this._data.level || null;
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

    get consumableTypeId() {
        return this._data.consumable_type_id || null;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get createdBy() {
        return this._data.createdBy || null;
    }

    get consumableType() {
        return this._data.consumableType || [];
    }

    get resources() {
        return this._data.resources || [];
    }

    get creatures() {
        return this._data.creatures || [];
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

    /**
     * Retourne les métadonnées des caractéristiques consumable indexées par db_column.
     * @private
     */
    _getConsumableCharacteristicsByColumn(options = {}) {
        return options?.ctx?.characteristics?.consumable?.byDbColumn || {};
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
     * Construit un rendu chips (icône/couleur) depuis effect si possible.
     * Fallback texte si ce n'est pas une structure de caractéristiques.
     * @private
     */
    _buildEffectChips(options = {}) {
        const byDb = this._getConsumableCharacteristicsByColumn(options);
        const effectPayload = this._parseJsonPayload(this.effect);
        const rawEffectText = this.effect ? String(this.effect).trim() : '';
        const entries = this._extractEffectEntries(effectPayload);
        if (entries.length === 0) return null;

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
        const filterValue = [rawEffectText, searchValue].filter(Boolean).join(' ').trim();

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
     * Génère une cellule pour un champ (surcharge pour gérer les champs spécifiques à Consumable)
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

        // Sinon, gérer les champs spécifiques à Consumable
        const { size = 'md', format = {} } = options;
        
        switch (fieldKey) {
            case 'name':
                return this._toNameCell(format, size, options);
            case 'description':
                return this._toDescriptionCell(format, size, options);
            case 'effect':
                return this._toEffectCell(format, size, options);
            case 'recipe':
                return this._toRecipeCell(format, size, options);
            case 'image':
                return this._toImageCell(format, size, options);
            case 'consumable_type':
            case 'consumableType':
                return this._toConsumableTypeCell(format, size, options);
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
        const href = options.href || `/consumables/${this.id}`;
        
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
    _toDescriptionCell(format, size, _options) {
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
     * Génère une cellule pour la recette
     * @private
     */
    _toRecipeCell(format, size, _options) {
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
    _toImageCell(_format, size, _options) {
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
                alt: this.name || 'Consumable image',
                width: imageSize,
                height: imageSize,
                sortValue: imageUrl,
                searchValue: imageUrl,
            },
        };
    }

    /**
     * Génère une cellule pour le type de consommable
     * @private
     */
    _toConsumableTypeCell(_format, _size, _options) {
        const consumableType = this.consumableType;
        
        if (!consumableType) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                },
            };
        }

        const typeName = consumableType.name || consumableType.label || '-';

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
     * Génère une cellule pour le créateur
     * @private
     */
    _toCreatedByCell(_format, _size, _options) {
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
            description: this.description,
            effect: this.effect,
            level: this.level,
            recipe: this.recipe,
            price: this.price,
            rarity: this.rarity,
            state: this.state,
            read_level: this.readLevel,
            write_level: this.writeLevel,
            dofus_version: this.dofusVersion,
            image: this.image,
            auto_update: this.autoUpdate,
            consumable_type_id: this.consumableTypeId
        };
    }
}

export default Consumable;
