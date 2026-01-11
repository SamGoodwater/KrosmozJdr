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

    get usable() {
        return this._data.usable ?? false;
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
        
        // Si la méthode de base a trouvé quelque chose (formatter ou valeur par défaut valide), l'utiliser
        if (baseCell && (baseCell.type !== 'text' || (baseCell.value && baseCell.value !== '-'))) {
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
            case 'usable':
                return this._toUsableCell(format, size, options);
            case 'is_visible':
                return this._toIsVisibleCell(format, size, options);
            case 'dofusdb_id':
                return this._toDofusdbIdCell(format, size, options);
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
        const bonus = this.bonus || '-';
        
        return {
            type: 'text',
            value: bonus,
            params: {
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 20 : (size === 'md' ? 30 : null)),
                searchValue: bonus === '-' ? '' : bonus,
                sortValue: bonus,
            },
        };
    }

    /**
     * Génère une cellule pour usable
     * @private
     */
    _toUsableCell(format, size, options) {
        const usable = this.usable ?? false;
        const label = usable ? 'Oui' : 'Non';
        
        return {
            type: 'badge',
            value: label,
            params: {
                color: usable ? 'success' : 'neutral',
                sortValue: usable ? 1 : 0,
                searchValue: label,
            },
        };
    }

    /**
     * Génère une cellule pour is_visible
     * @private
     */
    _toIsVisibleCell(format, size, options) {
        // Utiliser le VisibilityFormatter via la méthode de base
        return super.toCell('is_visible', options);
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
            dofusdb_id: this.dofusdbId,
            name: this.name,
            description: this.description,
            bonus: this.bonus,
            usable: this.usable
        };
    }
}

export default Panoply;
