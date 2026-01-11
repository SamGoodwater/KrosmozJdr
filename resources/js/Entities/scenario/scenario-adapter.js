/**
 * Scenario adapter — Version simplifiée
 *
 * @description
 * Transforme une réponse backend "entities" en `TableResponse` conforme à TanStackTable v2.
 * 
 * ⚠️ IMPORTANT : Les cellules ne sont plus pré-générées ici. Elles sont générées à la volée
 * par le composant tableau via `scenario.toCell()` selon la taille d'écran (xs-xl).
 *
 * @example
 * <EntityTanStackTable :response-adapter="adaptScenarioEntitiesTableResponse" />
 */

import { Scenario } from "@/Models/Entity/Scenario";

/**
 * Adapter: backend `{ meta, entities }` -> frontend `{ meta, rows }`
 *
 * @param {any} payload - Réponse backend avec meta et entities
 * @returns {{meta:any, rows:any[]}} Réponse formatée pour TanStackTable
 */
export function adaptScenarioEntitiesTableResponse(payload) {
  const meta = payload?.meta || {};
  const entities = Array.isArray(payload?.entities) ? payload.entities : [];

  // Transformer les entités brutes en instances de Scenario
  const rows = entities.map((entityData) => {
    const scenario = new Scenario(entityData);
    
    return {
      id: scenario.id,
      // Les cellules seront générées à la volée par le composant tableau via scenario.toCell()
      // On ne pré-génère plus les cellules ici
      cells: {},
      rowParams: { 
        entity: scenario, // Passer l'instance Scenario pour génération des cellules
      },
    };
  });

  return { meta, rows };
}

export default adaptScenarioEntitiesTableResponse;
