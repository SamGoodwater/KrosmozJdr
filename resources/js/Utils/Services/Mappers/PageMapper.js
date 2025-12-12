/**
 * Mapper pour transformer les données Page (backend) en Model (frontend)
 * 
 * @description
 * Transforme les données brutes du backend (Resource/Entity) en modèle Page normalisé.
 * Gère la normalisation des données, la résolution des relations, et la conversion des types/enums.
 * 
 * @example
 * import { PageMapper } from '@/Utils/Services/Mappers';
 * const pageModel = PageMapper.mapToModel(rawPageData);
 */
import { Page } from '@/Models';
import { BaseMapper } from '../BaseMapper';
import { SectionMapper } from './SectionMapper';

export class PageMapper extends BaseMapper {
    /**
     * Mappe les données brutes en modèle Page
     * 
     * @param {Object} rawData - Données brutes du backend (Resource ou Entity)
     * @returns {Page|null} Instance Page normalisée
     * 
     * @static
     */
    static mapToModel(rawData) {
        if (!rawData) {
            return null;
        }

        // Si c'est déjà une instance Page, la retourner telle quelle
        if (rawData instanceof Page) {
            return rawData;
        }

        // Normaliser les données brutes
        const normalized = this.normalizePageData(rawData);

        // Créer l'instance Page
        return new Page(normalized);
    }

    /**
     * Normalise les données brutes d'une page
     * 
     * @param {Object} rawData - Données brutes (peut être un Proxy Vue/Inertia)
     * @returns {Object} Données normalisées
     * 
     * @protected
     * @static
     */
    static normalizePageData(rawData) {
        if (!rawData) {
            return null;
        }

        // Utiliser la méthode normalize de BaseMapper
        const normalized = this.normalize(rawData);

        if (!normalized) {
            return null;
        }

        // Extraire les valeurs avec fallback
        const extract = (key, defaultValue = null) => {
            return this.extractValue(normalized, [key, `data.${key}`], defaultValue);
        };

        // Normaliser les permissions
        const can = this.normalizePermissions(rawData);

        // Normaliser les relations
        const parent = extract('parent') ? this.mapToModel(extract('parent')) : null;
        const children = this.normalizeRelation(extract('children', []), PageMapper, true);
        const sections = this.normalizeRelation(extract('sections', []), SectionMapper, true);

        // Normaliser les relations pivots
        const users = this.normalizePivot(extract('users', []), {
            extractIds: false,
            idKey: 'id'
        });

        const campaigns = this.normalizePivot(extract('campaigns', []), {
            extractIds: false,
            idKey: 'id'
        });

        const scenarios = this.normalizePivot(extract('scenarios', []), {
            extractIds: false,
            idKey: 'id'
        });

        // Normaliser createdBy
        const createdBy = extract('createdBy') || extract('created_by_user');

        return {
            // Propriétés de base
            id: extract('id'),
            title: extract('title', null),
            slug: extract('slug', null),
            
            // Enums
            is_visible: extract('is_visible', 'guest'),
            can_edit_role: extract('can_edit_role', 'admin'),
            state: extract('state', 'draft'),
            
            // Menu
            in_menu: extract('in_menu', false),
            parent_id: extract('parent_id', null),
            menu_order: extract('menu_order', 0),
            
            // Timestamps
            created_by: extract('created_by', null),
            created_at: extract('created_at', null),
            updated_at: extract('updated_at', null),
            deleted_at: extract('deleted_at', null),
            
            // Relations
            parent: parent,
            children: children,
            sections: sections,
            users: users,
            campaigns: campaigns,
            scenarios: scenarios,
            createdBy: createdBy,
            
            // Permissions
            can: can,
        };
    }

    /**
     * Mappe les données d'une page pour un formulaire
     * 
     * @param {Page|Object} page - Page (Model ou données brutes)
     * @returns {Object} Données formatées pour un formulaire
     * 
     * @static
     */
    static mapToFormData(page) {
        const pageModel = page instanceof Page ? page : this.mapToModel(page);

        if (!pageModel) {
            return null;
        }

        return {
            title: pageModel.title || '',
            slug: pageModel.slug || '',
            is_visible: this.fromEnum(pageModel.isVisible) || 'guest',
            can_edit_role: this.fromEnum(pageModel.canEditRole) || 'admin',
            in_menu: pageModel.inMenu ?? true,
            state: this.fromEnum(pageModel.state) || 'draft',
            parent_id: pageModel.parentId || null,
            menu_order: pageModel.menuOrder || 0,
        };
    }

    /**
     * Mappe les données d'un formulaire pour l'envoi au backend
     * 
     * @param {Object} formData - Données du formulaire
     * @returns {Object} Données formatées pour le backend
     * 
     * @static
     */
    static mapFromFormData(formData) {
        if (!formData || typeof formData !== 'object') {
            return {};
        }

        // Nettoyer les valeurs vides
        const cleaned = {};

        Object.keys(formData).forEach(key => {
            const value = formData[key];
            
            // Ignorer les valeurs null, undefined, ou chaînes vides (sauf pour certains champs)
            if (value === null || value === undefined || value === '') {
                if (['title', 'slug'].includes(key)) {
                    cleaned[key] = null;
                }
                return;
            }

            // Valeurs simples
            cleaned[key] = value;
        });

        return cleaned;
    }
}

export default PageMapper;

