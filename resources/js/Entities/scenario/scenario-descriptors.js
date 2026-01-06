/**
 * Scenario field descriptors (Option B)
 *
 * @description
 * Source de vérité côté frontend pour :
 * - l'affichage (cellules table + vues)
 * - l'édition (forms / bulk)
 *
 * ⚠️ Sécurité : ces descriptors ne sont que de l'UX. Le backend reste la vérité (Policies + filtrage des champs).
 */

export const DEFAULT_SCENARIO_FIELD_VIEWS = Object.freeze({
  table: { size: "small" },
  text: { size: "normal" },
  compact: { size: "small" },
  minimal: { size: "small" },
  extended: { size: "large" },
});

/**
 * Ordre d'affichage "Scenario" par vue.
 */
export const SCENARIO_VIEW_FIELDS = Object.freeze({
  quickEdit: [
    "state",
    "is_public",
    "usable",
    "is_visible",
    "description",
    "keyword",
  ],
  compact: [
    "name",
    "slug",
    "state",
    "is_public",
  ],
  extended: [
    "name",
    "slug",
    "state",
    "is_public",
    "description",
    "keyword",
    "created_by",
    "created_at",
    "updated_at",
  ],
});

/**
 * Descriptors "Scenario".
 *
 * @param {Object} ctx
 * @returns {Record<string, any>}
 */
export function getScenarioFieldDescriptors(ctx = {}) {
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
        views: DEFAULT_SCENARIO_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
    name: {
      key: "name",
      label: "Nom",
      icon: "fa-solid fa-font",
      format: "text",
      display: {
        views: { ...DEFAULT_SCENARIO_FIELD_VIEWS, table: { size: "small", mode: "route" } },
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
    slug: {
      key: "slug",
      label: "Slug",
      icon: "fa-solid fa-link",
      format: "text",
      display: {
        views: DEFAULT_SCENARIO_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: {
          type: "text",
          group: "Métadonnées",
          required: true,
          showInCompact: false,
          bulk: { enabled: false },
        },
      },
    },
    state: {
      key: "state",
      label: "État",
      icon: "fa-solid fa-info-circle",
      format: "number",
      display: {
        views: DEFAULT_SCENARIO_FIELD_VIEWS,
        sizes: { small: { mode: "badge" }, normal: { mode: "badge" }, large: { mode: "badge" } },
      },
      edit: {
        form: {
          type: "text",
          group: "Statut",
          placeholder: "Ex: 0",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : Number(v)) },
        },
      },
    },
    is_public: {
      key: "is_public",
      label: "Public",
      icon: "fa-solid fa-globe",
      format: "bool",
      display: {
        views: DEFAULT_SCENARIO_FIELD_VIEWS,
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
    description: {
      key: "description",
      label: "Description",
      icon: "fa-solid fa-align-left",
      format: "text",
      display: {
        views: DEFAULT_SCENARIO_FIELD_VIEWS,
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
    keyword: {
      key: "keyword",
      label: "Mots-clés",
      icon: "fa-solid fa-tags",
      format: "text",
      display: {
        views: DEFAULT_SCENARIO_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: {
          type: "text",
          group: "Métadonnées",
          placeholder: "Ex: aventure, fantasy",
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
        views: DEFAULT_SCENARIO_FIELD_VIEWS,
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
        views: DEFAULT_SCENARIO_FIELD_VIEWS,
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
        views: DEFAULT_SCENARIO_FIELD_VIEWS,
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
        views: DEFAULT_SCENARIO_FIELD_VIEWS,
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
        views: DEFAULT_SCENARIO_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
  };
}

