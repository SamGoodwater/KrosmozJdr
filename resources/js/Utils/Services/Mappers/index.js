/**
 * Export centralisé de tous les mappers
 * 
 * @description
 * Point d'entrée unique pour importer tous les mappers du projet.
 * 
 * @example
 * import { SectionMapper, PageMapper } from '@/Utils/Services/Mappers';
 */
import { SectionMapper } from './SectionMapper';
import { PageMapper } from './PageMapper';
import { BaseMapper } from '../BaseMapper';

// Exports nommés
export { SectionMapper } from './SectionMapper';
export { PageMapper } from './PageMapper';
export { BaseMapper } from '../BaseMapper';

// Export par défaut pour compatibilité
export default {
    SectionMapper,
    PageMapper,
    BaseMapper,
};

