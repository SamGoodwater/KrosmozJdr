/**
 * Modèle Spell pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de spell côté frontend.
 * 
 * @example
 * const spell = new Spell(props.spell);
 * console.log(spell.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';

export class Spell extends BaseModel {
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

    get area() {
        return this._data.area || null;
    }

    get level() {
        return this._data.level || null;
    }

    get po() {
        return this._data.po || null;
    }

    get poEditable() {
        return this._data.po_editable || null;
    }

    get pa() {
        return this._data.pa || null;
    }

    get castPerTurn() {
        return this._data.cast_per_turn || null;
    }

    get castPerTarget() {
        return this._data.cast_per_target || null;
    }

    get sightLine() {
        return this._data.sight_line || null;
    }

    get numberBetweenTwoCast() {
        return this._data.number_between_two_cast || null;
    }

    get numberBetweenTwoCastEditable() {
        return this._data.number_between_two_cast_editable || null;
    }

    get element() {
        return this._data.element || null;
    }

    get category() {
        return this._data.category || null;
    }

    get isMagic() {
        return this._data.is_magic || null;
    }

    get powerful() {
        return this._data.powerful || null;
    }

    get image() {
        return this._data.image || '';
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

    get creatures() {
        return this._data.creatures || [];
    }

    get classes() {
        return this._data.classes || [];
    }

    get scenarios() {
        return this._data.scenarios || [];
    }

    get campaigns() {
        return this._data.campaigns || [];
    }

    get spellTypes() {
        return this._data.spellTypes || [];
    }

    get monsters() {
        return this._data.monsters || [];
    }

    // ============================================
    // FORMATAGE DES CELLULES (surcharge pour champs spécifiques)
    // ============================================

    /**
     * Génère une cellule pour un champ (surcharge pour gérer les champs spécifiques à Spell)
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

        // Sinon, gérer les champs spécifiques à Spell
        const { size = 'md', format = {} } = options;
        
        switch (fieldKey) {
            case 'name':
                return this._toNameCell(format, size, options);
            case 'description':
                return this._toDescriptionCell(format, size, options);
            case 'effect':
                return this._toEffectCell(format, size, options);
            case 'area':
                return this._toAreaCell(format, size, options);
            case 'po':
                return this._toPoCell(format, size, options);
            case 'pa':
                return this._toPaCell(format, size, options);
            case 'cast_per_turn':
                return this._toCastPerTurnCell(format, size, options);
            case 'cast_per_target':
                return this._toCastPerTargetCell(format, size, options);
            case 'sight_line':
                return this._toSightLineCell(format, size, options);
            case 'number_between_two_cast':
                return this._toNumberBetweenTwoCastCell(format, size, options);
            case 'is_magic':
                return this._toIsMagicCell(format, size, options);
            case 'powerful':
                return this._toPowerfulCell(format, size, options);
            case 'image':
                return this._toImageCell(format, size, options);
            case 'spell_types':
            case 'spellTypes':
                return this._toSpellTypesCell(format, size, options);
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
        const href = options.href || `/spells/${this.id}`;
        
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
     * Génère une cellule pour la zone (area)
     * @private
     */
    _toAreaCell(format, size, options) {
        const area = this.area ?? null;
        const value = area !== null ? String(area) : '-';
        
        return {
            type: 'text',
            value,
            params: {
                sortValue: area ?? 0,
                searchValue: value === '-' ? '' : value,
            },
        };
    }

    /**
     * Génère une cellule pour les PO (portée)
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
     * Génère une cellule pour les PA (coût)
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
     * Génère une cellule pour les lancers par tour
     * @private
     */
    _toCastPerTurnCell(format, size, options) {
        const castPerTurn = this.castPerTurn || '-';
        
        return {
            type: 'text',
            value: castPerTurn,
            params: {
                sortValue: castPerTurn === '-' ? '' : castPerTurn,
                searchValue: castPerTurn === '-' ? '' : castPerTurn,
            },
        };
    }

    /**
     * Génère une cellule pour les lancers par cible
     * @private
     */
    _toCastPerTargetCell(format, size, options) {
        const castPerTarget = this.castPerTarget || '-';
        
        return {
            type: 'text',
            value: castPerTarget,
            params: {
                sortValue: castPerTarget === '-' ? '' : castPerTarget,
                searchValue: castPerTarget === '-' ? '' : castPerTarget,
            },
        };
    }

    /**
     * Génère une cellule pour la ligne de vue
     * @private
     */
    _toSightLineCell(format, size, options) {
        // Utiliser le BooleanFormatter via la méthode de base
        return super.toCell('sight_line', options);
    }

    /**
     * Génère une cellule pour le nombre entre deux lancers
     * @private
     */
    _toNumberBetweenTwoCastCell(format, size, options) {
        const number = this.numberBetweenTwoCast || '-';
        
        return {
            type: 'text',
            value: number,
            params: {
                sortValue: number === '-' ? '' : number,
                searchValue: number === '-' ? '' : number,
            },
        };
    }

    /**
     * Génère une cellule pour is_magic
     * @private
     */
    _toIsMagicCell(format, size, options) {
        // Utiliser le BooleanFormatter via la méthode de base
        return super.toCell('is_magic', options);
    }

    /**
     * Génère une cellule pour powerful
     * @private
     */
    _toPowerfulCell(format, size, options) {
        const powerful = this.powerful ?? null;
        const value = powerful !== null ? String(powerful) : '-';
        
        return {
            type: 'text',
            value,
            params: {
                sortValue: powerful ?? 0,
                searchValue: value === '-' ? '' : value,
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
                alt: this.name || 'Spell image',
                width: imageSize,
                height: imageSize,
                sortValue: imageUrl,
                searchValue: imageUrl,
            },
        };
    }

    /**
     * Génère une cellule pour les types de sort
     * @private
     */
    _toSpellTypesCell(format, size, options) {
        const spellTypes = this.spellTypes || [];
        
        if (!spellTypes.length) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                },
            };
        }

        const typeNames = spellTypes.map(t => t.name || t.label || '-').filter(n => n !== '-');
        const displayValue = typeNames.join(', ') || '-';
        
        return {
            type: 'text',
            value: displayValue,
            params: {
                tooltip: displayValue === '-' ? '' : displayValue,
                sortValue: displayValue,
                searchValue: displayValue === '-' ? '' : displayValue,
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
            area: this.area,
            level: this.level,
            po: this.po,
            po_editable: this.poEditable,
            pa: this.pa,
            cast_per_turn: this.castPerTurn,
            cast_per_target: this.castPerTarget,
            sight_line: this.sightLine,
            number_between_two_cast: this.numberBetweenTwoCast,
            number_between_two_cast_editable: this.numberBetweenTwoCastEditable,
            element: this.element,
            category: this.category,
            is_magic: this.isMagic,
            powerful: this.powerful,
            state: this.state,
            read_level: this.readLevel,
            write_level: this.writeLevel,
            image: this.image,
            auto_update: this.autoUpdate
        };
    }
}

export default Spell;
