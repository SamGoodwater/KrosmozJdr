import { Condition } from "@/Models/Entity/Condition";

export function adaptConditionEntitiesTableResponse(payload = {}) {
  const meta = payload.meta || {};
  const entities = Array.isArray(payload.entities) ? payload.entities : [];

  return {
    meta,
    rows: entities.map((raw) => {
      const entity = raw instanceof Condition ? raw : new Condition(raw);
      return {
        id: entity.id,
        cells: {},
        rowParams: { entity },
      };
    }),
  };
}

export default adaptConditionEntitiesTableResponse;
