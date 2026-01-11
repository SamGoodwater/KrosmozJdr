/**
 * Panoply adapter — Version simplifiée
 *
 * @description
 * Transforme une réponse backend "entities" en `TableResponse` conforme à TanStackTable v2.
 * 
 * ⚠️ IMPORTANT : Les cellules ne sont plus pré-générées ici. Elles sont générées à la volée
 * par le composant tableau via `panoply.toCell()` selon la taille d'écran (xs-xl).
 *
 * @example
 * <EntityTanStackTable :response-adapter="adaptPanoplyEntitiesTableResponse" />
 */

import { Panoply } from "@/Models/Entity/Panoply";

/**
 * Adapter: backend `{ meta, entities }` -> frontend `{ meta, rows }`
 *
 * @param {any} payload - Réponse backend avec meta et entities
 * @returns {{meta:any, rows:any[]}} Réponse formatée pour TanStackTable
 */
export function adaptPanoplyEntitiesTableResponse(payload) {
  const meta = payload?.meta || {};
  const entities = Array.isArray(payload?.entities) ? payload.entities : [];

  // Transformer les entités brutes en instances de Panoply
  const rows = entities.map((entityData) => {
    const panoply = new Panoply(entityData);
    
    return {
      id: panoply.id,
      // Les cellules seront générées à la volée par le composant tableau via panoply.toCell()
      // On ne pré-génère plus les cellules ici
      cells: {},
      rowParams: { 
        entity: panoply, // Passer l'instance Panoply pour génération des cellules
      },
    };
  });

  return { meta, rows };
}

export default adaptPanoplyEntitiesTableResponse;
