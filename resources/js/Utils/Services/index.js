/**
 * Export centralisé de tous les services
 * 
 * @description
 * Point d'entrée unique pour importer tous les services du projet.
 * 
 * @example
 * import { TransformService, BaseMapper } from '@/Utils/Services';
 */
import { TransformService } from './TransformService';
import { BaseMapper } from './BaseMapper';
import * as Mappers from './Mappers';

// Exports nommés
export { TransformService } from './TransformService';
export { BaseMapper } from './BaseMapper';
export * from './Mappers';

// Export par défaut pour compatibilité
export default {
    TransformService,
    BaseMapper,
    ...Mappers,
};

