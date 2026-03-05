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
import { buildCharacteristicEffectCell } from '@/Composables/entity/useCharacteristicEffectFormatter';

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

    get resourcesCount() {
        return Number(this._data.resources_count ?? this.resources.length ?? 0);
    }

    get creaturesCount() {
        return Number(this._data.creatures_count ?? this.creatures.length ?? 0);
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
            case 'consumable_summary_relations':
                return this._toConsumableSummaryRelationsCell(format, size, options);
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
        return buildCharacteristicEffectCell({
            rawValues: [this.effect],
            options,
            sourceGroups: ['consumable', 'item'],
            format,
            size,
            chipsLayout: { maxRows: 3 },
        });
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
     * Génère une cellule résumé (chips) des relations métier.
     * @private
     */
    _toConsumableSummaryRelationsCell(_format, _size, _options) {
        const items = [
            {
                icon: 'fa-solid fa-flask',
                value: this.resourcesCount > 0 ? `${this.resourcesCount} ressource${this.resourcesCount > 1 ? 's' : ''}` : null,
                tooltip: this.resourcesCount > 0 ? `Ressources de recette: ${this.resourcesCount}` : '',
            },
            {
                icon: 'fa-solid fa-dragon',
                value: this.creaturesCount > 0 ? `${this.creaturesCount} créature${this.creaturesCount > 1 ? 's' : ''}` : null,
                tooltip: this.creaturesCount > 0 ? `Créatures: ${this.creaturesCount}` : '',
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
                sortValue:
                    this.resourcesCount +
                    this.creaturesCount +
                    this.campaignsCount +
                    this.scenariosCount +
                    this.shopsCount,
                searchValue,
                filterValue: searchValue,
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
