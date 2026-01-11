/**
 * ResourceType adapter — Version simplifiée
 *
 * @description
 * Transforme une réponse backend "entities" en `TableResponse` conforme à TanStackTable v2.
 *
 * ⚠️ IMPORTANT : Les cellules ne sont plus pré-générées ici. Elles sont générées à la volée
 * par le composant tableau via `resourceType.toCell()` selon la taille d'écran (xs-xl).
 *
 * @example
 * <EntityTanStackTable :response-adapter="adaptResourceTypeEntitiesTableResponse" />
 */

import { ResourceType } from "@/Models/Entity/ResourceType";

/**
 * Adapter: backend `{ meta, entities }` -> frontend `{ meta, rows }`
 *
 * @param {any} payload - Réponse backend avec meta et entities
 * @returns {{meta:any, rows:any[]}} Réponse formatée pour TanStackTable
 */
export function adaptResourceTypeEntitiesTableResponse(payload) {
  const meta = payload?.meta || {};
  const entities = Array.isArray(payload?.entities) ? payload.entities : [];

  // Transformer les entités brutes en instances de ResourceType
  const rows = entities.map((entityData) => {
    const resourceType = new ResourceType(entityData);

    return {
      id: resourceType.id,
      // Les cellules seront générées à la volée par le composant tableau via resourceType.toCell()
      // On ne pré-génère plus les cellules ici
      cells: {},
      rowParams: {
        entity: resourceType, // Passer l'instance ResourceType pour génération des cellules
      },
    };
  });

  return { meta, rows };
}

export default adaptResourceTypeEntitiesTableResponse;


