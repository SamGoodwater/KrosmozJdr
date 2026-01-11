/**
 * Resource adapter — Version simplifiée
 *
 * @description
 * Transforme une réponse backend "entities" en `TableResponse` conforme à TanStackTable v2.
 * 
 * ⚠️ IMPORTANT : Les cellules ne sont plus pré-générées ici. Elles sont générées à la volée
 * par le composant tableau via `resource.toCell()` selon la taille d'écran (xs-xl).
 *
 * @example
 * <EntityTanStackTable :response-adapter="adaptResourceEntitiesTableResponse" />
 */

import { Resource } from "@/Models/Entity/Resource";

/**
 * Adapter: backend `{ meta, entities }` -> frontend `{ meta, rows }`
 *
 * @param {any} payload - Réponse backend avec meta et entities
 * @returns {{meta:any, rows:any[]}} Réponse formatée pour TanStackTable
 */
export function adaptResourceEntitiesTableResponse(payload) {
  const meta = payload?.meta || {};
  const entities = Array.isArray(payload?.entities) ? payload.entities : [];

  // Transformer les entités brutes en instances de Resource
  const rows = entities.map((entityData) => {
    const resource = new Resource(entityData);
    
    return {
      id: resource.id,
      // Les cellules seront générées à la volée par le composant tableau via resource.toCell()
      // On ne pré-génère plus les cellules ici
      cells: {},
      rowParams: { 
        entity: resource, // Passer l'instance Resource pour génération des cellules
      },
    };
  });

  return { meta, rows };
}

export default adaptResourceEntitiesTableResponse;
