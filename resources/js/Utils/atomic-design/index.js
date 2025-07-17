/**
 * index.js — Point d'entrée pour les helpers atomic-design
 *
 * @description
 * Fichier d'index qui exporte tous les helpers des différents managers
 * pour faciliter les imports dans les composants.
 *
 * @example
 * import { 
 *   getInputProps, 
 *   validateLabel, 
 *   createValidation 
 * } from '@/Utils/atomic-design';
 */

// uiHelper.js - Helpers universels
export * from './uiHelper';

// atomManager.js - Props et attributs des atoms
export * from './atomManager';

// labelManager.js - Gestion des labels
export * from './labelManager';

// validationManager.js - Gestion de la validation
export * from './validationManager'; 