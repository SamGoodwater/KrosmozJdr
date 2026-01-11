/**
 * Modèle Resource pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de resource côté frontend.
 * 
 * @example
 * const resource = new Resource(props.resource);
 * console.log(resource.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';

export class Resource extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get dofusdbId() {
        return this._data.dofusdb_id || null;
    }

    get officialId() {
        return this._data.official_id || null;
    }

    get name() {
        return this._data.name || '';
    }

    get description() {
        return this._data.description || '';
    }

    get level() {
        return this._data.level ?? null;
    }

    get price() {
        return this._data.price ?? null;
    }

    get weight() {
        return this._data.weight ?? null;
    }

    get rarity() {
        // La colonne est NOT NULL en base : on garantit un int.
        return this._data.rarity ?? 0;
    }

    get dofusVersion() {
        return this._data.dofus_version ?? null;
    }

    get usable() {
        return Boolean(this._data.usable);
    }

    get image() {
        return this._data.image || '';
    }

    get autoUpdate() {
        return Boolean(this._data.auto_update);
    }

    get resourceTypeId() {
        return this._data.resource_type_id || null;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get createdBy() {
        return this._data.createdBy || null;
    }

    get resourceType() {
        return this._data.resourceType || null;
    }

    get consumables() {
        return this._data.consumables || [];
    }

    get creatures() {
        return this._data.creatures || [];
    }

    get items() {
        return this._data.items || [];
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

    // ============================================
    // FORMATAGE DES CELLULES (surcharge pour champs spécifiques)
    // ============================================

    /**
     * Génère une cellule pour un champ (surcharge pour gérer les champs spécifiques à Resource)
     * @param {string} fieldKey - Clé du champ
     * @param {Object} [options={}] - Options (size, context, config, ctx)
     * @returns {Object|null} Cell object ou null si valeur invalide
     */
    toCell(fieldKey, options = {}) {
        // Gérer d'abord les champs spécifiques à Resource qui n'existent pas directement dans _data
        // (comme resource_type qui est une relation)
        const { size = 'md', format = {} } = options;
        
        switch (fieldKey) {
            case 'resource_type':
            case 'resourceType':
                return this._toResourceTypeCell(format, size, options);
            case 'name':
                return this._toNameCell(format, size, options);
            case 'description':
                return this._toDescriptionCell(format, size, options);
            case 'image':
                return this._toImageCell(format, size, options);
            case 'created_by':
            case 'createdBy':
                return this._toCreatedByCell(format, size, options);
            case 'created_at':
                return this._toCreatedAtCell(format, size, options);
            case 'updated_at':
                return this._toUpdatedAtCell(format, size, options);
            default:
                // Pour les autres champs, essayer la méthode de base (gère les formatters automatiquement)
                // La méthode de base vérifie si le champ existe dans _data, donc on peut l'appeler directement
                const baseCell = super.toCell(fieldKey, options);
                
                // Si la méthode de base a retourné une cellule valide, l'utiliser
                if (baseCell) {
                    return baseCell;
                }
                
                // Si le champ existe dans _data mais n'a pas de formatter, créer une cellule par défaut
                if (fieldKey in this._data) {
                    const value = this._data[fieldKey];
                    return {
                        type: 'text',
                        value: value !== null && value !== undefined ? String(value) : '-',
                        params: {
                            sortValue: value,
                            searchValue: value !== null && value !== undefined ? String(value) : '',
                        },
                    };
                }
                
                // Fallback final si le champ n'existe pas du tout
                return { type: 'text', value: '-', params: {} };
        }
    }

    /**
     * Génère une cellule pour le nom (lien vers la page de détail)
     * @private
     * @param {Object} format - Format résolu
     * @param {string} size - Taille d'écran
     * @param {Object} options - Options
     * @returns {Object} Cell object
     */
    _toNameCell(format, size, options) {
        const name = this.name || '-';
        const href = options.href || `/resources/${this.id}`;
        
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
     * @param {Object} format - Format résolu
     * @param {string} size - Taille d'écran
     * @param {Object} options - Options
     * @returns {Object} Cell object
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
     * Génère une cellule pour l'image (miniature)
     * @private
     * @param {Object} format - Format résolu
     * @param {string} size - Taille d'écran
     * @param {Object} options - Options
     * @returns {Object} Cell object
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
                alt: this.name || 'Resource image',
                width: imageSize,
                height: imageSize,
                sortValue: imageUrl,
                searchValue: imageUrl,
            },
        };
    }

    /**
     * Génère une cellule pour le type de ressource
     * @private
     * @param {Object} format - Format résolu
     * @param {string} size - Taille d'écran
     * @param {Object} options - Options
     * @returns {Object} Cell object
     */
    _toResourceTypeCell(format, size, options) {
        const resourceType = this.resourceType;
        
        if (!resourceType) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                },
            };
        }

        const typeName = resourceType.name || resourceType.label || '-';
        
        return {
            type: 'text',
            value: typeName,
            params: {
                sortValue: typeName,
                searchValue: typeName,
            },
        };
    }

    /**
     * Génère une cellule pour le créateur
     * @private
     * @param {Object} format - Format résolu
     * @param {string} size - Taille d'écran
     * @param {Object} options - Options
     * @returns {Object} Cell object
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
                sortValue: userName,
                searchValue: userName,
            },
        };
    }

    /**
     * Génère une cellule pour la date de création
     * @private
     * @param {Object} format - Format résolu
     * @param {string} size - Taille d'écran
     * @param {Object} options - Options
     * @returns {Object} Cell object
     */
    _toCreatedAtCell(format, size, options) {
        // Utiliser le DateFormatter via la méthode de base
        return super.toCell('created_at', options);
    }

    /**
     * Génère une cellule pour la date de modification
     * @private
     * @param {Object} format - Format résolu
     * @param {string} size - Taille d'écran
     * @param {Object} options - Options
     * @returns {Object} Cell object
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
            official_id: this.officialId,
            name: this.name,
            description: this.description,
            level: this.level,
            price: this.price,
            weight: this.weight,
            rarity: this.rarity,
            dofus_version: this.dofusVersion,
            usable: this.usable,
            is_visible: this._data.is_visible ?? null,
            image: this.image,
            auto_update: this.autoUpdate,
            resource_type_id: this.resourceTypeId
        };
    }
}

export default Resource;
