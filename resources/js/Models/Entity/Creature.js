/**
 * Modèle Creature pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de creature côté frontend.
 * 
 * @example
 * const creature = new Creature(props.creature);
 * console.log(creature.name); // Accès normalisé
 */
import { BaseModel } from '../BaseModel';

export class Creature extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get name() {
        return this._data.name || '';
    }

    get description() {
        return this._data.description || '';
    }

    get hostility() {
        return this._data.hostility || null;
    }

    get location() {
        return this._data.location || null;
    }

    get level() {
        return this._data.level || null;
    }

    get life() {
        return this._data.life || null;
    }

    get pa() {
        return this._data.pa || null;
    }

    get pm() {
        return this._data.pm || null;
    }

    get po() {
        return this._data.po || null;
    }

    get ini() {
        return this._data.ini || null;
    }

    get invocation() {
        return this._data.invocation || null;
    }

    get touch() {
        return this._data.touch || null;
    }

    get ca() {
        return this._data.ca || null;
    }

    get dodgePa() {
        return this._data.dodge_pa || null;
    }

    get dodgePm() {
        return this._data.dodge_pm || null;
    }

    get fuite() {
        return this._data.fuite || null;
    }

    get tacle() {
        return this._data.tacle || null;
    }

    get vitality() {
        return this._data.vitality || null;
    }

    get sagesse() {
        return this._data.sagesse || null;
    }

    get strong() {
        return this._data.strong || null;
    }

    get intel() {
        return this._data.intel || null;
    }

    get agi() {
        return this._data.agi || null;
    }

    get chance() {
        return this._data.chance || null;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get createdBy() {
        return this._data.createdBy || null;
    }

    get attributes() {
        return this._data.attributes || [];
    }

    get capabilities() {
        return this._data.capabilities || [];
    }

    get items() {
        return this._data.items || [];
    }

    get resources() {
        return this._data.resources || [];
    }

    get spells() {
        return this._data.spells || [];
    }

    get consumables() {
        return this._data.consumables || [];
    }

    get npc() {
        return this._data.npc || null;
    }

    get monster() {
        return this._data.monster || [];
    }

    // ============================================
    // FORMATAGE DES CELLULES (surcharge pour champs spécifiques)
    // ============================================

    /**
     * Génère une cellule pour un champ (surcharge pour gérer les champs spécifiques à Creature)
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

        // Sinon, gérer les champs spécifiques à Creature
        const { size = 'md', format = {} } = options;
        
        switch (fieldKey) {
            case 'name':
                return this._toNameCell(format, size, options);
            case 'description':
                return this._toDescriptionCell(format, size, options);
            case 'hostility':
                // HostilityFormatter est géré automatiquement par BaseModel via FormatterRegistry
                return super.toCell('hostility', options);
            case 'location':
                return this._toLocationCell(format, size, options);
            case 'level':
                // LevelFormatter est géré automatiquement par BaseModel via FormatterRegistry
                return super.toCell('level', options);
            case 'life':
            case 'pa':
            case 'pm':
            case 'po':
            case 'ini':
            case 'invocation':
            case 'touch':
            case 'ca':
            case 'dodge_pa':
            case 'dodge_pm':
            case 'fuite':
            case 'tacle':
            case 'vitality':
            case 'sagesse':
            case 'strong':
            case 'intel':
            case 'agi':
            case 'chance':
                return this._toNumericCell(fieldKey, format, size, options);
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
        const href = options.href || `/creatures/${this.id}`;
        
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
     * Génère une cellule pour la localisation
     * @private
     */
    _toLocationCell(format, size, options) {
        const location = this.location || '-';
        
        return {
            type: 'text',
            value: location,
            params: {
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 15 : null),
                searchValue: location === '-' ? '' : location,
                sortValue: location,
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
            hostility: this.hostility,
            location: this.location,
            level: this.level,
            life: this.life,
            pa: this.pa,
            pm: this.pm,
            po: this.po,
            ini: this.ini,
            invocation: this.invocation,
            touch: this.touch,
            ca: this.ca,
            dodge_pa: this.dodgePa,
            dodge_pm: this.dodgePm,
            fuite: this.fuite,
            tacle: this.tacle,
            vitality: this.vitality,
            sagesse: this.sagesse,
            strong: this.strong,
            intel: this.intel,
            agi: this.agi,
            chance: this.chance
        };
    }
}

export default Creature;
