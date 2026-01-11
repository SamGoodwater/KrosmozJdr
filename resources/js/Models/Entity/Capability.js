/**
 * Modèle Capability pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de capability côté frontend.
 * 
 * @example
 * const capability = new Capability(props.capability);
 * console.log(capability.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';

export class Capability extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

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

    get pa() {
        return this._data.pa || null;
    }

    get po() {
        return this._data.po || null;
    }

    get poEditable() {
        return this._data.po_editable || null;
    }

    get timeBeforeUseAgain() {
        return this._data.time_before_use_again || null;
    }

    get castingTime() {
        return this._data.casting_time || null;
    }

    get duration() {
        return this._data.duration || null;
    }

    get element() {
        return this._data.element || null;
    }

    get isMagic() {
        return this._data.is_magic || null;
    }

    get ritualAvailable() {
        return this._data.ritual_available || null;
    }

    get powerful() {
        return this._data.powerful || null;
    }

    get usable() {
        return this._data.usable ?? false;
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

    get specializations() {
        return this._data.specializations || [];
    }

    get creatures() {
        return this._data.creatures || [];
    }

    // ============================================
    // FORMATAGE DES CELLULES (surcharge pour champs spécifiques)
    // ============================================

    /**
     * Génère une cellule pour un champ (surcharge pour gérer les champs spécifiques à Capability)
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

        // Sinon, gérer les champs spécifiques à Capability
        const { size = 'md', format = {} } = options;
        
        switch (fieldKey) {
            case 'name':
                return this._toNameCell(format, size, options);
            case 'description':
                return this._toDescriptionCell(format, size, options);
            case 'effect':
                return this._toEffectCell(format, size, options);
            case 'level':
                return this._toLevelCell(format, size, options);
            case 'pa':
                return this._toPaCell(format, size, options);
            case 'po':
                return this._toPoCell(format, size, options);
            case 'po_editable':
                return this._toPoEditableCell(format, size, options);
            case 'time_before_use_again':
                return this._toTimeBeforeUseAgainCell(format, size, options);
            case 'casting_time':
                return this._toCastingTimeCell(format, size, options);
            case 'duration':
                return this._toDurationCell(format, size, options);
            case 'element':
                return this._toElementCell(format, size, options);
            case 'is_magic':
                return this._toIsMagicCell(format, size, options);
            case 'ritual_available':
                return this._toRitualAvailableCell(format, size, options);
            case 'powerful':
                return this._toPowerfulCell(format, size, options);
            case 'usable':
                return this._toUsableCell(format, size, options);
            case 'is_visible':
                return this._toIsVisibleCell(format, size, options);
            case 'image':
                return this._toImageCell(format, size, options);
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
        const href = options.href || `/capabilities/${this.id}`;
        
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
     * Génère une cellule pour l'effet
     * @private
     */
    _toEffectCell(format, size, options) {
        const effect = this.effect || '-';
        
        return {
            type: 'text',
            value: effect,
            params: {
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 20 : (size === 'md' ? 30 : null)),
                searchValue: effect === '-' ? '' : effect,
                sortValue: effect,
            },
        };
    }

    /**
     * Génère une cellule pour le niveau
     * @private
     */
    _toLevelCell(format, size, options) {
        // Utiliser le LevelFormatter via la méthode de base
        return super.toCell('level', options);
    }

    /**
     * Génère une cellule pour PA
     * @private
     */
    _toPaCell(format, size, options) {
        const pa = this.pa || '-';
        
        return {
            type: 'text',
            value: pa,
            params: {
                sortValue: pa === '-' ? '' : pa,
                searchValue: pa === '-' ? '' : pa,
            },
        };
    }

    /**
     * Génère une cellule pour PO
     * @private
     */
    _toPoCell(format, size, options) {
        const po = this.po || '-';
        
        return {
            type: 'text',
            value: po,
            params: {
                sortValue: po === '-' ? '' : po,
                searchValue: po === '-' ? '' : po,
            },
        };
    }

    /**
     * Génère une cellule pour po_editable
     * @private
     */
    _toPoEditableCell(format, size, options) {
        const poEditable = this.poEditable ?? false;
        const label = poEditable ? 'Oui' : 'Non';
        
        return {
            type: 'badge',
            value: label,
            params: {
                color: poEditable ? 'success' : 'neutral',
                sortValue: poEditable ? 1 : 0,
                searchValue: label,
            },
        };
    }

    /**
     * Génère une cellule pour time_before_use_again
     * @private
     */
    _toTimeBeforeUseAgainCell(format, size, options) {
        const time = this.timeBeforeUseAgain || '-';
        
        return {
            type: 'text',
            value: time,
            params: {
                sortValue: time === '-' ? '' : time,
                searchValue: time === '-' ? '' : time,
            },
        };
    }

    /**
     * Génère une cellule pour casting_time
     * @private
     */
    _toCastingTimeCell(format, size, options) {
        const castingTime = this.castingTime || '-';
        
        return {
            type: 'text',
            value: castingTime,
            params: {
                sortValue: castingTime === '-' ? '' : castingTime,
                searchValue: castingTime === '-' ? '' : castingTime,
            },
        };
    }

    /**
     * Génère une cellule pour duration
     * @private
     */
    _toDurationCell(format, size, options) {
        const duration = this.duration || '-';
        
        return {
            type: 'text',
            value: duration,
            params: {
                sortValue: duration === '-' ? '' : duration,
                searchValue: duration === '-' ? '' : duration,
            },
        };
    }

    /**
     * Génère une cellule pour element
     * @private
     */
    _toElementCell(format, size, options) {
        // Utiliser le ElementFormatter via la méthode de base
        return super.toCell('element', options);
    }

    /**
     * Génère une cellule pour is_magic
     * @private
     */
    _toIsMagicCell(format, size, options) {
        const isMagic = this.isMagic ?? false;
        const label = isMagic ? 'Oui' : 'Non';
        
        return {
            type: 'badge',
            value: label,
            params: {
                color: isMagic ? 'success' : 'neutral',
                sortValue: isMagic ? 1 : 0,
                searchValue: label,
            },
        };
    }

    /**
     * Génère une cellule pour ritual_available
     * @private
     */
    _toRitualAvailableCell(format, size, options) {
        const ritualAvailable = this.ritualAvailable ?? false;
        const label = ritualAvailable ? 'Oui' : 'Non';
        
        return {
            type: 'badge',
            value: label,
            params: {
                color: ritualAvailable ? 'success' : 'neutral',
                sortValue: ritualAvailable ? 1 : 0,
                searchValue: label,
            },
        };
    }

    /**
     * Génère une cellule pour powerful
     * @private
     */
    _toPowerfulCell(format, size, options) {
        const powerful = this.powerful || '-';
        
        return {
            type: 'text',
            value: powerful,
            params: {
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 15 : null),
                sortValue: powerful === '-' ? '' : powerful,
                searchValue: powerful === '-' ? '' : powerful,
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
     * Génère une cellule pour image
     * @private
     */
    _toImageCell(format, size, options) {
        // Utiliser le ImageFormatter via la méthode de base
        return super.toCell('image', options);
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
            effect: this.effect,
            level: this.level,
            pa: this.pa,
            po: this.po,
            po_editable: this.poEditable,
            time_before_use_again: this.timeBeforeUseAgain,
            casting_time: this.castingTime,
            duration: this.duration,
            element: this.element,
            is_magic: this.isMagic,
            ritual_available: this.ritualAvailable,
            powerful: this.powerful,
            usable: this.usable,
            image: this.image
        };
    }
}

export default Capability;
