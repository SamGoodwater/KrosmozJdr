/**
 * Modèle Specialization pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de specialization côté frontend.
 * 
 * @example
 * const specialization = new Specialization(props.specialization);
 * console.log(specialization.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';
import { resolveEntityRouteHref } from '@/Composables/entity/entityRouteRegistry';
import { getFormatter } from '@/Utils/Formatters/FormatterRegistry.js';

export class Specialization extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get name() {
        return this._data.name || '';
    }

    get description() {
        return this._data.description ?? null;
    }

    get shortDescription() {
        return this._data.short_description ?? null;
    }

    get image() {
        return this._data.image || '';
    }

    // ============================================
    // RELATIONS
    // ============================================

    get createdBy() {
        return this._data.createdBy || null;
    }

    get capabilities() {
        return this._data.capabilities || [];
    }

    get creatureTraits() {
        return this._data.creatureTraits || [];
    }

    get spells() {
        return this._data.spells || [];
    }

    get consumables() {
        return this._data.consumables || [];
    }

    get resources() {
        return this._data.resources || [];
    }

    get items() {
        return this._data.items || [];
    }

    get sections() {
        return this._data.sections || [];
    }

    get npcs() {
        return this._data.npcs || [];
    }

    // ============================================
    // FORMATAGE DES CELLULES (surcharge pour champs spécifiques)
    // ============================================

    /**
     * Génère une cellule pour un champ (surcharge pour gérer les champs spécifiques à Specialization)
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

        // Sinon, gérer les champs spécifiques à Specialization
        const { size = 'md', format = {} } = options;
        
        switch (fieldKey) {
            case 'name':
                return this._toNameCell(format, size, options);
            case 'description':
                return this._toDescriptionCell(format, size, options);
            case 'short_description':
                return this._toShortDescriptionCell(format, size, options);
            case 'image':
                return this._toImageCell(format, size, options);
            case 'capabilities_count':
                return this._toCapabilitiesCountCell(format, size, options);
            case 'spells_count':
                return this._toSpellsCountCell(format, size, options);
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
        const href = options.href || resolveEntityRouteHref('specializations', 'show', this.id) || `/entities/specializations/${this.id}`;
        
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
    _toDescriptionCell(format, size, _options) {
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

    _toShortDescriptionCell(format, size, _options) {
        const shortDescription = this.shortDescription || this.description || '-';

        return {
            type: 'text',
            value: shortDescription,
            params: {
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 30 : (size === 'md' ? 60 : null)),
                searchValue: shortDescription === '-' ? '' : shortDescription,
                sortValue: shortDescription,
            },
        };
    }

    /**
     * Génère une cellule pour image.
     * Ne pas appeler `super.toCell('image')` : `BaseModel.toCell` délègue déjà à `_toImageCell`, ce qui provoquerait une récursion infinie.
     *
     * @private
     */
    _toImageCell(format, size, options) {
        const FormatterClass = getFormatter('image');
        if (FormatterClass?.toCell) {
            const cell = FormatterClass.toCell(this._data.image, { ...options, size, format });
            if (cell?.type) return cell;
        }
        return {
            type: 'text',
            value: this.image || '-',
            params: { sortValue: this.image || '', searchValue: this.image || '' },
        };
    }

    /**
     * Génère une cellule pour capabilities_count
     * @private
     */
    _toCapabilitiesCountCell(_format, _size, _options) {
        const capabilitiesCount = this.capabilities?.length || this._data.capabilities_count || 0;
        
        return {
            type: 'text',
            value: String(capabilitiesCount),
            params: {
                sortValue: Number(capabilitiesCount),
                searchValue: String(capabilitiesCount),
            },
        };
    }

    _toSpellsCountCell(_format, _size, _options) {
        const spellsCount = this.spells?.length || this._data.spells_count || 0;

        return {
            type: 'text',
            value: String(spellsCount),
            params: {
                sortValue: Number(spellsCount),
                searchValue: String(spellsCount),
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
            short_description: this.shortDescription,
            description: this.description ?? "",
            state: this.state,
            read_level: this.readLevel,
            write_level: this.writeLevel,
            image: this.image
        };
    }
}

export default Specialization;
