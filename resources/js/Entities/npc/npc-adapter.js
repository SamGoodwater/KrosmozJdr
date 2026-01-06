/**
 * Npc adapter (Option B)
 *
 * @description
 * Transforme une réponse backend "entities" en `TableResponse` conforme à TanStackTable v2,
 * en générant les `cells` côté frontend à partir de `npc-descriptors`.
 */

import { DEFAULT_NPC_FIELD_VIEWS, getNpcFieldDescriptors } from "@/Entities/npc/npc-descriptors";
import { getTruncateClass, sizeToTruncateScale } from "@/Utils/entity/text-truncate";

const pad2 = (n) => String(n).padStart(2, "0");
const formatDateFr = (isoString) => {
  if (!isoString) return "-";
  const ms = Date.parse(String(isoString));
  if (!Number.isFinite(ms)) return "-";
  const d = new Date(ms);
  return `${pad2(d.getDate())}/${pad2(d.getMonth() + 1)}/${d.getFullYear()} ${pad2(d.getHours())}:${pad2(d.getMinutes())}`;
};

const npcShowHref = (id) => {
  try {
    return route("entities.npcs.show", { npc: id });
  } catch {
    return null;
  }
};

const resolveViewConfigFor = (descriptor, { view = "table" } = {}) => {
  const views = descriptor?.display?.views || DEFAULT_NPC_FIELD_VIEWS;
  const v = views?.[view] || null;
  if (v && typeof v === "object") return v;
  return { size: "normal" };
};

/**
 * Construit une Cell v2 depuis une entité Npc brute.
 *
 * @param {string} colId
 * @param {any} entity
 * @param {any} ctx
 * @param {{context?: "table"|"text"|"compact"|"minimal"|"extended", size?: "small"|"normal"|"large"}} [opts]
 * @returns {{type:string,value:any,params?:any}}
 */
export function buildNpcCell(colId, entity, ctx = {}, opts = {}) {
  const descriptors = getNpcFieldDescriptors(ctx);
  const d = descriptors[colId] || descriptors?.[colId?.replace(/-/g, "_")] || null;
  const context = opts?.context || "table";
  const viewCfg = resolveViewConfigFor(d, { view: context });
  const size = opts?.size || viewCfg?.size || "normal";
  const sizeCfg = d?.display?.sizes?.[size] || {};
  const mode = viewCfg?.mode || sizeCfg?.mode || null;
  const truncateScale = sizeToTruncateScale(size);

  // Baselines
  const id = entity?.id ?? null;

  if (colId === "creature_name") {
    const creature = entity?.creature || null;
    const name = creature?.name || "-";
    return {
      type: "route",
      value: name,
      params: {
        href: npcShowHref(id),
        tooltip: name === "-" ? "" : String(name),
        truncate: { context, scale: truncateScale },
        searchValue: name === "-" ? "" : String(name),
        sortValue: name === "-" ? "" : String(name),
      },
    };
  }

  if (colId === "classe" || colId === "classe_id") {
    const classe = entity?.classe || null;
    const classeName = classe?.name || "-";
    return {
      type: "text",
      value: classeName,
      params: {
        tooltip: classeName === "-" ? "" : classeName,
        truncate: { context, scale: truncateScale },
        searchValue: classeName === "-" ? "" : classeName,
        sortValue: classeName,
      },
    };
  }

  if (colId === "specialization" || colId === "specialization_id") {
    const spec = entity?.specialization || null;
    const specName = spec?.name || "-";
    return {
      type: "text",
      value: specName,
      params: {
        tooltip: specName === "-" ? "" : specName,
        truncate: { context, scale: truncateScale },
        searchValue: specName === "-" ? "" : specName,
        sortValue: specName,
      },
    };
  }

  if (colId === "story" || colId === "historical" || colId === "age" || colId === "size") {
    const v = entity?.[colId] ?? null;
    return {
      type: "text",
      value: v === null || typeof v === "undefined" || v === "" ? "-" : String(v),
      params: {
        sortValue: v === null || typeof v === "undefined" ? "" : String(v),
      },
    };
  }

  if (colId === "created_at") {
    const v = entity?.created_at ?? null;
    const label = v ? formatDateFr(v) : "-";
    const sortValue = v ? new Date(v).getTime() : 0;
    return {
      type: "text",
      value: label,
      params: {
        sortValue,
        searchValue: label === "-" ? "" : label,
      },
    };
  }

  if (colId === "updated_at") {
    const v = entity?.updated_at ?? null;
    const label = v ? formatDateFr(v) : "-";
    const sortValue = v ? new Date(v).getTime() : 0;
    return {
      type: "text",
      value: label,
      params: {
        sortValue,
        searchValue: label === "-" ? "" : label,
      },
    };
  }

  if (colId === "id") {
    const v = entity?.id ?? null;
    return {
      type: "text",
      value: v ? String(v) : "-",
      params: {
        sortValue: v ? Number(v) : 0,
      },
    };
  }

  // Fallback générique
  const rawValue = entity?.[colId] ?? null;
  return {
    type: "text",
    value: rawValue === null || rawValue === undefined ? "-" : String(rawValue),
    params: {
      sortValue: String(rawValue ?? ""),
    },
  };
}

/**
 * Adapte une réponse backend "entities" en TableResponse pour TanStackTable.
 *
 * @param {{meta:any, entities:any[]}} response
 * @returns {{meta:any, rows:any[]}}
 */
export function adaptNpcEntitiesTableResponse({ meta, entities }) {
  const ctx = { meta };
  const rows = (Array.isArray(entities) ? entities : []).map((entity) => {
    const cells = {};
    // Générer les cellules pour toutes les colonnes définies dans la config table
    const colIds = [
      "id",
      "creature_name",
      "classe",
      "specialization",
      "created_at",
      "updated_at",
    ];
    for (const colId of colIds) {
      cells[colId] = buildNpcCell(colId, entity, ctx, { context: "table" });
    }
    return {
      id: entity?.id ?? null,
      cells,
      rowParams: {
        entity,
      },
    };
  });

  return {
    meta: meta || {},
    rows,
  };
}

