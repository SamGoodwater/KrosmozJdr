/**
 * Classe de base pour tous les modèles frontend
 * 
 * @description
 * Classe abstraite qui fournit les fonctionnalités communes à tous les modèles :
 * - Extraction normalisée des données (gère les Proxies Vue/Inertia)
 * - Propriétés communes (id, created_by, created_at, updated_at)
 * - Permissions communes (can.update, can.delete, etc.)
 * - Méthodes utilitaires communes
 * - Formatage des données via FormatterRegistry
 * - Cache des cellules générées
 * 
 * @abstract
 */
import { getFormatter } from '../Utils/Formatters/FormatterRegistry.js';

export class BaseModel {
    /**
     * @param {Object} rawData - Données brutes (peut être un Proxy, un objet avec .data, etc.)
     */
    constructor(rawData) {
        // Normaliser l'extraction des données
        this._raw = rawData;
        this._data = this._extractData(rawData);
        
        // Cache des cellules générées (Map<fieldKey-options, Cell>)
        this._cellCache = new Map();
    }

    /**
     * Extrait les données normalisées depuis différentes structures possibles
     * Gère les Proxies Vue/Inertia en désenveloppant si nécessaire
     * @protected
     * @param {*} raw - Données brutes (peut être un Proxy Vue/Inertia)
     * @returns {Object} Données normalisées
     */
    _extractData(raw) {
        if (!raw) return {};
        
        // Si c'est déjà une instance du même modèle, retourner ses données
        if (raw instanceof this.constructor) {
            return raw._data;
        }
        
        // Désenvelopper les Proxies Vue/Inertia si nécessaire
        // Les Proxies peuvent masquer certaines propriétés comme 'can' au niveau racine
        let unwrapped = raw;
        try {
            // Vérifier si c'est un Proxy en essayant d'accéder à une propriété clé
            // Si 'can' existe au niveau racine, on doit préserver cette structure
            const hasCanAtRoot = 'can' in raw || raw.can !== undefined;
            const hasDataProperty = raw.data && typeof raw.data === 'object';
            
            // Si 'can' est au niveau racine ET qu'il y a une propriété .data,
            // on doit fusionner les deux pour ne pas perdre 'can'
            if (hasCanAtRoot && hasDataProperty) {
                // Fusionner : données de .data + propriétés au niveau racine (comme 'can')
                return {
                    ...raw.data,
                    can: raw.can, // Préserver 'can' au niveau racine
                };
            }
            
            // Si les données sont dans .data (structure Inertia Resource)
            if (hasDataProperty && !hasCanAtRoot) {
                return raw.data;
            }
        } catch (e) {
            // En cas d'erreur avec les Proxies, continuer avec raw
        }
        
        // Sinon, utiliser raw directement
        return unwrapped;
    }

    // ============================================
    // PROPRIÉTÉS COMMUNES
    // ============================================

    get id() {
        return this._data.id;
    }

    get createdById() {
        return this._data.created_by;
    }

    get createdAt() {
        return this._data.created_at;
    }

    get updatedAt() {
        return this._data.updated_at;
    }

    get deletedAt() {
        return this._data.deleted_at;
    }

    get isVisible() {
        return this._data.is_visible;
    }

    // ============================================
    // RELATIONS COMMUNES
    // ============================================

    /**
     * Retourne l'utilisateur créateur (relation)
     * Peut être surchargé dans les classes filles
     */
    get createdBy() {
        return this._data.createdBy || null;
    }

    /**
     * Alias pour createdBy (pour compatibilité)
     */
    get createdByUser() {
        return this.createdBy;
    }

    // ============================================
    // PERMISSIONS COMMUNES
    // ============================================

    get can() {
        return this._data.can || {
            update: false,
            delete: false,
            forceDelete: false,
            restore: false,
            view: false
        };
    }

    get canUpdate() {
        return this.can.update || false;
    }

    get canDelete() {
        return this.can.delete || false;
    }

    get canForceDelete() {
        return this.can.forceDelete || false;
    }

    get canRestore() {
        return this.can.restore || false;
    }

    get canView() {
        return this.can.view || false;
    }

    // ============================================
    // MÉTHODES UTILITAIRES COMMUNES
    // ============================================

    /**
     * Retourne les données brutes (pour compatibilité)
     * @returns {Object}
     */
    toRaw() {
        return this._data;
    }

    /**
     * Retourne les données pour un formulaire (à surcharger dans les classes filles)
     * @returns {Object}
     */
    toFormData() {
        return {
            ...this._data
        };
    }

    /**
     * Vérifie si l'entité est supprimée (soft delete)
     * @returns {boolean}
     */
    get isDeleted() {
        return !!this.deletedAt;
    }

    /**
     * Crée une instance depuis des données brutes (méthode statique)
     * @param {*} rawData - Données brutes
     * @returns {BaseModel}
     */
    static from(rawData) {
        return new this(rawData);
    }

    /**
     * Crée un tableau d'instances depuis un tableau de données brutes
     * @param {Array} rawDataArray - Tableau de données brutes
     * @returns {Array<BaseModel>}
     */
    static fromArray(rawDataArray) {
        if (!Array.isArray(rawDataArray)) return [];
        return rawDataArray.map(data => new this(data));
    }

    // ============================================
    // FORMATAGE DES DONNÉES (via FormatterRegistry)
    // ============================================

    /**
     * Vérifie si l'entité a une propriété
     * @param {string} fieldKey - Clé du champ
     * @returns {boolean}
     */
    has(fieldKey) {
        return fieldKey in this._data && 
               this._data[fieldKey] !== null && 
               this._data[fieldKey] !== undefined;
    }

    /**
     * Formate une propriété en utilisant le formatter correspondant
     * @param {string} fieldKey - Clé du champ
     * @param {Object} [options={}] - Options de formatage
     * @returns {string|null} Label formaté ou null si valeur invalide
     */
    format(fieldKey, options = {}) {
        if (!this.has(fieldKey)) {
            return null;
        }

        const FormatterClass = getFormatter(fieldKey);
        if (!FormatterClass || typeof FormatterClass.format !== 'function') {
            // Fallback : retourner la valeur brute comme string
            const value = this._data[fieldKey];
            return value === null || value === undefined ? null : String(value);
        }

        return FormatterClass.format(this._data[fieldKey], options);
    }

    /**
     * Génère une cellule pour une propriété en utilisant le formatter correspondant
     * @param {string} fieldKey - Clé du champ
     * @param {Object} [options={}] - Options (size, context, config, ctx, etc.)
     * @returns {Object|null} Cell object {type, value, params} ou null si valeur invalide
     */
    toCell(fieldKey, options = {}) {
        // Vérifier si la clé existe dans _data (même si la valeur est null/undefined)
        // Certains champs peuvent être null mais doivent quand même être formatés
        if (!(fieldKey in this._data)) {
            return null;
        }

        // Normaliser les options
        const normalizedOptions = {
            size: this._normalizeSize(options.size || 'md'),
            context: options.context || 'table',
            config: options.config || {},
            ctx: options.ctx || {},
            ...options,
        };

        // Vérifier le cache
        const cacheKey = this._getCacheKey(fieldKey, normalizedOptions);
        if (this._cellCache.has(cacheKey)) {
            return this._cellCache.get(cacheKey);
        }

        // Résoudre le format selon le descriptor et la taille
        const descriptor = normalizedOptions.config[fieldKey] || {};
        const format = this._resolveFormat(fieldKey, descriptor, normalizedOptions.context, normalizedOptions.size);

        // Vérifier si un composant personnalisé est défini dans le descriptor
        const cellConfig = descriptor.display?.cell;
        const hasCustomComponent = cellConfig?.component;

        // Essayer d'utiliser le formatter centralisé (sauf si un composant personnalisé est défini)
        if (!hasCustomComponent) {
            const FormatterClass = getFormatter(fieldKey);
            if (FormatterClass && typeof FormatterClass.toCell === 'function') {
                try {
                    const rawValue = this._data[fieldKey];
                    const cell = FormatterClass.toCell(rawValue, {
                        ...normalizedOptions,
                        format,
                    });
                    
                    if (cell && cell.type) {
                        // Ajouter la configuration du composant personnalisé si elle existe
                        if (cellConfig) {
                            cell.params = cell.params || {};
                            cell.params.component = cellConfig.component;
                            cell.params.componentProps = cellConfig.props || {};
                            cell.params.passEntity = cellConfig.passEntity || false;
                            cell.params.passValue = cellConfig.passValue !== false; // true par défaut
                        }
                        
                        // Mettre en cache
                        this._cellCache.set(cacheKey, cell);
                        return cell;
                    }
                } catch (e) {
                    // En cas d'erreur dans le formatter, logger et continuer avec le fallback
                    if (process.env.NODE_ENV !== 'production') {
                        console.warn(`[BaseModel] Formatter error for fieldKey="${fieldKey}":`, e);
                    }
                }
            }
        }

        // Essayer une méthode spécifique du modèle (pour les champs personnalisés)
        const specificMethod = `_to${this._capitalize(fieldKey)}Cell`;
        if (typeof this[specificMethod] === 'function') {
            const cell = this[specificMethod](format, normalizedOptions.size, normalizedOptions);
            if (cell) {
                // Ajouter la configuration du composant personnalisé si elle existe
                if (cellConfig) {
                    cell.params = cell.params || {};
                    cell.params.component = cellConfig.component;
                    cell.params.componentProps = cellConfig.props || {};
                    cell.params.passEntity = cellConfig.passEntity || false;
                    cell.params.passValue = cellConfig.passValue !== false; // true par défaut
                }
                
                this._cellCache.set(cacheKey, cell);
                return cell;
            }
        }

        // Fallback : cellule par défaut
        const defaultCell = this._toDefaultCell(fieldKey, format, normalizedOptions.size, normalizedOptions);
        
        // Ajouter la configuration du composant personnalisé si elle existe
        if (cellConfig) {
            defaultCell.params = defaultCell.params || {};
            defaultCell.params.component = cellConfig.component;
            defaultCell.params.componentProps = cellConfig.props || {};
            defaultCell.params.passEntity = cellConfig.passEntity || false;
            defaultCell.params.passValue = cellConfig.passValue !== false; // true par défaut
        }
        
        this._cellCache.set(cacheKey, defaultCell);
        return defaultCell;
    }

    /**
     * Résout le format selon le descriptor et la taille
     * @private
     * @param {string} fieldKey - Clé du champ
     * @param {Object} descriptor - Descriptor du champ
     * @param {string} context - Contexte (table, form, etc.)
     * @param {string} size - Taille d'écran (xs, sm, md, lg, xl)
     * @returns {Object} Format {mode, truncate, etc.}
     */
    _resolveFormat(fieldKey, descriptor, context, size) {
        const viewCfg = descriptor?.display?.views?.[context] || {};
        const sizeCfg = descriptor?.display?.sizes?.[size] || {};
        return {
            mode: viewCfg?.mode || sizeCfg?.mode || null,
            truncate: viewCfg?.truncate || sizeCfg?.truncate || null,
        };
    }

    /**
     * Normalise la taille d'écran
     * @private
     * @param {string} size - Taille d'écran (xs, sm, md, lg, xl, auto)
     * @returns {string} Taille normalisée (xs, sm, md, lg, xl)
     */
    _normalizeSize(size) {
        if (!size || size === 'auto') {
            return 'md';
        }
        const validSizes = ['xs', 'sm', 'md', 'lg', 'xl'];
        return validSizes.includes(size) ? size : 'md';
    }

    /**
     * Génère une cellule par défaut (texte simple)
     * @private
     * @param {string} fieldKey - Clé du champ
     * @param {Object} format - Format résolu
     * @param {string} size - Taille d'écran
     * @param {Object} options - Options
     * @returns {Object} Cell object
     */
    _toDefaultCell(fieldKey, format, size, options) {
        const value = this._data[fieldKey];
        const text = value === null || value === undefined || value === '' ? '-' : String(value);
        
        return {
            type: 'text',
            value: text,
            params: {
                sortValue: value,
                searchValue: text === '-' ? '' : text,
                filterValue: value,
            },
        };
    }

    /**
     * Génère une clé de cache pour une cellule
     * @private
     * @param {string} fieldKey - Clé du champ
     * @param {Object} options - Options
     * @returns {string} Clé de cache
     */
    _getCacheKey(fieldKey, options) {
        // Créer une clé basée sur fieldKey + size + context
        const keyParts = [
            fieldKey,
            options.size || 'md',
            options.context || 'table',
        ];
        return keyParts.join('|');
    }

    /**
     * Invalide le cache des cellules
     * @param {string} [fieldKey] - Clé du champ spécifique (optionnel, invalide tout si non fourni)
     */
    invalidateCache(fieldKey = null) {
        if (fieldKey) {
            // Invalider uniquement les cellules pour ce champ
            const keysToDelete = [];
            for (const key of this._cellCache.keys()) {
                if (key.startsWith(`${fieldKey}|`)) {
                    keysToDelete.push(key);
                }
            }
            keysToDelete.forEach(key => this._cellCache.delete(key));
        } else {
            // Invalider tout le cache
            this._cellCache.clear();
        }
    }

    /**
     * Capitalise la première lettre (helper)
     * @private
     * @param {string} str - Chaîne à capitaliser
     * @returns {string} Chaîne capitalisée
     */
    _capitalize(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // ============================================
    // MÉTHODES DE CONVENANCE (pour compatibilité et lisibilité)
    // ============================================

    /**
     * Vérifie si l'entité a une rareté
     * @returns {boolean}
     */
    hasRarity() {
        return this.has('rarity');
    }

    /**
     * Formate la rareté
     * @param {Object} [options={}] - Options de formatage
     * @returns {string|null}
     */
    formatRarity(options = {}) {
        return this.format('rarity', options);
    }

    /**
     * Génère une cellule pour la rareté
     * @param {Object} [options={}] - Options
     * @returns {Object|null}
     */
    toRarityCell(options = {}) {
        return this.toCell('rarity', options);
    }

    /**
     * Vérifie si l'entité a un niveau
     * @returns {boolean}
     */
    hasLevel() {
        return this.has('level');
    }

    /**
     * Formate le niveau
     * @param {Object} [options={}] - Options de formatage
     * @returns {string|null}
     */
    formatLevel(options = {}) {
        return this.format('level', options);
    }

    /**
     * Génère une cellule pour le niveau
     * @param {Object} [options={}] - Options
     * @returns {Object|null}
     */
    toLevelCell(options = {}) {
        return this.toCell('level', options);
    }

    /**
     * Vérifie si l'entité a une visibilité
     * @returns {boolean}
     */
    hasVisibility() {
        return this.has('visibility');
    }

    /**
     * Formate la visibilité
     * @param {Object} [options={}] - Options de formatage
     * @returns {string|null}
     */
    formatVisibility(options = {}) {
        return this.format('visibility', options);
    }

    /**
     * Génère une cellule pour la visibilité
     * @param {Object} [options={}] - Options
     * @returns {Object|null}
     */
    toVisibilityCell(options = {}) {
        return this.toCell('visibility', options);
    }
}

export default BaseModel;

