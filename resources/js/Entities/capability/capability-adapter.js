/**
 * Capability adapter — Version simplifiée
 *
 * @description
 * Transforme une réponse backend "entities" en `TableResponse` conforme à TanStackTable v2.
 * 
 * ⚠️ IMPORTANT : Les cellules ne sont plus pré-générées ici. Elles sont générées à la volée
 * par le composant tableau via `capability.toCell()` selon la taille d'écran (xs-xl).
 *
 * @example
 * <EntityTanStackTable :response-adapter="adaptCapabilityEntitiesTableResponse" />
 */

import { Capability } from "@/Models/Entity/Capability";

/**
 * Adapter: backend `{ meta, entities }` -> frontend `{ meta, rows }`
 *
 * @param {any} payload - Réponse backend avec meta et entities
 * @returns {{meta:any, rows:any[]}} Réponse formatée pour TanStackTable
 */
export function adaptCapabilityEntitiesTableResponse(payload) {
  const meta = payload?.meta || {};
  const entities = Array.isArray(payload?.entities) ? payload.entities : [];

  // Transformer les entités brutes en instances de Capability
  const rows = entities.map((entityData) => {
    const capability = new Capability(entityData);
    
    return {
      id: capability.id,
      // Les cellules seront générées à la volée par le composant tableau via capability.toCell()
      // On ne pré-génère plus les cellules ici
      cells: {},
      rowParams: { 
        entity: capability, // Passer l'instance Capability pour génération des cellules
      },
    };
  });

  return { meta, rows };
}

export default adaptCapabilityEntitiesTableResponse;
