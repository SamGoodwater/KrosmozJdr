/**
 * Attribute adapter — Version simplifiée
 *
 * @description
 * Transforme une réponse backend "entities" en `TableResponse` conforme à TanStackTable v2.
 * 
 * ⚠️ IMPORTANT : Les cellules ne sont plus pré-générées ici. Elles sont générées à la volée
 * par le composant tableau via `attribute.toCell()` selon la taille d'écran (xs-xl).
 *
 * @example
 * <EntityTanStackTable :response-adapter="adaptAttributeEntitiesTableResponse" />
 */

import { Attribute } from "@/Models/Entity/Attribute";

/**
 * Adapter: backend `{ meta, entities }` -> frontend `{ meta, rows }`
 *
 * @param {any} payload - Réponse backend avec meta et entities
 * @returns {{meta:any, rows:any[]}} Réponse formatée pour TanStackTable
 */
export function adaptAttributeEntitiesTableResponse(payload) {
  const meta = payload?.meta || {};
  const entities = Array.isArray(payload?.entities) ? payload.entities : [];

  // Transformer les entités brutes en instances de Attribute
  const rows = entities.map((entityData) => {
    const attribute = new Attribute(entityData);
    
    return {
      id: attribute.id,
      // Les cellules seront générées à la volée par le composant tableau via attribute.toCell()
      // On ne pré-génère plus les cellules ici
      cells: {},
      rowParams: { 
        entity: attribute, // Passer l'instance Attribute pour génération des cellules
      },
    };
  });

  return { meta, rows };
}

export default adaptAttributeEntitiesTableResponse;
