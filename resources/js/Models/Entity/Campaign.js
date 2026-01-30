/**
 * Modèle Campaign pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de campaign côté frontend.
 * 
 * @example
 * const campaign = new Campaign(props.campaign);
 * console.log(campaign.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';

export class Campaign extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get name() {
        return this._data.name || '';
    }

    get description() {
        return this._data.description || '';
    }

    get slug() {
        return this._data.slug || null;
    }

    get keyword() {
        return this._data.keyword || null;
    }

    get isPublic() {
        return this._data.is_public ?? false;
    }

    get state() {
        return this._data.state || null;
    }

    get progressState() {
        return this._data.progress_state ?? null;
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

    get users() {
        return this._data.users || [];
    }

    get scenarios() {
        return this._data.scenarios || [];
    }

    get pages() {
        return this._data.pages || [];
    }

    get items() {
        return this._data.items || [];
    }

    get consumables() {
        return this._data.consumables || [];
    }

    get resources() {
        return this._data.resources || [];
    }

    get shops() {
        return this._data.shops || [];
    }

    get npcs() {
        return this._data.npcs || [];
    }

    get monsters() {
        return this._data.monsters || [];
    }

    get spells() {
        return this._data.spells || [];
    }

    get panoplies() {
        return this._data.panoplies || [];
    }

    get files() {
        return this._data.files || [];
    }

    /**
     * Retourne l'URL de l'entité
     * @returns {string}
     */
    get url() {
        if (!this.slug) return '';
        try {
            if (typeof route !== 'undefined') {
                return route('entities.campaigns.show', this.slug);
            }
            return `/entities/campaigns/${this.slug}`;
        } catch (e) {
            return `/entities/campaigns/${this.slug}`;
        }
    }

    /**
     * Retourne l'URL complète de l'entité
     * @returns {string}
     */
    get fullUrl() {
        if (!this.url) return '';
        return window.location.origin + this.url;
    }
    // ============================================
    // FORMATAGE DES CELLULES (surcharge pour champs spécifiques)
    // ============================================

    /**
     * Génère une cellule pour un champ (surcharge pour gérer les champs spécifiques à Campaign)
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

        // Sinon, gérer les champs spécifiques à Campaign
        const { size = 'md', format = {} } = options;
        
        switch (fieldKey) {
            case 'name':
                return this._toNameCell(format, size, options);
            case 'slug':
                return this._toSlugCell(format, size, options);
            case 'description':
                return this._toDescriptionCell(format, size, options);
            case 'keyword':
                return this._toKeywordCell(format, size, options);
            case 'state':
                return this._toStateCell(format, size, options);
            case 'is_public':
                return this._toIsPublicCell(format, size, options);
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
        const href = options.href || this.url || `/campaigns/${this.slug || this.id}`;
        
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
     * Génère une cellule pour le slug
     * @private
     */
    _toSlugCell(format, size, options) {
        const slug = this.slug || '-';
        
        return {
            type: 'text',
            value: slug,
            params: {
                sortValue: slug === '-' ? '' : slug,
                searchValue: slug === '-' ? '' : slug,
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
     * Génère une cellule pour le keyword
     * @private
     */
    _toKeywordCell(format, size, options) {
        const keyword = this.keyword || '-';
        
        return {
            type: 'text',
            value: keyword,
            params: {
                sortValue: keyword === '-' ? '' : keyword,
                searchValue: keyword === '-' ? '' : keyword,
            },
        };
    }

    /**
     * Génère une cellule pour l'état (state)
     * @private
     */
    _toStateCell(format, size, options) {
        const state = this.state ?? null;
        const STATE_LABELS = {
            0: 'En cours',
            1: 'Terminée',
            2: 'En pause',
            3: 'Annulée',
        };
        const label = state !== null && STATE_LABELS[state] ? STATE_LABELS[state] : (state !== null ? String(state) : '-');
        
        const STATE_COLORS = {
            0: 'info',
            1: 'success',
            2: 'warning',
            3: 'error',
        };
        const color = state !== null && STATE_COLORS[state] ? STATE_COLORS[state] : 'neutral';
        
        return {
            type: 'badge',
            value: label,
            params: {
                color,
                sortValue: state !== null ? Number(state) : 0,
                searchValue: label === '-' ? '' : label,
            },
        };
    }

    /**
     * Génère une cellule pour is_public
     * @private
     */
    _toIsPublicCell(format, size, options) {
        const isPublic = this.isPublic ?? false;
        const label = isPublic ? 'Oui' : 'Non';
        
        return {
            type: 'badge',
            value: label,
            params: {
                color: isPublic ? 'success' : 'neutral',
                sortValue: isPublic ? 1 : 0,
                searchValue: label,
            },
        };
    }

    /**
     * Génère une cellule pour une image
     * @private
     */
    _toImageCell(format, size, options) {
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
                alt: this.name || 'Campaign',
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
            name: this.name,
            description: this.description,
            slug: this.slug,
            keyword: this.keyword,
            is_public: this.isPublic,
            state: this.state,
            progress_state: this.progressState,
            read_level: this.readLevel,
            write_level: this.writeLevel,
            image: this.image
        };
    }
}

export default Campaign;
