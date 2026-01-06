/**
 * Classe adapter (Option B)
 *
 * @description
 * Transforme une réponse backend "entities" en `TableResponse` conforme à TanStackTable v2,
 * en générant les `cells` côté frontend à partir de `classe-descriptors`.
 */

import { DEFAULT_CLASSE_FIELD_VIEWS, getClasseFieldDescriptors } from "@/Entities/classe/classe-descriptors";
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

const classeShowHref = (id) => {
  try {
    return route("entities.classes.show", { classe: id });
  } catch {
    return null;
  }
};

const resolveViewConfigFor = (descriptor, { view = "table" } = {}) => {
  const views = descriptor?.display?.views || DEFAULT_CLASSE_FIELD_VIEWS;
  const v = views?.[view] || null;
  if (v && typeof v === "object") return v;
  return { size: "normal" };
};

/**
 * Construit une Cell v2 depuis une entité Classe brute.
 *
 * @param {string} colId
 * @param {any} entity
 * @param {any} ctx
 * @param {{context?: "table"|"text"|"compact"|"minimal"|"extended", size?: "small"|"normal"|"large"}} [opts]
 * @returns {{type:string,value:any,params?:any}}
 */
export function buildClasseCell(colId, entity, ctx = {}, opts = {}) {
  const descriptors = getClasseFieldDescriptors(ctx);
  const d = descriptors[colId] || descriptors?.[colId?.replace(/-/g, "_")] || null;
  const context = opts?.context || "table";
  const viewCfg = resolveViewConfigFor(d, { view: context });
  const size = opts?.size || viewCfg?.size || "normal";
  const sizeCfg = d?.display?.sizes?.[size] || {};
  const mode = viewCfg?.mode || sizeCfg?.mode || null;
  const truncateScale = sizeToTruncateScale(size);

  // Baselines
  const id = entity?.id ?? null;

  if (colId === "name") {
    const name = entity?.name || "-";
    return {
      type: "route",
      value: name,
      params: {
        href: classeShowHref(id),
        tooltip: name === "-" ? "" : String(name),
        truncate: { context, scale: truncateScale },
        searchValue: name === "-" ? "" : String(name),
        sortValue: name === "-" ? "" : String(name),
      },
    };
  }

  if (colId === "life" || colId === "life_dice" || colId === "specificity" || colId === "description") {
    const v = entity?.[colId] ?? null;
    return {
      type: "text",
      value: v === null || typeof v === "undefined" || v === "" ? "-" : String(v),
      params: {
        sortValue: v === null || typeof v === "undefined" ? "" : String(v),
        searchValue: colId === "specificity" || colId === "description" ? (v === null || typeof v === "undefined" ? "" : String(v)) : undefined,
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

  if (colId === "usable") {
    const v = entity?.usable ?? null;
    const boolValue = v === 1 || v === true || String(v) === "1";
    const label = boolValue ? "Oui" : "Non";
    if (mode === "badge") {
      return {
        type: "badge",
        value: label,
        params: {
          color: boolValue ? "success" : "neutral",
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

  if (colId === "is_visible") {
    const v = entity?.is_visible ?? "guest";
    const VISIBILITY_LABELS = {
      guest: "Invité",
      user: "Utilisateur",
      player: "Joueur",
      game_master: "Maître du jeu",
      admin: "Administrateur",
    };
    const visibilityColor = (v) => {
      const s = String(v ?? "");
      if (s === "admin") return "error";
      if (s === "game_master") return "warning";
      if (s === "user") return "info";
      return "neutral";
    };
    const label = VISIBILITY_LABELS[v] || String(v);
    if (mode === "badge") {
      return {
        type: "badge",
        value: label,
        params: {
          color: visibilityColor(v),
          sortValue: label,
        },
      };
    }
    return {
      type: "text",
      value: label,
      params: {
        sortValue: label,
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

  if (colId === "created_by") {
    const user = entity?.createdBy || null;
    const label = user?.name || user?.email || "-";
    return {
      type: "text",
      value: label,
      params: {
        sortValue: label,
        searchValue: label === "-" ? "" : label,
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
export function adaptClasseEntitiesTableResponse({ meta, entities }) {
  const ctx = { meta };
  const rows = (Array.isArray(entities) ? entities : []).map((entity) => {
    const cells = {};
    // Générer les cellules pour toutes les colonnes définies dans la config table
    const colIds = [
      "id",
      "name",
      "life",
      "life_dice",
      "specificity",
      "dofusdb_id",
      "created_by",
      "created_at",
      "updated_at",
    ];
    for (const colId of colIds) {
      cells[colId] = buildClasseCell(colId, entity, ctx, { context: "table" });
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

