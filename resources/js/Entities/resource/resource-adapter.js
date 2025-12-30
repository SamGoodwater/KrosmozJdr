/**
 * Resource adapter (Option B)
 *
 * @description
 * Transforme une réponse backend "entities" en `TableResponse` conforme à TanStackTable v2,
 * en générant les `cells` côté frontend à partir de `resource-descriptors`.
 *
 * @example
 * <EntityTanStackTable :response-adapter="adaptResourceEntitiesTableResponse" />
 */

import { DEFAULT_RESOURCE_FIELD_VIEWS, getResourceFieldDescriptors } from "@/Entities/resource/resource-descriptors";
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

const formatDateShortFr = (isoString) => {
  if (!isoString) return "-";
  const ms = Date.parse(String(isoString));
  if (!Number.isFinite(ms)) return "-";
  const d = new Date(ms);
  return `${pad2(d.getDate())}/${pad2(d.getMonth() + 1)}/${d.getFullYear()}`;
};

const boolToOuiNon = (v) => (String(v) === "1" || v === true ? "Oui" : "Non");

const getOptionLabel = (options, value, fallback = "-") => {
  const v = String(value ?? "");
  const arr = Array.isArray(options) ? options : [];
  const opt = arr.find((o) => String(o?.value ?? "") === v);
  return opt?.label ? String(opt.label) : fallback;
};

const RESOURCE_RARITY_LABELS = Object.freeze({
  0: "Commun",
  1: "Peu commun",
  2: "Rare",
  3: "Très rare",
  4: "Légendaire",
  5: "Unique",
});

const VISIBILITY_LABELS = Object.freeze({
  guest: "Invité",
  user: "Utilisateur",
  game_master: "Maître de jeu",
  admin: "Administrateur",
});

const visibilityColor = (v) => {
  const s = String(v ?? "");
  if (s === "admin") return "error";
  if (s === "game_master") return "warning";
  if (s === "user") return "info";
  return "neutral";
};

// NOTE: ancien mapping de rareté -> couleurs DaisyUI.
// On utilise désormais `Badge color="auto"` (côté CellRenderer/Badge) pour la rareté.

const resourceShowHref = (id) => {
  try {
    return route("entities.resources.show", { resource: id });
  } catch {
    // fallback safe
    return null;
  }
};

const dofusDbResourceHref = (dofusdbId) => {
  const n = toNumber(dofusdbId);
  if (!n) return null;
  return `https://www.dofus.com/fr/mmorpg/encyclopedie/ressources/${n}`;
};

const resolveViewConfigFor = (descriptor, { view = "table" } = {}) => {
  const views = descriptor?.display?.views || DEFAULT_RESOURCE_FIELD_VIEWS;
  const v = views?.[view] || null;
  if (v && typeof v === "object") return v;
  return { size: "normal" };
};

/**
 * Construit une Cell v2 depuis une entité Resource brute.
 *
 * @param {string} colId
 * @param {any} entity
 * @param {any} ctx
 * @param {{context?: "table"|"text"|"compact"|"minimal"|"extended", size?: "small"|"normal"|"large"}} [opts]
 * @returns {{type:string,value:any,params?:any}}
 */
export function buildResourceCell(colId, entity, ctx = {}, opts = {}) {
  const descriptors = getResourceFieldDescriptors(ctx);
  const d = descriptors[colId] || descriptors?.[colId?.replace(/-/g, "_")] || null;
  const context = opts?.context || "table";
  const viewCfg = resolveViewConfigFor(d, { view: context });
  const size = opts?.size || viewCfg?.size || "normal";
  const sizeCfg = d?.display?.sizes?.[size] || {};
  const mode = viewCfg?.mode || sizeCfg?.mode || null;
  const truncateScale = sizeToTruncateScale(size);

  // Baselines
  const id = entity?.id ?? null;

  if (colId === "image") {
    return {
      type: "image",
      value: entity?.image || null,
      params: {
        alt: entity?.name || "Image",
        searchValue: entity?.name || "",
      },
    };
  }

  if (colId === "name") {
    const name = entity?.name || "-";
    return {
      type: "route",
      value: name,
      params: {
        href: resourceShowHref(id),
        tooltip: name === "-" ? "" : String(name),
        truncate: { context, scale: truncateScale },
        searchValue: name === "-" ? "" : String(name),
        sortValue: name === "-" ? "" : String(name),
      },
    };
  }

  if (colId === "level") {
    const v = entity?.level ?? null;
    const sortValue = toNumber(v) ?? String(v ?? "");
    // Produit: badge (nuancié level)
    if (mode === "badge") {
      const shown = v === null || typeof v === "undefined" || v === "" ? "-" : String(v);
      return {
        type: "badge",
        value: shown,
        params: {
          color: "auto",
          autoLabel: shown,
          autoScheme: "level",
          glassy: true,
          filterValue: v === null || typeof v === "undefined" ? "" : String(v),
          sortValue,
          searchValue: v === null || typeof v === "undefined" ? "" : String(v),
        },
      };
    }
    return {
      type: "text",
      value: v === null || typeof v === "undefined" || v === "" ? "-" : v,
      params: {
        filterValue: v === null || typeof v === "undefined" ? "" : String(v),
        sortValue,
        searchValue: v === null || typeof v === "undefined" ? "" : String(v),
      },
    };
  }

  if (colId === "resource_type") {
    const typeName = entity?.resourceType?.name || "-";
    const typeId = entity?.resource_type_id ?? null;
    // Produit: badge (toutes tailles)
    if (mode === "badge") {
      return {
        type: "badge",
        value: typeName,
        params: {
          color: "neutral",
          tooltip: typeName === "-" ? "" : typeName,
          truncate: { context, scale: truncateScale },
          truncateClass: getTruncateClass({ context, scale: truncateScale }),
          filterValue: typeId ? String(typeId) : "",
          sortValue: typeName,
          searchValue: typeName === "-" ? "" : typeName,
        },
      };
    }

    return {
      type: "text",
      value: typeName,
      params: {
        tooltip: typeName === "-" ? "" : typeName,
        truncate: { context, scale: truncateScale },
        filterValue: typeId ? String(typeId) : "",
        sortValue: typeName,
        searchValue: typeName === "-" ? "" : typeName,
      },
    };
  }

  if (colId === "rarity") {
    const rarity = entity?.rarity ?? 0;
    const label =
      getOptionLabel(ctx?.meta?.filterOptions?.rarity, rarity, "") ||
      RESOURCE_RARITY_LABELS[toNumber(rarity) ?? 0] ||
      String(rarity ?? "-");
    return {
      type: "badge",
      value: label,
      params: {
        color: "auto",
        // Pour un nuancié logique, utiliser la valeur numérique (1..6) plutôt que le label
        autoLabel: String(toNumber(rarity) ?? rarity ?? ""),
        autoScheme: "rarity",
        glassy: true,
        filterValue: String(toNumber(rarity) ?? rarity ?? ""),
        sortValue: toNumber(rarity) ?? 0,
      },
    };
  }

  if (colId === "dofus_version") {
    const raw = entity?.dofus_version ?? null;
    const v = raw === null || typeof raw === "undefined" || raw === "" ? "-" : String(raw);
    return {
      type: "text",
      value: v,
      params: {
        tooltip: v === "-" ? "" : v,
        truncate: { context, scale: truncateScale },
        sortValue: v === "-" ? "" : v,
        searchValue: v === "-" ? "" : v,
      },
    };
  }

  if (colId === "is_visible") {
    const raw = entity?.is_visible ?? null;
    const code = raw === null || typeof raw === "undefined" || raw === "" ? "" : String(raw);
    const label = VISIBILITY_LABELS[code] || (code ? code : "-");
    return {
      type: "badge",
      value: label,
      params: {
        color: visibilityColor(code),
        filterValue: code,
        sortValue: code,
      },
    };
  }

  if (colId === "price") {
    const v = entity?.price ?? null;
    const sortValue = toNumber(v) ?? String(v ?? "");
    return { type: "text", value: v === null || v === "" ? "-" : v, params: { sortValue } };
  }

  if (colId === "weight") {
    const v = entity?.weight ?? null;
    const sortValue = toNumber(v) ?? String(v ?? "");
    return { type: "text", value: v === null || v === "" ? "-" : v, params: { sortValue } };
  }

  if (colId === "usable") {
    const raw = entity?.usable ?? 0;
    const boolValue = String(raw) === "1" || raw === true;
    if (mode === "boolIcon") {
      return {
        type: "icon",
        value: boolValue ? "fa-solid fa-check" : "fa-solid fa-xmark",
        params: {
          alt: boolValue ? "Oui" : "Non",
          boolean: true,
          booleanValue: boolValue ? 1 : 0,
        },
      };
    }
    return {
      type: "badge",
      value: boolToOuiNon(raw),
      params: {
        color: boolValue ? "success" : "error",
        filterValue: boolValue ? "1" : "0",
        sortValue: boolValue ? 1 : 0,
        boolean: true,
        booleanValue: boolValue ? 1 : 0,
      },
    };
  }

  if (colId === "auto_update") {
    const raw = entity?.auto_update ?? false;
    const boolValue = raw === true || String(raw) === "1";
    if (mode === "boolIcon") {
      return {
        type: "icon",
        value: boolValue ? "fa-solid fa-check" : "fa-solid fa-xmark",
        params: {
          alt: boolValue ? "Oui" : "Non",
          boolean: true,
          booleanValue: boolValue ? 1 : 0,
        },
      };
    }
    return {
      type: "badge",
      value: boolValue ? "Oui" : "Non",
      params: {
        color: boolValue ? "success" : "error",
        filterValue: boolValue ? "1" : "0",
        sortValue: boolValue ? 1 : 0,
        boolean: true,
        booleanValue: boolValue ? 1 : 0,
      },
    };
  }

  if (colId === "dofusdb_id") {
    const raw = entity?.dofusdb_id ?? null;
    const n = toNumber(raw);
    const shown = n ? String(n) : "-";
    return {
      type: "route",
      value: shown,
      params: {
        href: dofusDbResourceHref(n),
        target: "_blank",
        tooltip: shown === "-" ? "" : shown,
        truncate: { context, scale: truncateScale },
        sortValue: n ?? 0,
        filterValue: n ? String(n) : "",
      },
    };
  }

  if (colId === "created_by") {
    const createdByLabel = entity?.createdBy?.name || entity?.createdBy?.email || "-";
    return {
      type: "text",
      value: createdByLabel,
      params: {
        tooltip: createdByLabel === "-" ? "" : createdByLabel,
        truncate: { context, scale: truncateScale },
        sortValue: createdByLabel,
        searchValue: createdByLabel === "-" ? "" : createdByLabel,
      },
    };
  }

  if (colId === "created_at" || colId === "updated_at") {
    const iso = entity?.[colId] ?? null;
    const ms = iso ? Date.parse(String(iso)) : 0;
    const label = mode === "dateShort" ? formatDateShortFr(iso) : formatDateFr(iso);
    return {
      type: "text",
      value: label,
      params: {
        sortValue: Number.isFinite(ms) ? Math.floor(ms / 1000) : 0,
        searchValue: label === "-" ? "" : label,
      },
    };
  }

  // fallback text
  const v = entity?.[colId];
  return {
    type: "text",
    value: v === null || typeof v === "undefined" || v === "" ? "-" : v,
    params: d?.label ? { label: d.label } : {},
  };
}

/**
 * Adapter: backend `{ meta, entities }` -> frontend `{ meta, rows }`
 *
 * @param {any} payload
 * @returns {{meta:any, rows:any[]}}
 */
export function adaptResourceEntitiesTableResponse(payload) {
  const meta = payload?.meta || {};
  const entities = Array.isArray(payload?.entities) ? payload.entities : [];
  const ctx = { meta, capabilities: meta?.capabilities || null };

  const rows = entities.map((entity) => {
    const id = entity?.id;
    return {
      id,
      cells: {
        image: buildResourceCell("image", entity, ctx, { context: "table" }),
        name: buildResourceCell("name", entity, ctx, { context: "table" }),
        level: buildResourceCell("level", entity, ctx, { context: "table" }),
        resource_type: buildResourceCell("resource_type", entity, ctx, { context: "table" }),
        rarity: buildResourceCell("rarity", entity, ctx, { context: "table" }),
        price: buildResourceCell("price", entity, ctx, { context: "table" }),
        weight: buildResourceCell("weight", entity, ctx, { context: "table" }),
        usable: buildResourceCell("usable", entity, ctx, { context: "table" }),
        auto_update: buildResourceCell("auto_update", entity, ctx, { context: "table" }),
        dofusdb_id: buildResourceCell("dofusdb_id", entity, ctx, { context: "table" }),
        created_by: buildResourceCell("created_by", entity, ctx, { context: "table" }),
        created_at: buildResourceCell("created_at", entity, ctx, { context: "table" }),
        updated_at: buildResourceCell("updated_at", entity, ctx, { context: "table" }),
      },
      rowParams: { entity },
    };
  });

  return { meta, rows };
}

export default adaptResourceEntitiesTableResponse;


