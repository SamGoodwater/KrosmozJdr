/**
 * Modèle ResourceType pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de resource-type côté frontend.
 * 
 * @example
 * const resourceType = new ResourceType(props.resourceType);
 * console.log(resourceType.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';
import { getFormatter } from '../../Utils/Formatters/FormatterRegistry.js';

export class ResourceType extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get name() {
        return this._data.name || '';
    }

    get dofusdbTypeId() {
        return this._data.dofusdb_type_id || null;
    }

    get decision() {
        return this._data.decision || 'pending';
    }

    get seenCount() {
        return this._data.seen_count || 0;
    }

    get lastSeenAt() {
        return this._data.last_seen_at || null;
    }

    get resourcesCount() {
        return this._data.resources_count || 0;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get createdBy() {
        return this._data.createdBy || null;
    }

    get resources() {
        return this._data.resources || [];
    }

    // ============================================
    // FORMATAGE DES CELLULES (surcharge pour champs spécifiques)
    // ============================================

    /**
     * Génère une cellule pour un champ (surcharge pour gérer les champs spécifiques à ResourceType)
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

        // Sinon, gérer les champs spécifiques à ResourceType
        const { size = 'md', format = {} } = options;
        
        switch (fieldKey) {
            case 'name':
                return this._toNameCell(format, size, options);
            case 'decision':
                return this._toDecisionCell(format, size, options);
            case 'created_by':
            case 'createdBy':
                return this._toCreatedByCell(format, size, options);
            case 'created_at':
                return this._toCreatedAtCell(format, size, options);
            case 'updated_at':
                return this._toUpdatedAtCell(format, size, options);
            case 'resources_count':
                return this._toResourcesCountCell(format, size, options);
            default:
                // Fallback vers la méthode de base
                return baseCell;
        }
    }

    /**
     * Génère une cellule pour le nom
     * @private
     * @param {Object} format - Format résolu
     * @param {string} size - Taille d'écran
     * @param {Object} options - Options
     * @returns {Object} Cell object
     */
    _toNameCell(format, size, options) {
        const name = this.name || '-';
        const href = options.href || (this.id ? `/resource-types/${this.id}` : undefined);
        
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
     * Génère une cellule pour la décision (statut)
     * @private
     * @param {Object} format - Format résolu
     * @param {string} size - Taille d'écran
     * @param {Object} options - Options
     * @returns {Object} Cell object
     */
    _toDecisionCell(format, size, options) {
        const decision = this.decision || 'pending';
        
        const labels = {
            'pending': 'En attente',
            'allowed': 'Utilisé',
            'blocked': 'Non utilisé',
        };
        
        const colors = {
            'pending': 'gray-700',
            'allowed': 'green-700',
            'blocked': 'red-700',
        };
        
        return {
            type: 'badge',
            value: labels[decision] || decision,
            params: {
                color: colors[decision] || 'gray-700',
                tooltip: labels[decision] || decision,
                filterValue: decision,
                sortValue: decision,
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
                tooltip: userName === '-' ? '' : userName,
                sortValue: userName === '-' ? '' : userName,
                searchValue: userName === '-' ? '' : userName,
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
        const dateFormatter = getFormatter('date');
        if (dateFormatter) {
            return dateFormatter.toCell(this.createdAt, { size, ...options });
        }
        
        const date = this.createdAt || null;
        if (!date) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                },
            };
        }

        return {
            type: 'text',
            value: new Date(date).toLocaleDateString('fr-FR'),
            params: {
                tooltip: new Date(date).toLocaleString('fr-FR'),
                sortValue: date,
                searchValue: new Date(date).toLocaleDateString('fr-FR'),
            },
        };
    }

    /**
     * Génère une cellule pour la date de mise à jour
     * @private
     * @param {Object} format - Format résolu
     * @param {string} size - Taille d'écran
     * @param {Object} options - Options
     * @returns {Object} Cell object
     */
    _toUpdatedAtCell(format, size, options) {
        const dateFormatter = getFormatter('date');
        if (dateFormatter) {
            return dateFormatter.toCell(this.updatedAt, { size, ...options });
        }
        
        const date = this.updatedAt || null;
        if (!date) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                },
            };
        }

        return {
            type: 'text',
            value: new Date(date).toLocaleDateString('fr-FR'),
            params: {
                tooltip: new Date(date).toLocaleString('fr-FR'),
                sortValue: date,
                searchValue: new Date(date).toLocaleDateString('fr-FR'),
            },
        };
    }

    /**
     * Génère une cellule pour le nombre de ressources
     * @private
     * @param {Object} format - Format résolu
     * @param {string} size - Taille d'écran
     * @param {Object} options - Options
     * @returns {Object} Cell object
     */
    _toResourcesCountCell(format, size, options) {
        const count = this.resourcesCount || 0;
        
        return {
            type: 'text',
            value: String(count),
            params: {
                tooltip: `${count} ressource${count > 1 ? 's' : ''}`,
                sortValue: count,
                searchValue: String(count),
            },
        };
    }

    /**
     * Retourne les données pour un formulaire
     * @returns {Object}
     */
    toFormData() {
        return {
            name: this.name,
            dofusdb_type_id: this.dofusdbTypeId,
            decision: this.decision,
            state: this.state,
            read_level: this.readLevel,
            write_level: this.writeLevel,
        };
    }
}
