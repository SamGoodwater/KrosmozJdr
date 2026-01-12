/**
 * createEntityAdapter — Crée un adapter générique pour transformer les réponses backend en TableResponse
 *
 * @description
 * Fonction factory qui génère un adapter pour une entité donnée.
 * L'adapter transforme `{ meta, entities }` en `{ meta, rows }` conforme à TanStackTable v2.
 *
 * **Gestion automatique :**
 * - Si un mapper existe (ex: ResourceMapper), l'utilise pour transformer les données
 * - Sinon, utilise directement le constructeur du modèle
 *
 * @example
 * const adapter = createEntityAdapter(Resource, ResourceMapper);
 * const result = adapter({ meta: {...}, entities: [...] });
 *
 * @param {Class} ModelClass - Classe du modèle (ex: Resource, Item, Monster)
 * @param {Class|null} [MapperClass] - Classe du mapper optionnel (ex: ResourceMapper)
 * @returns {(payload:any) => ({meta:any, rows:any[]})} Fonction adapter
 */
export function createEntityAdapter(ModelClass, MapperClass = null) {
  if (!ModelClass) {
    throw new Error('createEntityAdapter: ModelClass est obligatoire');
  }

  /**
   * Adapter: backend `{ meta, entities }` -> frontend `{ meta, rows }`
   *
   * @param {any} payload - Réponse backend avec meta et entities
   * @returns {{meta:any, rows:any[]}} Réponse formatée pour TanStackTable
   */
  return function adaptEntitiesTableResponse(payload) {
    const meta = payload?.meta || {};
    const entities = Array.isArray(payload?.entities) ? payload.entities : [];

    // Si un mapper est disponible et a la méthode fromApiArray, l'utiliser
    let modelInstances;
    if (MapperClass && typeof MapperClass.fromApiArray === 'function') {
      modelInstances = MapperClass.fromApiArray(entities);
    } else {
      // Sinon, utiliser directement le constructeur du modèle
      modelInstances = entities.map((entityData) => new ModelClass(entityData));
    }

    const rows = modelInstances.map((entity) => {
      return {
        id: entity.id,
        // Les cellules seront générées à la volée par le composant tableau via entity.toCell()
        // On ne pré-génère plus les cellules ici
        cells: {},
        rowParams: {
          entity, // Passer l'instance du modèle pour génération des cellules
        },
      };
    });

    return { meta, rows };
  };
}

export default createEntityAdapter;
