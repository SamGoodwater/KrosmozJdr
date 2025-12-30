/**
 * Item field descriptors (Option B)
 *
 * @description
 * Source de vérité côté frontend pour l'affichage (table + vues) et, à terme, l'édition.
 * ⚠️ Sécurité : UX only. Le backend reste la vérité (Policies + validation).
 */

/**
 * @typedef {Object} ItemFieldDescriptor
 * @property {string} key
 * @property {string} label
 * @property {string|null} [icon]
 * @property {"text"|"number"|"bool"|"date"|"image"|"link"|"enum"} [format]
 * @property {(ctx: any) => boolean} [visibleIf]
 * @property {Object} [display]
 * @property {Record<"table"|"text"|"compact"|"minimal"|"extended", { size: "small"|"normal"|"large", mode?: string }>} [display.views]
 * @property {Record<"small"|"normal"|"large", any>} [display.sizes]
 */

export const DEFAULT_ITEM_FIELD_VIEWS = Object.freeze({
  table: { size: "small" },
  text: { size: "normal" },
  compact: { size: "small" },
  minimal: { size: "small" },
  extended: { size: "large" },
});

/**
 * Ordre d'affichage "Item" par vue.
 *
 * @description
 * (v1) On centralise l'ordre ici pour commencer.
 */
export const ITEM_VIEW_FIELDS = Object.freeze({
  quickEdit: ["rarity", "level", "usable", "auto_update", "is_visible", "price", "dofus_version", "description", "image"],
  minimal: ["level", "item_type", "rarity"],
  compact: ["rarity", "item_type", "level"],
  extended: ["rarity", "item_type", "level", "dofusdb_id", "created_by", "created_at", "updated_at"],
});

export function getItemFieldDescriptors(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || null;
  const canUpdateAny = Boolean(can?.updateAny);

  return {
    id: {
      key: "id",
      label: "ID",
      icon: "fa-solid fa-hashtag",
      format: "number",
      visibleIf: () => canUpdateAny,
      display: { views: DEFAULT_ITEM_FIELD_VIEWS, sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } } },
    },
    name: {
      key: "name",
      label: "Nom",
      icon: "fa-solid fa-font",
      format: "text",
      display: {
        views: { ...DEFAULT_ITEM_FIELD_VIEWS, table: { size: "small", mode: "route" } },
        sizes: { small: { mode: "route" }, normal: { mode: "route" }, large: { mode: "route" } },
      },
      edit: { form: { type: "text", required: true, showInCompact: true, bulk: { enabled: false } } },
    },
    description: {
      key: "description",
      label: "Description",
      icon: "fa-solid fa-align-left",
      format: "text",
      display: {
        views: DEFAULT_ITEM_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: {
          type: "textarea",
          group: "Contenu",
          required: false,
          showInCompact: false,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    level: {
      key: "level",
      label: "Niveau",
      icon: "fa-solid fa-level-up-alt",
      format: "number",
      display: { views: DEFAULT_ITEM_FIELD_VIEWS, sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } } },
      edit: {
        form: {
          type: "text",
          group: "Métier",
          placeholder: "Ex: 50",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    rarity: {
      key: "rarity",
      label: "Rareté",
      icon: "fa-solid fa-star",
      format: "enum",
      display: { views: DEFAULT_ITEM_FIELD_VIEWS, sizes: { small: { mode: "badge" }, normal: { mode: "badge" }, large: { mode: "badge" } } },
      edit: {
        form: {
          type: "select",
          group: "Métier",
          help: "Rareté stockée en base comme entier (0..4).",
          required: false,
          showInCompact: true,
          options: [
            { value: 0, label: "Commun" },
            { value: 1, label: "Peu commun" },
            { value: 2, label: "Rare" },
            { value: 3, label: "Épique" },
            { value: 4, label: "Légendaire" },
          ],
          defaultValue: 0,
          bulk: { enabled: true, nullable: false, build: (v) => Number(v) },
        },
      },
    },
    usable: {
      key: "usable",
      label: "Utilisable",
      icon: "fa-solid fa-check",
      format: "bool",
      display: { views: DEFAULT_ITEM_FIELD_VIEWS, sizes: { small: { mode: "badge" }, normal: { mode: "badge" }, large: { mode: "badge" } } },
      edit: {
        form: {
          type: "checkbox",
          group: "Statut",
          required: false,
          showInCompact: true,
          defaultValue: false,
          bulk: { enabled: true, nullable: false, build: (v) => v === "1" || v === true },
        },
      },
    },
    auto_update: {
      key: "auto_update",
      label: "Auto-update",
      icon: "fa-solid fa-arrows-rotate",
      format: "bool",
      visibleIf: () => canUpdateAny,
      display: { views: DEFAULT_ITEM_FIELD_VIEWS, sizes: { small: { mode: "badge" }, normal: { mode: "badge" }, large: { mode: "badge" } } },
      edit: {
        form: {
          type: "checkbox",
          group: "Statut",
          required: false,
          showInCompact: true,
          defaultValue: false,
          bulk: { enabled: true, nullable: false, build: (v) => v === "1" || v === true },
        },
      },
    },
    is_visible: {
      key: "is_visible",
      label: "Visibilité",
      icon: "fa-solid fa-eye",
      format: "enum",
      display: { views: DEFAULT_ITEM_FIELD_VIEWS, sizes: { small: { mode: "badge" }, normal: { mode: "badge" }, large: { mode: "badge" } } },
      edit: {
        form: {
          type: "select",
          group: "Statut",
          required: false,
          showInCompact: true,
          options: [
            { value: "guest", label: "Invité" },
            { value: "user", label: "Utilisateur" },
            { value: "game_master", label: "Maître de jeu" },
            { value: "admin", label: "Administrateur" },
          ],
          defaultValue: "guest",
          bulk: { enabled: true, nullable: false, build: (v) => v },
        },
      },
    },
    price: {
      key: "price",
      label: "Prix",
      icon: "fa-solid fa-coins",
      format: "text",
      display: { views: DEFAULT_ITEM_FIELD_VIEWS, sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } } },
      edit: {
        form: {
          type: "text",
          group: "Métadonnées",
          placeholder: "Ex: 12 000 kamas",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    dofus_version: {
      key: "dofus_version",
      label: "Version Dofus",
      icon: "fa-solid fa-code-branch",
      format: "text",
      display: { views: DEFAULT_ITEM_FIELD_VIEWS, sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } } },
      edit: {
        form: {
          type: "text",
          group: "Métadonnées",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    item_type: {
      key: "item_type",
      label: "Type",
      icon: "fa-solid fa-tags",
      format: "text",
      display: { views: DEFAULT_ITEM_FIELD_VIEWS, sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } } },
    },
    image: {
      key: "image",
      label: "Image",
      icon: "fa-solid fa-image",
      format: "image",
      display: {
        views: DEFAULT_ITEM_FIELD_VIEWS,
        sizes: { small: { mode: "thumb" }, normal: { mode: "thumb" }, large: { mode: "thumb" } },
      },
      edit: {
        form: {
          type: "text",
          label: "Image (URL)",
          group: "Image",
          required: false,
          showInCompact: false,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    dofusdb_id: {
      key: "dofusdb_id",
      label: "DofusDB",
      icon: "fa-solid fa-up-right-from-square",
      format: "link",
      visibleIf: () => canUpdateAny,
      display: { views: DEFAULT_ITEM_FIELD_VIEWS, sizes: { small: { mode: "route" }, normal: { mode: "route" }, large: { mode: "route" } } },
    },
    created_by: {
      key: "created_by",
      label: "Créé par",
      icon: "fa-solid fa-user",
      format: "text",
      visibleIf: () => canUpdateAny,
      display: { views: DEFAULT_ITEM_FIELD_VIEWS, sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } } },
    },
    created_at: {
      key: "created_at",
      label: "Créé le",
      icon: "fa-solid fa-calendar-plus",
      format: "date",
      visibleIf: () => canUpdateAny,
      display: { views: DEFAULT_ITEM_FIELD_VIEWS, sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } } },
    },
    updated_at: {
      key: "updated_at",
      label: "Modifié le",
      icon: "fa-solid fa-calendar-check",
      format: "date",
      visibleIf: () => canUpdateAny,
      display: { views: DEFAULT_ITEM_FIELD_VIEWS, sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } } },
    },
  };
}


