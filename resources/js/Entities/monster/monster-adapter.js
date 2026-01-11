/**
 * Monster adapter — Version simplifiée
 *
 * @description
 * Transforme une réponse backend "entities" en `TableResponse` conforme à TanStackTable v2.
 * 
 * ⚠️ IMPORTANT : Les cellules ne sont plus pré-générées ici. Elles sont générées à la volée
 * par le composant tableau via `monster.toCell()` selon la taille d'écran (xs-xl).
 *
 * @example
 * <EntityTanStackTable :response-adapter="adaptMonsterEntitiesTableResponse" />
 */

import { Monster } from "@/Models/Entity/Monster";

/**
 * Adapter: backend `{ meta, entities }` -> frontend `{ meta, rows }`
 *
 * @param {any} payload - Réponse backend avec meta et entities
 * @returns {{meta:any, rows:any[]}} Réponse formatée pour TanStackTable
 */
export function adaptMonsterEntitiesTableResponse(payload) {
  const meta = payload?.meta || {};
  const entities = Array.isArray(payload?.entities) ? payload.entities : [];

  // Transformer les entités brutes en instances de Monster
  const rows = entities.map((entityData) => {
    const monster = new Monster(entityData);
    
    return {
      id: monster.id,
      // Les cellules seront générées à la volée par le composant tableau via monster.toCell()
      // On ne pré-génère plus les cellules ici
      cells: {},
      rowParams: { 
        entity: monster, // Passer l'instance Monster pour génération des cellules
      },
    };
  });

  return { meta, rows };
}

export default adaptMonsterEntitiesTableResponse;
