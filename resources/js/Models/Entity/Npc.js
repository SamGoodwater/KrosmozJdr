/**
 * Modèle Npc pour le frontend
 * 
 * @description
 * Classe pour normaliser et manipuler les données de npc côté frontend.
 * 
 * @example
 * const npc = new Npc(props.npc);
 * console.log(npc.creature?.name); // Accès via la relation creature
 */
import { BaseModel } from '../BaseModel';

export class Npc extends BaseModel {
    // ============================================
    // PROPRIÉTÉS DE BASE
    // ============================================

    get creatureId() {
        return this._data.creature_id || null;
    }

    get story() {
        return this._data.story || null;
    }

    get historical() {
        return this._data.historical || null;
    }

    get age() {
        return this._data.age || null;
    }

    get size() {
        return this._data.size || null;
    }

    get breedId() {
        return this._data.breed_id || null;
    }

    get specializationId() {
        return this._data.specialization_id || null;
    }

    // ============================================
    // RELATIONS
    // ============================================

    get creature() {
        return this._data.creature || null;
    }

    get breed() {
        return this._data.breed || null;
    }

    get specialization() {
        return this._data.specialization || null;
    }

    get panoplies() {
        return this._data.panoplies || [];
    }

    get scenarios() {
        return this._data.scenarios || [];
    }

    get campaigns() {
        return this._data.campaigns || [];
    }

    get shop() {
        return this._data.shop || null;
    }

    // ============================================
    // FORMATAGE DES CELLULES (surcharge pour champs spécifiques)
    // ============================================

    /**
     * Génère une cellule pour un champ (surcharge pour gérer les champs spécifiques à NPC)
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

        // Sinon, gérer les champs spécifiques à NPC
        const { size = 'md', format = {} } = options;
        
        switch (fieldKey) {
            case 'creature_name':
            case 'creatureName':
                return this._toCreatureNameCell(format, size, options);
            case 'breed':
            case 'breed_id':
            case 'classe':
            case 'classe_id':
                return this._toBreedCell(format, size, options);
            case 'specialization':
            case 'specialization_id':
                return this._toSpecializationCell(format, size, options);
            case 'story':
                return this._toStoryCell(format, size, options);
            case 'historical':
                return this._toHistoricalCell(format, size, options);
            case 'age':
                return this._toAgeCell(format, size, options);
            case 'size':
                return this._toSizeCell(format, size, options);
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
     * Génère une cellule pour le nom de la créature (lien vers la page de détail)
     * @private
     */
    _toCreatureNameCell(format, size, options) {
        const creature = this.creature;
        if (!creature) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                },
            };
        }

        const name = creature.name || '-';
        const href = options.href || `/creatures/${creature.id}`;
        
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
     * Génère une cellule pour la breed (affichée « Classe »)
     * @private
     */
    _toBreedCell(format, size, options) {
        const breed = this.breed;

        if (!breed) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                },
            };
        }

        const breedName = breed.name || breed.label || '-';

        return {
            type: 'text',
            value: breedName,
            params: {
                sortValue: breedName,
                searchValue: breedName,
            },
        };
    }

    /**
     * Génère une cellule pour la spécialisation
     * @private
     */
    _toSpecializationCell(format, size, options) {
        const specialization = this.specialization;
        
        if (!specialization) {
            return {
                type: 'text',
                value: '-',
                params: {
                    sortValue: '',
                    searchValue: '',
                },
            };
        }

        const specializationName = specialization.name || specialization.label || '-';
        
        return {
            type: 'text',
            value: specializationName,
            params: {
                sortValue: specializationName,
                searchValue: specializationName,
            },
        };
    }

    /**
     * Génère une cellule pour l'histoire
     * @private
     */
    _toStoryCell(format, size, options) {
        const story = this.story || '-';
        
        return {
            type: 'text',
            value: story,
            params: {
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 30 : (size === 'md' ? 50 : null)),
                searchValue: story === '-' ? '' : story,
                sortValue: story,
            },
        };
    }

    /**
     * Génère une cellule pour l'historique
     * @private
     */
    _toHistoricalCell(format, size, options) {
        const historical = this.historical || '-';
        
        return {
            type: 'text',
            value: historical,
            params: {
                truncate: format.truncate || (size === 'xs' || size === 'sm' ? 30 : (size === 'md' ? 50 : null)),
                searchValue: historical === '-' ? '' : historical,
                sortValue: historical,
            },
        };
    }

    /**
     * Génère une cellule pour l'âge
     * @private
     */
    _toAgeCell(format, size, options) {
        const age = this.age || '-';
        
        return {
            type: 'text',
            value: age,
            params: {
                sortValue: age === '-' ? '' : age,
                searchValue: age === '-' ? '' : age,
            },
        };
    }

    /**
     * Génère une cellule pour la taille
     * @private
     */
    _toSizeCell(format, size, options) {
        const sizeValue = this.size || '-';
        
        return {
            type: 'text',
            value: sizeValue,
            params: {
                sortValue: sizeValue === '-' ? '' : sizeValue,
                searchValue: sizeValue === '-' ? '' : sizeValue,
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
            creature_id: this.creatureId,
            story: this.story,
            historical: this.historical,
            age: this.age,
            size: this.size,
            classe_id: this.classeId,
            specialization_id: this.specializationId
        };
    }
}

export default Npc;
