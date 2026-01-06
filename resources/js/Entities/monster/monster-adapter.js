/**
 * Monster adapter (Option B)
 *
 * @description
 * Transforme une réponse backend "entities" en `TableResponse` conforme à TanStackTable v2,
 * en générant les `cells` côté frontend à partir de `monster-descriptors`.
 */

import { DEFAULT_MONSTER_FIELD_VIEWS, getMonsterFieldDescriptors } from "@/Entities/monster/monster-descriptors";
import { getTruncateClass, sizeToTruncateScale } from "@/Utils/entity/text-truncate";

const toNumber = (v) => {
  const n = typeof v === "string" ? Number(v) : v;
  return typeof n === "number" && Number.isFinite(n) ? n : null;
};

const pad2 = (n) => String(n).padStart(2, "0");
const formatDateFr = (isoString) => {
  if (!isoString) return "-";
  const ms = Date.parse(String(isoString));
  if (!Number.isFinite(ms)) return "-";
  const d = new Date(ms);
  return `${pad2(d.getDate())}/${pad2(d.getMonth() + 1)}/${d.getFullYear()} ${pad2(d.getHours())}:${pad2(d.getMinutes())}`;
};

const MONSTER_SIZE_LABELS = Object.freeze({
  0: "Minuscule",
  1: "Petit",
  2: "Moyen",
  3: "Grand",
  4: "Colossal",
  5: "Gigantesque",
});

const monsterShowHref = (id) => {
  try {
    return route("entities.monsters.show", { monster: id });
  } catch {
    return null;
  }
};

const resolveViewConfigFor = (descriptor, { view = "table" } = {}) => {
  const views = descriptor?.display?.views || DEFAULT_MONSTER_FIELD_VIEWS;
  const v = views?.[view] || null;
  if (v && typeof v === "object") return v;
  return { size: "normal" };
};

/**
 * Construit une Cell v2 depuis une entité Monster brute.
 *
 * @param {string} colId
 * @param {any} entity
 * @param {any} ctx
 * @param {{context?: "table"|"text"|"compact"|"minimal"|"extended", size?: "small"|"normal"|"large"}} [opts]
 * @returns {{type:string,value:any,params?:any}}
 */
export function buildMonsterCell(colId, entity, ctx = {}, opts = {}) {
  const descriptors = getMonsterFieldDescriptors(ctx);
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
        href: monsterShowHref(id),
        tooltip: name === "-" ? "" : String(name),
        truncate: { context, scale: truncateScale },
        searchValue: name === "-" ? "" : String(name),
        sortValue: name === "-" ? "" : String(name),
      },
    };
  }

  if (colId === "monster_race") {
    const race = entity?.monsterRace || null;
    const raceName = race?.name || "-";
    return {
      type: "text",
      value: raceName,
      params: {
        tooltip: raceName === "-" ? "" : raceName,
        truncate: { context, scale: truncateScale },
        searchValue: raceName === "-" ? "" : raceName,
        sortValue: raceName,
      },
    };
  }

  if (colId === "size") {
    const v = entity?.size ?? null;
    const sizeValue = toNumber(v);
    const label = sizeValue !== null && MONSTER_SIZE_LABELS[sizeValue] ? MONSTER_SIZE_LABELS[sizeValue] : (v !== null ? String(v) : "-");
    if (mode === "badge") {
      return {
        type: "badge",
        value: label,
        params: {
          color: "neutral",
          filterValue: sizeValue !== null ? String(sizeValue) : "",
          sortValue: sizeValue ?? 0,
        },
      };
    }
    return {
      type: "text",
      value: label,
      params: {
        filterValue: sizeValue !== null ? String(sizeValue) : "",
        sortValue: sizeValue ?? 0,
      },
    };
  }

  if (colId === "is_boss") {
    const v = entity?.is_boss ?? null;
    const boolValue = v === 1 || v === true || String(v) === "1";
    const label = boolValue ? "Boss" : "Non";
    if (mode === "badge") {
      return {
        type: "badge",
        value: label,
        params: {
          color: boolValue ? "error" : "base",
          filterValue: boolValue ? "1" : "0",
          sortValue: boolValue ? 1 : 0,
        },
      };
    }
    return {
      type: "text",
      value: label,
      params: {
        filterValue: boolValue ? "1" : "0",
        sortValue: boolValue ? 1 : 0,
      },
    };
  }

  if (colId === "boss_pa") {
    const v = entity?.boss_pa ?? null;
    return {
      type: "text",
      value: v === null || typeof v === "undefined" || v === "" ? "-" : String(v),
      params: {
        sortValue: v === null || typeof v === "undefined" ? "" : String(v),
      },
    };
  }

  if (colId === "dofusdb_id") {
    const v = entity?.dofusdb_id ?? null;
    return {
      type: "text",
      value: v ? String(v) : "-",
      params: {
        sortValue: toNumber(v) ?? 0,
        searchValue: v ? String(v) : "",
      },
    };
  }

  if (colId === "dofus_version") {
    const v = entity?.dofus_version ?? null;
    return {
      type: "text",
      value: v === null || typeof v === "undefined" || v === "" ? "-" : String(v),
      params: {
        sortValue: v === null || typeof v === "undefined" ? "" : String(v),
      },
    };
  }

  if (colId === "auto_update") {
    const v = entity?.auto_update ?? false;
    const boolValue = v === true || String(v) === "1" || String(v) === "true";
    const label = boolValue ? "Oui" : "Non";
    if (mode === "badge") {
      return {
        type: "badge",
        value: label,
        params: {
          color: boolValue ? "info" : "neutral",
          sortValue: boolValue ? 1 : 0,
        },
      };
    }
    return {
      type: "text",
      value: label,
      params: {
        sortValue: boolValue ? 1 : 0,
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
        sortValue: toNumber(v) ?? 0,
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
export function adaptMonsterEntitiesTableResponse({ meta, entities }) {
  const ctx = { meta };
  const rows = (Array.isArray(entities) ? entities : []).map((entity) => {
    const cells = {};
    // Générer les cellules pour toutes les colonnes définies dans la config table
    const colIds = [
      "id",
      "creature_name",
      "monster_race",
      "size",
      "is_boss",
      "dofusdb_id",
      "created_at",
      "updated_at",
    ];
    for (const colId of colIds) {
      cells[colId] = buildMonsterCell(colId, entity, ctx, { context: "table" });
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

