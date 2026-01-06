/**
 * Creature field descriptors (Option B)
 *
 * @description
 * Source de vérité côté frontend pour :
 * - l'affichage (cellules table + vues)
 * - l'édition (forms / bulk)
 *
 * ⚠️ Sécurité : ces descriptors ne sont que de l'UX. Le backend reste la vérité (Policies + filtrage des champs).
 */

/**
 * @typedef {Object} CreatureFieldDescriptor
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

export const DEFAULT_CREATURE_FIELD_VIEWS = Object.freeze({
  table: { size: "small" },
  text: { size: "normal" },
  compact: { size: "small" },
  minimal: { size: "small" },
  extended: { size: "large" },
});

/**
 * Ordre d'affichage "Creature" par vue.
 */
export const CREATURE_VIEW_FIELDS = Object.freeze({
  quickEdit: [
    "level",
    "hostility",
    "life",
    "usable",
    "is_visible",
  ],
  compact: [
    "level",
    "hostility",
    "life",
    "pa",
    "pm",
    "usable",
    "is_visible",
  ],
  extended: [
    "level",
    "hostility",
    "life",
    "pa",
    "pm",
    "po",
    "usable",
    "is_visible",
    "created_by",
    "created_at",
    "updated_at",
  ],
});

const HOSTILITY_OPTIONS = [
  { value: 0, label: "Amical" },
  { value: 1, label: "Curieux" },
  { value: 2, label: "Neutre" },
  { value: 3, label: "Hostile" },
  { value: 4, label: "Aggressif" },
];

/**
 * Descriptors "Creature".
 *
 * @param {Object} ctx
 * @returns {Record<string, CreatureFieldDescriptor>}
 */
export function getCreatureFieldDescriptors(ctx = {}) {
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
        views: DEFAULT_CREATURE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
    name: {
      key: "name",
      label: "Nom",
      icon: "fa-solid fa-font",
      format: "text",
      display: {
        views: { ...DEFAULT_CREATURE_FIELD_VIEWS, table: { size: "small", mode: "route" } },
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
        views: DEFAULT_CREATURE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: {
          type: "text",
          group: "Caractéristiques",
          placeholder: "Ex: 50",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    hostility: {
      key: "hostility",
      label: "Hostilité",
      icon: "fa-solid fa-exclamation-triangle",
      format: "enum",
      display: {
        views: DEFAULT_CREATURE_FIELD_VIEWS,
        sizes: { small: { mode: "badge" }, normal: { mode: "badge" }, large: { mode: "badge" } },
      },
      edit: {
        form: {
          type: "select",
          group: "Caractéristiques",
          required: false,
          showInCompact: true,
          options: HOSTILITY_OPTIONS,
          defaultValue: 2,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : Number(v)) },
        },
      },
    },
    life: {
      key: "life",
      label: "Vie",
      icon: "fa-solid fa-heart",
      format: "number",
      display: {
        views: DEFAULT_CREATURE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: {
          type: "text",
          group: "Caractéristiques",
          placeholder: "Ex: 30",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    pa: {
      key: "pa",
      label: "PA",
      icon: "fa-solid fa-bolt",
      format: "number",
      display: {
        views: DEFAULT_CREATURE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: {
          type: "text",
          group: "Caractéristiques",
          placeholder: "Ex: 6",
          required: false,
          showInCompact: false,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    pm: {
      key: "pm",
      label: "PM",
      icon: "fa-solid fa-wind",
      format: "number",
      display: {
        views: DEFAULT_CREATURE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: {
          type: "text",
          group: "Caractéristiques",
          placeholder: "Ex: 3",
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
        views: DEFAULT_CREATURE_FIELD_VIEWS,
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
        views: DEFAULT_CREATURE_FIELD_VIEWS,
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
    created_by: {
      key: "created_by",
      label: "Créé par",
      icon: "fa-solid fa-user",
      format: "text",
      visibleIf: () => canCreateAny,
      display: {
        views: DEFAULT_CREATURE_FIELD_VIEWS,
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
        views: DEFAULT_CREATURE_FIELD_VIEWS,
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
        views: DEFAULT_CREATURE_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
  };
}

