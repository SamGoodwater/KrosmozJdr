/**
 * ResourceType adapter (Option B)
 *
 * @description
 * Transforme une réponse backend "entities" en `TableResponse` conforme à TanStackTable v2,
 * en générant les `cells` côté frontend à partir de `resource-type-descriptors`.
 *
 * @example
 * <EntityTanStackTable :response-adapter="adaptResourceTypeEntitiesTableResponse" />
 */

import { getResourceTypeFieldDescriptors } from "@/Entities/resource-type/resource-type-descriptors";
import { getTruncateClass, sizeToTruncateScale } from "@/Utils/entity/text-truncate";

const pad2 = (n) => String(n).padStart(2, "0");
const formatDateFr = (isoString) => {
  if (!isoString) return "-";
  const ms = Date.parse(String(isoString));
  if (!Number.isFinite(ms)) return "-";
  const d = new Date(ms);
  return `${pad2(d.getDate())}/${pad2(d.getMonth() + 1)}/${d.getFullYear()} ${pad2(d.getHours())}:${pad2(d.getMinutes())}`;
};

const decisionLabel = (decision) => {
  if (decision === "allowed") return "Utilisé";
  if (decision === "blocked") return "Non utilisé";
  return "En attente";
};

const decisionColor = (decision) => {
  if (decision === "allowed") return "green-700";
  if (decision === "blocked") return "red-700";
  return "gray-700";
};

const resolveViewConfigFor = (descriptor, ctx = {}) => {
  const views = descriptor?.display?.views || {};
  const v = views?.[ctx?.view] || views?.table || null;
  if (v && typeof v === "object") return v;
  return { size: "normal" };
};

export function buildResourceTypeCell(colId, entity, ctx = {}, opts = {}) {
  const descriptors = getResourceTypeFieldDescriptors(ctx);
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
        href: entity?.id ? route("entities.resource-types.show", entity.id) : undefined,
        tooltip: name === "-" ? "" : name,
        truncate: { context, scale: truncateScale },
        truncateClass: getTruncateClass({ context, scale: truncateScale }),
        sortValue: name === "-" ? "" : name,
        searchValue: name === "-" ? "" : name,
      },
    };
  }

  if (colId === "decision") {
    const code = String(entity?.decision ?? "pending");
    const label = decisionLabel(code);
    return {
      type: "badge",
      value: label,
      params: {
        color: decisionColor(code),
        tooltip: label,
        truncate: { context, scale: truncateScale },
        truncateClass: getTruncateClass({ context, scale: truncateScale }),
        filterValue: code,
        sortValue: code,
      },
    };
  }

  if (colId === "last_seen_at" || colId === "created_at" || colId === "updated_at") {
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

  // defaults: text
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

/**
 * Adapter: backend `{ meta, entities }` -> frontend `{ meta, rows }`
 *
 * @param {any} payload
 * @returns {{meta:any, rows:any[]}}
 */
export function adaptResourceTypeEntitiesTableResponse(payload) {
  const meta = payload?.meta || {};
  const entities = Array.isArray(payload?.entities) ? payload.entities : [];
  const ctx = { meta, capabilities: meta?.capabilities || null };

  const rows = entities.map((entity) => {
    const id = entity?.id;
    return {
      id,
      cells: {
        id: buildResourceTypeCell("id", entity, ctx, { context: "table" }),
        created_at: buildResourceTypeCell("created_at", entity, ctx, { context: "table" }),
        updated_at: buildResourceTypeCell("updated_at", entity, ctx, { context: "table" }),
        name: buildResourceTypeCell("name", entity, ctx, { context: "table" }),
        dofusdb_type_id: buildResourceTypeCell("dofusdb_type_id", entity, ctx, { context: "table" }),
        decision: buildResourceTypeCell("decision", entity, ctx, { context: "table" }),
        seen_count: buildResourceTypeCell("seen_count", entity, ctx, { context: "table" }),
        last_seen_at: buildResourceTypeCell("last_seen_at", entity, ctx, { context: "table" }),
        resources_count: buildResourceTypeCell("resources_count", entity, ctx, { context: "table" }),
      },
      rowParams: { entity },
    };
  });

  return { meta, rows };
}


