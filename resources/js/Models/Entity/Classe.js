/**
 * Modèle Classe pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de classe côté frontend.
 * 
 * @example
 * const classe = new Classe(props.classe);
 * console.log(classe.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';

export class Classe extends BaseModel {
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

    get descriptionFast() {
        return this._data.description_fast || null;
    }

    get description() {
        return this._data.description || '';
    }

    get life() {
        return this._data.life || null;
    }

    get lifeDice() {
        return this._data.life_dice || null;
    }

    get specificity() {
        return this._data.specificity || null;
    }

    get dofusVersion() {
        return this._data.dofus_version || null;
    }

    get usable() {
        return this._data.usable ?? false;
    }

    get image() {
        return this._data.image || '';
    }

    get icon() {
        return this._data.icon || null;
    }

    get autoUpdate() {
        return this._data.auto_update ?? false;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get createdBy() {
        return this._data.createdBy || null;
    }

    get npcs() {
        return this._data.npcs || [];
    }

    get spells() {
        return this._data.spells || [];
    }

    // ============================================
    // FORMATAGE DES CELLULES (surcharge pour champs spécifiques)
    // ============================================

    /**
     * Génère une cellule pour un champ (surcharge pour gérer les champs spécifiques à Classe)
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

        // Sinon, gérer les champs spécifiques à Classe
        const { size = 'md', format = {} } = options;
        
        switch (fieldKey) {
            case 'name':
                return this._toNameCell(format, size, options);
            case 'description':
            case 'description_fast':
                return this._toDescriptionCell(fieldKey, format, size, options);
            case 'life':
            case 'life_dice':
                return this._toNumericCell(fieldKey, format, size, options);
            case 'specificity':
                return this._toSpecificityCell(format, size, options);
            case 'image':
            case 'icon':
                return this._toImageCell(fieldKey, format, size, options);
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
        const href = options.href || `/classes/${this.id}`;
        
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
    _toDescriptionCell(fieldKey, format, size, options) {
        const description = fieldKey === 'description_fast' 
            ? (this.descriptionFast || this.description || '-')
            : (this.description || '-');
        
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
     * Génère une cellule pour un champ numérique
     * @private
     */
    _toNumericCell(fieldKey, format, size, options) {
        const value = this[fieldKey] ?? null;
        const displayValue = value !== null && value !== '' ? String(value) : '-';
        
        return {
            type: 'text',
            value: displayValue,
            params: {
                sortValue: value !== null && value !== '' ? Number(value) || 0 : 0,
                searchValue: displayValue === '-' ? '' : displayValue,
            },
        };
    }

    /**
     * Génère une cellule pour la spécificité
     * @private
     */
    _toSpecificityCell(format, size, options) {
        const specificity = this.specificity || '-';
        
        return {
            type: 'text',
            value: specificity,
            params: {
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 30 : (size === 'md' ? 50 : null)),
                searchValue: specificity === '-' ? '' : specificity,
                sortValue: specificity,
            },
        };
    }

    /**
     * Génère une cellule pour une image/icône
     * @private
     */
    _toImageCell(fieldKey, format, size, options) {
        const imageUrl = fieldKey === 'icon' ? this.icon : this.image;
        
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
        
        return {
            type: 'image',
            value: imageUrl,
            params: {
                alt: this.name || fieldKey,
                size: size === 'xs' ? 'xs' : (size === 'sm' ? 'sm' : 'md'),
                sortValue: imageUrl,
                searchValue: imageUrl,
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
            official_id: this.officialId,
            dofusdb_id: this.dofusdbId,
            name: this.name,
            description_fast: this.descriptionFast,
            description: this.description,
            life: this.life,
            life_dice: this.lifeDice,
            specificity: this.specificity,
            dofus_version: this.dofusVersion,
            usable: this.usable,
            image: this.image,
            icon: this.icon,
            auto_update: this.autoUpdate
        };
    }
}

export default Classe;
