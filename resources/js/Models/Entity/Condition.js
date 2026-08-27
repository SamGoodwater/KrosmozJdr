/**
 * Modèle Condition pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de condition côté frontend.
 * 
 * @example
 * const condition = new Condition(props.condition);
 * console.log(condition.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';
import { CONDITION_MECHANICAL_FLAGS, formatConditionDispellable, getConditionDispellableIcon, listActiveMechanicalFlags, readConditionFlag } from '@/Composables/condition/conditionDisplay';
import { resolveEntityRouteHref } from '@/Composables/entity/entityRouteRegistry';

export class Condition extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get name() {
        return this._data.name || '';
    }

    get description() {
        return this._data.description || '';
    }

    get image() {
        return this._data.image || '';
    }

    /**
     * Peut être dissipé ; défaut `true` si la clé est absente des données.
     * @returns {boolean}
     */
    get dissipable() {
        const v = this._data.dissipable;
        if (v === null || v === undefined) return true;
        return Boolean(v);
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

    // ============================================
    // FORMATAGE DES CELLULES (surcharge pour champs spécifiques)
    // ============================================

    /**
     * Génère une cellule pour un champ (surcharge pour gérer les champs spécifiques à Condition)
     * @param {string} fieldKey - Clé du champ
     * @param {Object} [options={}] - Options (size, context, config, ctx)
     * @returns {Object|null} Cell object ou null si valeur invalide
     */
    toCell(fieldKey, options = {}) {
        if (fieldKey === 'dissipable') {
            return this._toDissipableCell(options);
        }
        if (fieldKey === 'mechanical_flags') {
            return this._toMechanicalFlagsCell(options);
        }

        // D'abord, essayer la méthode de base (gère les formatters automatiquement)
        const baseCell = super.toCell(fieldKey, options);
        
        // Si la méthode de base a trouvé quelque chose (formatter ou valeur par défaut valide), l'utiliser
        if (baseCell && (baseCell.type !== 'text' || (baseCell.value && baseCell.value !== '-'))) {
            return baseCell;
        }

        // Sinon, gérer les champs spécifiques à Condition
        const { size = 'md', format = {} } = options;
        
        switch (fieldKey) {
            case 'name':
                return this._toNameCell(format, size, options);
            case 'description':
                return this._toDescriptionCell(format, size, options);
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
        const href = options.href || resolveEntityRouteHref('conditions', 'show', this.id) || `/entities/conditions/${this.id}`;
        
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

    /**
     * Génère une cellule pour une image
     * @private
     */
    _toImageCell(format, size, _options) {
        const imageUrl = this.image;
        
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
                alt: this.name || 'État',
                size: size === 'xs' ? 'xs' : (size === 'sm' ? 'sm' : 'md'),
                sortValue: imageUrl,
                searchValue: imageUrl,
            },
        };
    }

    /**
     * Cellule dissipabilité (icône caractéristique).
     * @private
     */
    _toDissipableCell(_options) {
        const d = this.dissipable;
        const icon = getConditionDispellableIcon(d);
        const label = formatConditionDispellable(d) || '';

        return {
            type: 'image',
            value: icon || '',
            params: {
                alt: label,
                tooltip: label,
                searchValue: label,
                sortValue: d ? 1 : 0,
                filterValue: d ? '1' : '0',
            },
        };
    }

    /**
     * Cellule résumé des flags mécaniques actifs.
     * @private
     */
    _toMechanicalFlagsCell(options = {}) {
        const flags = listActiveMechanicalFlags(this);
        const summary = flags.map((f) => f.label).join(' · ') || '—';
        const { size = 'md', format = {} } = options;

        return {
            type: 'text',
            value: summary,
            params: {
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 32 : null),
                searchValue: summary === '—' ? '' : summary,
                sortValue: summary,
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
        const flags = {};
        for (const { key } of CONDITION_MECHANICAL_FLAGS) {
            flags[key] = readConditionFlag(this, key);
        }

        return {
            name: this.name,
            description: this.description,
            state: this.state,
            read_level: this.readLevel,
            write_level: this.writeLevel,
            dissipable: this.dissipable,
            image: this.image,
            ...flags,
        };
    }
}

export default Condition;
