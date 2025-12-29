/**
 * Resource field descriptors (Option B)
 *
 * @description
 * Source de vérité côté frontend pour :
 * - l'affichage (cellules table + vues)
 * - l'édition (forms / bulk)
 *
 * ⚠️ Sécurité : ces descriptors ne sont que de l'UX. Le backend reste la vérité (Policies + filtrage des champs).
 *
 * @example
 * import { getResourceFieldDescriptors } from "@/Entities/resource/resource-descriptors";
 * const descriptors = getResourceFieldDescriptors({ meta });
 */

/**
 * @typedef {Object} ResourceFieldDescriptor
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
 * @property {Record<"table"|"text"|"compact"|"minimal"|"extended", { size: "small"|"normal"|"large", mode?: string, truncate?: number }>} [display.views]
 * @property {Record<"small"|"normal"|"large", any>} [display.sizes]
 */

const truncate = (value, max = 40) => {
  const s = String(value ?? "");
  if (!s) return "";
  if (s.length <= max) return s;
  return s.slice(0, Math.max(0, max - 1)) + "…";
};

/**
 * Convention globale (v1) — utilisée comme base, mais surchargée champ par champ :
 * - table -> small
 * - text -> normal
 * - compact -> small
 * - minimal -> normal
 * - extended -> large
 */
export const DEFAULT_RESOURCE_FIELD_VIEWS = Object.freeze({
  table: { size: "small" },
  text: { size: "normal" },
  compact: { size: "small" },
  // Produit: minimal utilise la représentation "small" (icône + valeur)
  minimal: { size: "small" },
  extended: { size: "large" },
});

/**
 * Ordre d'affichage "Ressource" par vue.
 *
 * @description
 * (v1) On centralise l'ordre ici pour commencer. À terme, on pourra déplacer
 * la notion de "présence dans une vue" directement dans chaque descriptor.
 */
export const RESOURCE_VIEW_FIELDS = Object.freeze({
  compact: [
    "rarity",
    "resource_type",
    "level",
    "usable",
    "price",
    "weight",
    "dofus_version",
    "is_visible",
    "auto_update",
    "dofusdb_id",
  ],
  extended: [
    "rarity",
    "resource_type",
    "level",
    "usable",
    "price",
    "weight",
    "dofus_version",
    "is_visible",
    "auto_update",
    "dofusdb_id",
    "created_by",
    "created_at",
    "updated_at",
  ],
});

/**
 * Descriptors "Ressource".
 *
 * Note : v1 = on définit la structure small/normal/large + mapping context -> size,
 * en priorité pour le contexte `table` (TanStack).
 *
 * @param {Object} ctx
 * @returns {Record<string, ResourceFieldDescriptor>}
 */
export function getResourceFieldDescriptors(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || null;
  const canUpdateAny = Boolean(can?.updateAny);

  return {
    image: {
      key: "image",
      label: "Image",
      icon: "fa-solid fa-image",
      color: "auto",
      format: "image",
      display: {
        views: DEFAULT_RESOURCE_FIELD_VIEWS,
        sizes: {
          small: { mode: "thumb" },
          normal: { mode: "thumb" },
          large: { mode: "thumb" },
        },
      },
    },
    name: {
      key: "name",
      label: "Nom",
      icon: "fa-solid fa-font",
      color: "auto",
      format: "text",
      display: {
        views: {
          ...DEFAULT_RESOURCE_FIELD_VIEWS,
          // Exemple: en table on veut un texte lisible (pas seulement une icône)
          table: { size: "small", mode: "route", truncate: 44 },
        },
        sizes: {
          small: { mode: "route", truncate: 44 },
          normal: { mode: "route", truncate: 80 },
          large: { mode: "route" },
        },
      },
    },
    level: {
      key: "level",
      label: "Niveau",
      icon: "fa-solid fa-level-up-alt",
      color: "auto",
      format: "number",
      display: {
        views: {
          ...DEFAULT_RESOURCE_FIELD_VIEWS,
          // Layout: small = icon + value (pas de label)
          compact: { size: "small" },
        },
        sizes: {
          // Produit: le niveau doit toujours être un badge (nuancié "level")
          small: { mode: "badge" },
          normal: { mode: "badge" },
          large: { mode: "badge" },
        },
      },
    },
    resource_type: {
      key: "resource_type",
      label: "Type",
      icon: "fa-solid fa-tag",
      color: "auto",
      format: "text",
      display: {
        views: DEFAULT_RESOURCE_FIELD_VIEWS,
        sizes: {
          // Produit: toujours sous forme de badge
          small: { mode: "badge" },
          normal: { mode: "badge" },
          large: { mode: "badge" },
        },
      },
    },
    rarity: {
      key: "rarity",
      label: "Rareté",
      icon: "fa-solid fa-star",
      color: "auto",
      format: "enum",
      display: {
        views: DEFAULT_RESOURCE_FIELD_VIEWS,
        sizes: {
          small: { mode: "badge" },
          normal: { mode: "badge" },
          large: { mode: "badge" },
        },
      },
    },
    price: {
      key: "price",
      label: "Prix",
      icon: "fa-solid fa-coins",
      color: "auto",
      format: "number",
      display: {
        views: {
          ...DEFAULT_RESOURCE_FIELD_VIEWS,
          compact: { size: "small" },
        },
        sizes: {
          small: { mode: "text" },
          normal: { mode: "text" },
          large: { mode: "text" },
        },
      },
    },
    weight: {
      key: "weight",
      label: "Poids",
      icon: "fa-solid fa-weight-hanging",
      color: "auto",
      format: "number",
      display: {
        views: {
          ...DEFAULT_RESOURCE_FIELD_VIEWS,
          compact: { size: "small" },
        },
        sizes: {
          small: { mode: "text" },
          normal: { mode: "text" },
          large: { mode: "text" },
        },
      },
    },
    dofus_version: {
      key: "dofus_version",
      label: "Version Dofus",
      icon: "fa-solid fa-code-branch",
      color: "auto",
      format: "text",
      display: {
        views: {
          ...DEFAULT_RESOURCE_FIELD_VIEWS,
          compact: { size: "small", truncate: 18 },
        },
        sizes: {
          small: { mode: "text", truncate: 18 },
          normal: { mode: "text", truncate: 40 },
          large: { mode: "text" },
        },
      },
    },
    is_visible: {
      key: "is_visible",
      label: "Visibilité",
      icon: "fa-solid fa-eye",
      color: "auto",
      format: "enum",
      display: {
        views: DEFAULT_RESOURCE_FIELD_VIEWS,
        sizes: {
          small: { mode: "badge" },
          normal: { mode: "badge" },
          large: { mode: "badge" },
        },
      },
    },
    usable: {
      key: "usable",
      label: "Utilisable",
      icon: "fa-solid fa-check",
      color: "auto",
      format: "bool",
      display: {
        views: {
          ...DEFAULT_RESOURCE_FIELD_VIEWS,
          // Exemple: table/minimal en icône, texte/extended en badge
          table: { size: "small", mode: "boolIcon" },
          minimal: { size: "small", mode: "boolIcon" },
        },
        sizes: {
          // table -> small : compact visuel
          small: { mode: "boolIcon" },
          normal: { mode: "boolBadge" },
          large: { mode: "boolBadge" },
        },
      },
    },
    auto_update: {
      key: "auto_update",
      label: "Auto-update",
      icon: "fa-solid fa-arrows-rotate",
      color: "auto",
      format: "bool",
      visibleIf: () => canUpdateAny,
      display: {
        views: {
          ...DEFAULT_RESOURCE_FIELD_VIEWS,
          table: { size: "small", mode: "boolIcon" },
          minimal: { size: "small", mode: "boolIcon" },
        },
        sizes: {
          small: { mode: "boolIcon" },
          normal: { mode: "boolBadge" },
          large: { mode: "boolBadge" },
        },
      },
    },
    dofusdb_id: {
      key: "dofusdb_id",
      label: "DofusDB",
      icon: "fa-solid fa-arrow-up-right-from-square",
      color: "auto",
      format: "link",
      visibleIf: () => canUpdateAny,
      display: {
        views: DEFAULT_RESOURCE_FIELD_VIEWS,
        sizes: {
          small: { mode: "routeExternal", truncate: 18 },
          normal: { mode: "routeExternal" },
          large: { mode: "routeExternal" },
        },
      },
    },
    created_by: {
      key: "created_by",
      label: "Créé par",
      icon: "fa-solid fa-user",
      color: "auto",
      format: "text",
      visibleIf: (c) => Boolean(c?.capabilities?.createAny ?? c?.meta?.capabilities?.createAny),
      display: {
        views: DEFAULT_RESOURCE_FIELD_VIEWS,
        sizes: {
          small: { mode: "text", truncate: 18 },
          normal: { mode: "text", truncate: 40 },
          large: { mode: "text" },
        },
      },
    },
    created_at: {
      key: "created_at",
      label: "Créé le",
      icon: "fa-solid fa-calendar",
      color: "auto",
      format: "date",
      visibleIf: (c) => Boolean(c?.capabilities?.createAny ?? c?.meta?.capabilities?.createAny),
      display: {
        views: DEFAULT_RESOURCE_FIELD_VIEWS,
        sizes: {
          small: { mode: "dateShort" },
          normal: { mode: "dateTime" },
          large: { mode: "dateTime" },
        },
      },
    },
    updated_at: {
      key: "updated_at",
      label: "Modifié le",
      icon: "fa-solid fa-clock",
      color: "auto",
      format: "date",
      visibleIf: (c) => Boolean(c?.capabilities?.createAny ?? c?.meta?.capabilities?.createAny),
      display: {
        views: DEFAULT_RESOURCE_FIELD_VIEWS,
        sizes: {
          small: { mode: "dateShort" },
          normal: { mode: "dateTime" },
          large: { mode: "dateTime" },
        },
      },
    },
  };
}

export default getResourceFieldDescriptors;

export { truncate };


