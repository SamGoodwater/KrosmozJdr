/**
 * Mapper pour transformer les données Section (backend) en Model (frontend)
 * 
 * @description
 * Transforme les données brutes du backend (Resource/Entity) en modèle Section normalisé.
 * Gère la normalisation des données, la résolution des relations, et la conversion des types/enums.
 * 
 * @example
 * import { SectionMapper } from '@/Utils/Services/Mappers';
 * const sectionModel = SectionMapper.mapToModel(rawSectionData);
 */
import { Section } from '@/Models';
import { BaseMapper } from '../BaseMapper';
import { PageMapper } from './PageMapper';

export class SectionMapper extends BaseMapper {
    /**
     * Mappe les données brutes en modèle Section
     * 
     * @param {Object} rawData - Données brutes du backend (Resource ou Entity)
     * @returns {Section|null} Instance Section normalisée
     * 
     * @static
     */
    static mapToModel(rawData) {
        if (!rawData) {
            return null;
        }

        // Si c'est déjà une instance Section, la retourner telle quelle
        if (rawData instanceof Section) {
            return rawData;
        }

        // Normaliser les données brutes
        const normalized = this.normalizeSectionData(rawData);

        // Créer l'instance Section
        return new Section(normalized);
    }

    /**
     * Normalise les données brutes d'une section
     * 
     * @param {Object} rawData - Données brutes (peut être un Proxy Vue/Inertia)
     * @returns {Object} Données normalisées
     * 
     * @protected
     * @static
     */
    static normalizeSectionData(rawData) {
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

        // Normaliser la relation page (si présente)
        const pageData = extract('page');
        const page = pageData ? PageMapper.mapToModel(pageData) : null;

        // Normaliser les relations pivots (users, files)
        const users = this.normalizePivot(extract('users', []), {
            extractIds: false, // Garder les données pivot si présentes
            idKey: 'id'
        });

        const files = this.normalizePivot(extract('files', []), {
            extractIds: false,
            idKey: 'id'
        });

        // Normaliser createdBy (relation)
        const createdBy = extract('createdBy') || extract('created_by_user');

        return {
            // Propriétés de base
            id: extract('id'),
            page_id: extract('page_id'),
            title: extract('title', null),
            slug: extract('slug', null),
            order: extract('order', 0),
            
            // Template/Type (gérer la compatibilité avec 'type' et 'template')
            template: extract('template') || extract('type', 'text'),
            
            // Settings et data
            settings: extract('settings', {}),
            data: extract('data', {}),
            
            // State + niveaux d'accès
            state: extract('state', 'draft'),
            read_level: extract('read_level', 0),
            write_level: extract('write_level', 4),
            
            // Timestamps
            created_by: extract('created_by', null),
            created_at: extract('created_at', null),
            updated_at: extract('updated_at', null),
            deleted_at: extract('deleted_at', null),
            
            // Relations
            page: page,
            users: users,
            files: files,
            createdBy: createdBy,
            
            // Permissions
            can: can,
        };
    }

    /**
     * Mappe les données d'une section pour un formulaire
     * 
     * @param {Section|Object} section - Section (Model ou données brutes)
     * @returns {Object} Données formatées pour un formulaire
     * 
     * @static
     */
    static mapToFormData(section) {
        const sectionModel = section instanceof Section ? section : this.mapToModel(section);

        if (!sectionModel) {
            return null;
        }

        return {
            page_id: sectionModel.pageId,
            title: sectionModel.title || '',
            slug: sectionModel.slug || '',
            order: sectionModel.order || 0,
            template: sectionModel.template,
            settings: sectionModel.settings || {},
            data: sectionModel.data || {},
            read_level: sectionModel.readLevel ?? 0,
            write_level: sectionModel.writeLevel ?? 4,
            state: this.fromEnum(sectionModel.state) || 'draft',
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
                // Certains champs peuvent être vides (comme title, slug)
                if (['title', 'slug'].includes(key)) {
                    cleaned[key] = null;
                }
                return;
            }

            // Traiter les objets (settings, data)
            if (key === 'settings' || key === 'data') {
                if (typeof value === 'object' && Object.keys(value).length > 0) {
                    cleaned[key] = value;
                }
                return;
            }

            // Traiter les tableaux (users, files pour les relations pivots)
            if (Array.isArray(value) && value.length > 0) {
                cleaned[key] = value;
                return;
            }

            // Valeurs simples
            cleaned[key] = value;
        });

        return cleaned;
    }
}

export default SectionMapper;

