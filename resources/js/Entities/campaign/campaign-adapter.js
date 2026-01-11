/**
 * Campaign adapter — Version simplifiée
 *
 * @description
 * Transforme une réponse backend "entities" en `TableResponse` conforme à TanStackTable v2.
 * 
 * ⚠️ IMPORTANT : Les cellules ne sont plus pré-générées ici. Elles sont générées à la volée
 * par le composant tableau via `campaign.toCell()` selon la taille d'écran (xs-xl).
 *
 * @example
 * <EntityTanStackTable :response-adapter="adaptCampaignEntitiesTableResponse" />
 */

import { Campaign } from "@/Models/Entity/Campaign";

/**
 * Adapter: backend `{ meta, entities }` -> frontend `{ meta, rows }`
 *
 * @param {any} payload - Réponse backend avec meta et entities
 * @returns {{meta:any, rows:any[]}} Réponse formatée pour TanStackTable
 */
export function adaptCampaignEntitiesTableResponse(payload) {
  const meta = payload?.meta || {};
  const entities = Array.isArray(payload?.entities) ? payload.entities : [];

  // Transformer les entités brutes en instances de Campaign
  const rows = entities.map((entityData) => {
    const campaign = new Campaign(entityData);
    
    return {
      id: campaign.id,
      // Les cellules seront générées à la volée par le composant tableau via campaign.toCell()
      // On ne pré-génère plus les cellules ici
      cells: {},
      rowParams: { 
        entity: campaign, // Passer l'instance Campaign pour génération des cellules
      },
    };
  });

  return { meta, rows };
}

export default adaptCampaignEntitiesTableResponse;
