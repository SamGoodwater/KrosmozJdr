/**
 * ResourceType field descriptors (Option B)
 *
 * @description
 * Source de vérité côté frontend pour :
 * - l'affichage (cellules table + vues)
 * - l'édition (forms / bulk) [à généraliser ensuite]
 *
 * ⚠️ Sécurité : ces descriptors ne sont que de l'UX. Le backend reste la vérité (Policies + validation).
 *
 * @example
 * import { getResourceTypeFieldDescriptors } from "@/Entities/resource-type/resource-type-descriptors";
 * const descriptors = getResourceTypeFieldDescriptors({ meta });
 */

/**
 * @typedef {Object} ResourceTypeFieldDescriptor
 * @property {string} key
 * @property {string} label
 * @property {string} [description]
 * @property {string} [tooltip]
 * @property {string|null} [icon]
 * @property {string|null|"auto"} [color]
 * @property {"text"|"number"|"bool"|"date"|"image"|"link"|"enum"} [format]
 * @property {(ctx: any) => boolean} [visibleIf]
 * @property {(ctx: any) => boolean} [editableIf]
 * @property {Object} [display]
 * @property {Record<"table"|"text"|"compact"|"minimal"|"extended", { size: "small"|"normal"|"large", mode?: string }>} [display.views]
 * @property {Record<"small"|"normal"|"large", any>} [display.sizes]
 */

export const DEFAULT_RESOURCE_TYPE_FIELD_VIEWS = Object.freeze({
  table: { size: "small" },
  text: { size: "normal" },
  compact: { size: "small" },
  minimal: { size: "small" },
  extended: { size: "large" },
});

/**
 * Ordre d'affichage "ResourceType" par vue.
 *
 * @description
 * (v1) On centralise l'ordre ici pour commencer (comme `resource`).
 */
export const RESOURCE_TYPE_VIEW_FIELDS = Object.freeze({
  quickEdit: ["decision", "usable", "is_visible"],
  minimal: ["decision", "resources_count", "dofusdb_type_id"],
  compact: ["decision", "resources_count", "seen_count", "last_seen_at", "dofusdb_type_id"],
  extended: [
    "decision",
    "resources_count",
    "seen_count",
    "last_seen_at",
    "dofusdb_type_id",
    "created_at",
    "updated_at",
  ],
});

export function getResourceTypeFieldDescriptors(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || null;
  const canUpdateAny = Boolean(can?.updateAny);

  return {
    id: {
      key: "id",
      label: "ID",
      icon: "fa-solid fa-hashtag",
      format: "number",
      visibleIf: () => canUpdateAny,
      display: {
        views: DEFAULT_RESOURCE_TYPE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
    name: {
      key: "name",
      label: "Nom",
      icon: "fa-solid fa-tag",
      format: "text",
      display: {
        views: { ...DEFAULT_RESOURCE_TYPE_FIELD_VIEWS, table: { size: "small", mode: "route" } },
        sizes: { small: { mode: "route" }, normal: { mode: "route" }, large: { mode: "route" } },
      },
      edit: {
        form: { type: "text", required: true, showInCompact: true, bulk: { enabled: false } },
      },
    },
    dofusdb_type_id: {
      key: "dofusdb_type_id",
      label: "DofusDB typeId",
      icon: "fa-solid fa-database",
      format: "number",
      visibleIf: () => canUpdateAny,
      display: {
        views: DEFAULT_RESOURCE_TYPE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: { type: "number", required: false, showInCompact: true, bulk: { enabled: false } },
      },
    },
    decision: {
      key: "decision",
      label: "Statut",
      icon: "fa-solid fa-circle-check",
      format: "enum",
      display: {
        views: DEFAULT_RESOURCE_TYPE_FIELD_VIEWS,
        sizes: { small: { mode: "badge" }, normal: { mode: "badge" }, large: { mode: "badge" } },
      },
      edit: {
        form: {
          type: "select",
          group: "Statut",
          tooltip: "Ce statut est utilisé dans la registry DofusDB (utilisé / non utilisé / en attente).",
          required: false,
          showInCompact: true,
          options: [
            { value: "pending", label: "En attente" },
            { value: "allowed", label: "Utilisé" },
            { value: "blocked", label: "Non utilisé" },
          ],
          defaultValue: "pending",
          bulk: { enabled: true, nullable: false, build: (v) => v },
        },
      },
    },
    usable: {
      key: "usable",
      label: "Utilisable",
      icon: "fa-solid fa-check-circle",
      format: "bool",
      display: {
        views: DEFAULT_RESOURCE_TYPE_FIELD_VIEWS,
        sizes: { small: { mode: "badge" }, normal: { mode: "badge" }, large: { mode: "badge" } },
      },
      edit: {
        form: {
          type: "checkbox",
          group: "Statut",
          required: false,
          showInCompact: true,
          defaultValue: true,
          bulk: { enabled: true, nullable: false, build: (v) => v === "1" || v === true },
        },
      },
    },
    is_visible: {
      key: "is_visible",
      label: "Visibilité",
      icon: "fa-solid fa-eye",
      format: "enum",
      display: {
        views: DEFAULT_RESOURCE_TYPE_FIELD_VIEWS,
        sizes: { small: { mode: "badge" }, normal: { mode: "badge" }, large: { mode: "badge" } },
      },
      edit: {
        form: {
          type: "select",
          group: "Statut",
          help: "Limite l’accès front (UX). La sécurité réelle reste côté backend.",
          required: false,
          showInCompact: true,
          options: [
            { value: "guest", label: "Invité" },
            { value: "super_admin", label: "Super admin" },
          ],
          defaultValue: "guest",
          bulk: { enabled: true, nullable: false, build: (v) => v },
        },
      },
    },
    seen_count: {
      key: "seen_count",
      label: "Détections",
      icon: "fa-solid fa-eye",
      format: "number",
      display: {
        views: DEFAULT_RESOURCE_TYPE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
    last_seen_at: {
      key: "last_seen_at",
      label: "Dernière détection",
      icon: "fa-solid fa-clock",
      format: "date",
      display: {
        views: DEFAULT_RESOURCE_TYPE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
    resources_count: {
      key: "resources_count",
      label: "Ressources",
      icon: "fa-solid fa-cubes",
      format: "number",
      display: {
        views: DEFAULT_RESOURCE_TYPE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
    created_at: {
      key: "created_at",
      label: "Créé le",
      icon: "fa-solid fa-calendar-plus",
      format: "date",
      visibleIf: () => canUpdateAny,
      display: {
        views: DEFAULT_RESOURCE_TYPE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
    updated_at: {
      key: "updated_at",
      label: "Modifié le",
      icon: "fa-solid fa-calendar-check",
      format: "date",
      visibleIf: () => canUpdateAny,
      display: {
        views: DEFAULT_RESOURCE_TYPE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
  };
}


