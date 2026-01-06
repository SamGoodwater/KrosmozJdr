/**
 * Consumable field descriptors (Option B)
 *
 * @description
 * Source de vérité côté frontend pour :
 * - l'affichage (cellules table + vues)
 * - l'édition (forms / bulk)
 *
 * ⚠️ Sécurité : ces descriptors ne sont que de l'UX. Le backend reste la vérité (Policies + filtrage des champs).
 */

export const DEFAULT_CONSUMABLE_FIELD_VIEWS = Object.freeze({
  table: { size: "small" },
  text: { size: "normal" },
  compact: { size: "small" },
  minimal: { size: "small" },
  extended: { size: "large" },
});

/**
 * Ordre d'affichage "Consumable" par vue.
 */
export const CONSUMABLE_VIEW_FIELDS = Object.freeze({
  quickEdit: [
    "level",
    "rarity",
    "consumable_type_id",
    "usable",
    "auto_update",
    "is_visible",
    "price",
    "description",
    "image",
  ],
  compact: [
    "name",
    "level",
    "rarity",
    "consumable_type_id",
    "price",
    "usable",
    "is_visible",
    "dofusdb_id",
  ],
  extended: [
    "name",
    "level",
    "rarity",
    "consumable_type_id",
    "price",
    "description",
    "effect",
    "recipe",
    "usable",
    "is_visible",
    "dofusdb_id",
    "created_by",
    "created_at",
    "updated_at",
  ],
});

const RARITY_OPTIONS = [
  { value: 0, label: "Commun" },
  { value: 1, label: "Peu commun" },
  { value: 2, label: "Rare" },
  { value: 3, label: "Très rare" },
  { value: 4, label: "Légendaire" },
  { value: 5, label: "Unique" },
];

/**
 * Descriptors "Consumable".
 *
 * @param {Object} ctx
 * @returns {Record<string, any>}
 */
export function getConsumableFieldDescriptors(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || null;
  const canUpdateAny = Boolean(can?.updateAny);
  const canCreateAny = Boolean(can?.createAny);

  return {
    id: {
      key: "id",
      label: "ID",
      icon: "fa-solid fa-hashtag",
      format: "number",
      visibleIf: () => canCreateAny,
      display: {
        views: DEFAULT_CONSUMABLE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
    name: {
      key: "name",
      label: "Nom",
      icon: "fa-solid fa-font",
      format: "text",
      display: {
        views: { ...DEFAULT_CONSUMABLE_FIELD_VIEWS, table: { size: "small", mode: "route" } },
        sizes: { small: { mode: "route" }, normal: { mode: "route" }, large: { mode: "route" } },
      },
      edit: {
        form: {
          type: "text",
          required: true,
          showInCompact: true,
          bulk: { enabled: false },
        },
      },
    },
    level: {
      key: "level",
      label: "Niveau",
      icon: "fa-solid fa-level-up-alt",
      format: "number",
      display: {
        views: DEFAULT_CONSUMABLE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
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
      icon: "fa-solid fa-gem",
      format: "enum",
      display: {
        views: DEFAULT_CONSUMABLE_FIELD_VIEWS,
        sizes: { small: { mode: "badge" }, normal: { mode: "badge" }, large: { mode: "badge" } },
      },
      edit: {
        form: {
          type: "select",
          group: "Métier",
          required: false,
          showInCompact: true,
          options: RARITY_OPTIONS,
          defaultValue: 0,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : Number(v)) },
        },
      },
    },
    consumable_type_id: {
      key: "consumable_type_id",
      label: "Type",
      icon: "fa-solid fa-tags",
      format: "number",
      display: {
        views: DEFAULT_CONSUMABLE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: {
          type: "select",
          group: "Métier",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : Number(v)) },
        },
      },
    },
    price: {
      key: "price",
      label: "Prix",
      icon: "fa-solid fa-coins",
      format: "text",
      display: {
        views: DEFAULT_CONSUMABLE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: {
          type: "text",
          group: "Métier",
          placeholder: "Ex: 100",
          required: false,
          showInCompact: false,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    description: {
      key: "description",
      label: "Description",
      icon: "fa-solid fa-align-left",
      format: "text",
      display: {
        views: DEFAULT_CONSUMABLE_FIELD_VIEWS,
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
    dofusdb_id: {
      key: "dofusdb_id",
      label: "DofusDB",
      icon: "fa-solid fa-link",
      format: "text",
      visibleIf: () => canUpdateAny,
      display: {
        views: DEFAULT_CONSUMABLE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: {
          type: "text",
          group: "Métadonnées",
          required: false,
          showInCompact: false,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    usable: {
      key: "usable",
      label: "Utilisable",
      icon: "fa-solid fa-check-circle",
      format: "bool",
      display: {
        views: DEFAULT_CONSUMABLE_FIELD_VIEWS,
        sizes: { small: { mode: "badge" }, normal: { mode: "badge" }, large: { mode: "badge" } },
      },
      edit: {
        form: {
          type: "checkbox",
          group: "Statut",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" || v === null ? null : Boolean(v)) },
        },
      },
    },
    is_visible: {
      key: "is_visible",
      label: "Visible",
      icon: "fa-solid fa-eye",
      format: "enum",
      display: {
        views: DEFAULT_CONSUMABLE_FIELD_VIEWS,
        sizes: { small: { mode: "badge" }, normal: { mode: "badge" }, large: { mode: "badge" } },
      },
      edit: {
        form: {
          type: "select",
          group: "Statut",
          required: false,
          showInCompact: true,
          options: [
            { value: "guest", label: "Invité" },
            { value: "user", label: "Utilisateur" },
            { value: "player", label: "Joueur" },
            { value: "game_master", label: "Maître du jeu" },
            { value: "admin", label: "Administrateur" },
          ],
          defaultValue: "guest",
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    auto_update: {
      key: "auto_update",
      label: "Mise à jour auto",
      icon: "fa-solid fa-sync",
      format: "bool",
      display: {
        views: DEFAULT_CONSUMABLE_FIELD_VIEWS,
        sizes: { small: { mode: "badge" }, normal: { mode: "badge" }, large: { mode: "badge" } },
      },
      edit: {
        form: {
          type: "checkbox",
          group: "Statut",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" || v === null ? null : Boolean(v)) },
        },
      },
    },
    created_by: {
      key: "created_by",
      label: "Créé par",
      icon: "fa-solid fa-user",
      format: "text",
      visibleIf: () => canCreateAny,
      display: {
        views: DEFAULT_CONSUMABLE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
    created_at: {
      key: "created_at",
      label: "Créé le",
      icon: "fa-solid fa-calendar-plus",
      format: "date",
      visibleIf: () => canCreateAny,
      display: {
        views: DEFAULT_CONSUMABLE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
    updated_at: {
      key: "updated_at",
      label: "Modifié le",
      icon: "fa-solid fa-calendar-edit",
      format: "date",
      visibleIf: () => canCreateAny,
      display: {
        views: DEFAULT_CONSUMABLE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
  };
}

