/**
 * Creature adapter — Version simplifiée
 *
 * @description
 * Transforme une réponse backend "entities" en `TableResponse` conforme à TanStackTable v2.
 * 
 * ⚠️ IMPORTANT : Les cellules ne sont plus pré-générées ici. Elles sont générées à la volée
 * par le composant tableau via `creature.toCell()` selon la taille d'écran (xs-xl).
 *
 * @example
 * <EntityTanStackTable :response-adapter="adaptCreatureEntitiesTableResponse" />
 */

import { Creature } from "@/Models/Entity/Creature";

/**
 * Adapter: backend `{ meta, entities }` -> frontend `{ meta, rows }`
 *
 * @param {any} payload - Réponse backend avec meta et entities
 * @returns {{meta:any, rows:any[]}} Réponse formatée pour TanStackTable
 */
export function adaptCreatureEntitiesTableResponse(payload) {
  const meta = payload?.meta || {};
  const entities = Array.isArray(payload?.entities) ? payload.entities : [];

  // Transformer les entités brutes en instances de Creature
  const rows = entities.map((entityData) => {
    const creature = new Creature(entityData);
    
    return {
      id: creature.id,
      // Les cellules seront générées à la volée par le composant tableau via creature.toCell()
      // On ne pré-génère plus les cellules ici
      cells: {},
      rowParams: { 
        entity: creature, // Passer l'instance Creature pour génération des cellules
      },
    };
  });

  return { meta, rows };
}

export default adaptCreatureEntitiesTableResponse;
