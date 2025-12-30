/**
 * Item adapter (Option B)
 *
 * @description
 * Transforme une réponse backend "entities" en `TableResponse` conforme à TanStackTable v2,
 * en générant les `cells` côté frontend à partir de `item-descriptors`.
 */

import { getItemFieldDescriptors, DEFAULT_ITEM_FIELD_VIEWS } from "@/Entities/item/item-descriptors";
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

const itemShowHref = (id) => {
  try {
    return route("entities.items.show", { item: id });
  } catch {
    return null;
  }
};

const dofusDbItemHref = (dofusdbId) => {
  const n = toNumber(dofusdbId);
  if (!n) return null;
  return `https://www.dofus.com/fr/mmorpg/encyclopedie/equipements/${n}`;
};

const resolveViewConfigFor = (descriptor, { view = "table" } = {}) => {
  const views = descriptor?.display?.views || DEFAULT_ITEM_FIELD_VIEWS;
  const v = views?.[view] || null;
  if (v && typeof v === "object") return v;
  return { size: "normal" };
};

const raritySort = (key) => {
  const k = String(key ?? "");
  const n = Number(k);
  if (Number.isFinite(n)) return n;
  return k || 999;
};

const getOptionLabel = (options, value, fallback = "-") => {
  const v = String(value ?? "");
  const arr = Array.isArray(options) ? options : [];
  const found = arr.find((o) => String(o?.value ?? "") === v);
  return found?.label ? String(found.label) : fallback;
};

export function buildItemCell(colId, entity, ctx = {}, opts = {}) {
  const descriptors = getItemFieldDescriptors(ctx);
  const d = descriptors[colId] || descriptors?.[colId?.replace(/-/g, "_")] || null;

  const context = opts?.context || "table";
  const viewCfg = resolveViewConfigFor(d, { view: context });
  const size = opts?.size || viewCfg?.size || "normal";
  const sizeCfg = d?.display?.sizes?.[size] || {};
  const mode = viewCfg?.mode || sizeCfg?.mode || null;

  const truncateScale = sizeToTruncateScale(size);

  if (colId === "name") {
    const name = String(entity?.name ?? "-") || "-";
    return {
      type: "route",
      value: name,
      params: {
        href: itemShowHref(entity?.id),
        tooltip: name === "-" ? "" : name,
        truncate: { context, scale: truncateScale },
        truncateClass: getTruncateClass({ context, scale: truncateScale }),
        sortValue: name === "-" ? "" : name,
        searchValue: name === "-" ? "" : name,
      },
    };
  }

  if (colId === "level") {
    const v = entity?.level ?? null;
    const shown = v === null || typeof v === "undefined" || v === "" ? "-" : String(v);
    return {
      type: "text",
      value: shown,
      params: {
        filterValue: shown === "-" ? "" : shown,
        sortValue: toNumber(v) ?? shown,
        tooltip: shown === "-" ? "" : shown,
        truncate: { context, scale: truncateScale },
        truncateClass: getTruncateClass({ context, scale: truncateScale }),
      },
    };
  }

  if (colId === "rarity") {
    const raw = entity?.rarity ?? null;
    const key = raw === null || typeof raw === "undefined" || raw === "" ? "" : String(raw);
    const label = key ? getOptionLabel(ctx?.meta?.filterOptions?.rarity, key, key) : "-";
    return {
      type: "badge",
      value: label,
      params: {
        // Rareté items: stable + lisible
        color: "auto",
        autoLabel: key,
        tooltip: label === "-" ? "" : label,
        truncate: { context, scale: truncateScale },
        truncateClass: getTruncateClass({ context, scale: truncateScale }),
        filterValue: key,
        sortValue: raritySort(key),
      },
    };
  }

  if (colId === "item_type") {
    const typeName = entity?.itemType?.name || "-";
    const typeId = entity?.item_type_id ?? null;
    return {
      type: "text",
      value: typeName,
      params: {
        tooltip: typeName === "-" ? "" : typeName,
        truncate: { context, scale: truncateScale },
        truncateClass: getTruncateClass({ context, scale: truncateScale }),
        filterValue: typeId ? String(typeId) : "",
        sortValue: typeName === "-" ? "" : typeName,
        searchValue: typeName === "-" ? "" : typeName,
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
        href: dofusDbItemHref(n),
        target: "_blank",
        tooltip: shown === "-" ? "" : `DofusDB #${shown}`,
        truncate: { context, scale: truncateScale },
        truncateClass: getTruncateClass({ context, scale: truncateScale }),
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
        truncateClass: getTruncateClass({ context, scale: truncateScale }),
        sortValue: createdByLabel === "-" ? "" : createdByLabel,
        searchValue: createdByLabel === "-" ? "" : createdByLabel,
      },
    };
  }

  if (colId === "created_at" || colId === "updated_at") {
    const iso = entity?.[colId] ?? null;
    const label = formatDateFr(iso);
    return {
      type: "text",
      value: label,
      params: {
        tooltip: label === "-" ? "" : label,
        truncate: { context, scale: truncateScale },
        truncateClass: getTruncateClass({ context, scale: truncateScale }),
        sortValue: label === "-" ? "" : label,
        searchValue: label === "-" ? "" : label,
      },
    };
  }

  // fallback
  const v = entity?.[colId];
  const text = v === null || typeof v === "undefined" || v === "" ? "-" : String(v);
  return {
    type: mode === "badge" ? "badge" : "text",
    value: text,
    params: {
      tooltip: text === "-" ? "" : text,
      truncate: { context, scale: truncateScale },
      truncateClass: getTruncateClass({ context, scale: truncateScale }),
      sortValue: text === "-" ? "" : text,
      searchValue: text === "-" ? "" : text,
    },
  };
}

export function adaptItemEntitiesTableResponse(payload) {
  const meta = payload?.meta || {};
  const entities = Array.isArray(payload?.entities) ? payload.entities : [];
  const ctx = { meta, capabilities: meta?.capabilities || null };

  const rows = entities.map((entity) => {
    const id = entity?.id;
    return {
      id,
      cells: {
        id: buildItemCell("id", entity, ctx, { context: "table" }),
        created_at: buildItemCell("created_at", entity, ctx, { context: "table" }),
        updated_at: buildItemCell("updated_at", entity, ctx, { context: "table" }),
        name: buildItemCell("name", entity, ctx, { context: "table" }),
        level: buildItemCell("level", entity, ctx, { context: "table" }),
        rarity: buildItemCell("rarity", entity, ctx, { context: "table" }),
        item_type: buildItemCell("item_type", entity, ctx, { context: "table" }),
        dofusdb_id: buildItemCell("dofusdb_id", entity, ctx, { context: "table" }),
        created_by: buildItemCell("created_by", entity, ctx, { context: "table" }),
      },
      rowParams: { entity },
    };
  });

  return { meta, rows };
}


