/**
 * Modèle Shop pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de shop côté frontend.
 * 
 * @example
 * const shop = new Shop(props.shop);
 * console.log(shop.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';

export class Shop extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get name() {
        return this._data.name || '';
    }

    get description() {
        return this._data.description || '';
    }

    get location() {
        return this._data.location || null;
    }

    get price() {
        return this._data.price || null;
    }

    get image() {
        return this._data.image || '';
    }

    get npcId() {
        return this._data.npc_id || null;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get createdBy() {
        return this._data.createdBy || null;
    }

    get npc() {
        return this._data.npc || null;
    }

    get items() {
        return this._data.items || [];
    }

    get panoplies() {
        return this._data.panoplies || [];
    }

    get consumables() {
        return this._data.consumables || [];
    }

    get resources() {
        return this._data.resources || [];
    }

    get scenarios() {
        return this._data.scenarios || [];
    }

    get campaigns() {
        return this._data.campaigns || [];
    }

    // ============================================
    // FORMATAGE DES CELLULES (surcharge pour champs spécifiques)
    // ============================================

    /**
     * Génère une cellule pour un champ (surcharge pour gérer les champs spécifiques à Shop)
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

        // Sinon, gérer les champs spécifiques à Shop
        const { size = 'md', format = {} } = options;
        
        switch (fieldKey) {
            case 'name':
                return this._toNameCell(format, size, options);
            case 'description':
                return this._toDescriptionCell(format, size, options);
            case 'location':
                return this._toLocationCell(format, size, options);
            case 'price':
                return this._toPriceCell(format, size, options);
            case 'image':
                return this._toImageCell(format, size, options);
            case 'npc_id':
                return this._toNpcIdCell(format, size, options);
            case 'npc_name':
                return this._toNpcNameCell(format, size, options);
            case 'items_count':
                return this._toItemsCountCell(format, size, options);
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
        const href = options.href || `/shops/${this.id}`;
        
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
     * Génère une cellule pour location
     * @private
     */
    _toLocationCell(format, size, options) {
        const location = this.location || '-';
        
        return {
            type: 'text',
            value: location,
            params: {
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 20 : null),
                sortValue: location === '-' ? '' : location,
                searchValue: location === '-' ? '' : location,
            },
        };
    }

    /**
     * Génère une cellule pour price
     * @private
     */
    _toPriceCell(format, size, options) {
        const price = this.price ?? null;
        const value = price !== null && price !== undefined ? String(price) : '-';
        
        return {
            type: 'text',
            value,
            params: {
                sortValue: price !== null && price !== undefined ? Number(price) : 0,
                searchValue: value === '-' ? '' : value,
            },
        };
    }

    /**
     * Génère une cellule pour image
     * @private
     */
    _toImageCell(format, size, options) {
        // Utiliser le ImageFormatter via la méthode de base
        return super.toCell('image', options);
    }

    /**
     * Génère une cellule pour npc_id
     * @private
     */
    _toNpcIdCell(format, size, options) {
        const npcId = this.npcId || '-';
        
        return {
            type: 'text',
            value: npcId,
            params: {
                sortValue: npcId === '-' ? '' : npcId,
                searchValue: npcId === '-' ? '' : npcId,
            },
        };
    }

    /**
     * Génère une cellule pour npc_name
     * @private
     */
    _toNpcNameCell(format, size, options) {
        const npc = this.npc;
        const npcName = npc?.name || '-';
        
        return {
            type: 'text',
            value: npcName,
            params: {
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 20 : null),
                sortValue: npcName === '-' ? '' : npcName,
                searchValue: npcName === '-' ? '' : npcName,
            },
        };
    }

    /**
     * Génère une cellule pour items_count
     * @private
     */
    _toItemsCountCell(format, size, options) {
        const itemsCount = this.items?.length || this._data.items_count || 0;
        
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
            name: this.name,
            description: this.description,
            location: this.location,
            price: this.price,
            state: this.state,
            read_level: this.readLevel,
            write_level: this.writeLevel,
            image: this.image,
            npc_id: this.npcId
        };
    }
}

export default Shop;
