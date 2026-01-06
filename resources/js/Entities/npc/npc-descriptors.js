/**
 * Npc field descriptors (Option B)
 *
 * @description
 * Source de vérité côté frontend pour :
 * - l'affichage (cellules table + vues)
 * - l'édition (forms / bulk)
 *
 * ⚠️ Sécurité : ces descriptors ne sont que de l'UX. Le backend reste la vérité (Policies + filtrage des champs).
 */

export const DEFAULT_NPC_FIELD_VIEWS = Object.freeze({
  table: { size: "small" },
  text: { size: "normal" },
  compact: { size: "small" },
  minimal: { size: "small" },
  extended: { size: "large" },
});

/**
 * Ordre d'affichage "Npc" par vue.
 */
export const NPC_VIEW_FIELDS = Object.freeze({
  quickEdit: [
    "classe_id",
    "specialization_id",
    "age",
    "size",
  ],
  compact: [
    "creature_name",
    "classe_id",
    "specialization_id",
  ],
  extended: [
    "creature_name",
    "classe_id",
    "specialization_id",
    "story",
    "historical",
    "age",
    "size",
    "created_at",
    "updated_at",
  ],
});

/**
 * Descriptors "Npc".
 *
 * @param {Object} ctx
 * @returns {Record<string, any>}
 */
export function getNpcFieldDescriptors(ctx = {}) {
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
        views: DEFAULT_NPC_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
    creature_name: {
      key: "creature_name",
      label: "Créature",
      icon: "fa-solid fa-user",
      format: "text",
      display: {
        views: { ...DEFAULT_NPC_FIELD_VIEWS, table: { size: "small", mode: "route" } },
        sizes: { small: { mode: "route" }, normal: { mode: "route" }, large: { mode: "route" } },
      },
      edit: {
        form: {
          type: "select",
          group: "Relations",
          required: true,
          showInCompact: true,
          bulk: { enabled: false },
        },
      },
    },
    classe_id: {
      key: "classe_id",
      label: "Classe",
      icon: "fa-solid fa-user-tie",
      format: "number",
      display: {
        views: DEFAULT_NPC_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: {
          type: "select",
          group: "Relations",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : Number(v)) },
        },
      },
    },
    specialization_id: {
      key: "specialization_id",
      label: "Spécialisation",
      icon: "fa-solid fa-star",
      format: "number",
      display: {
        views: DEFAULT_NPC_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: {
          type: "select",
          group: "Relations",
          required: false,
          showInCompact: false,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : Number(v)) },
        },
      },
    },
    story: {
      key: "story",
      label: "Histoire",
      icon: "fa-solid fa-book",
      format: "text",
      display: {
        views: DEFAULT_NPC_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: {
          type: "textarea",
          group: "Description",
          required: false,
          showInCompact: false,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    historical: {
      key: "historical",
      label: "Historique",
      icon: "fa-solid fa-scroll",
      format: "text",
      display: {
        views: DEFAULT_NPC_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: {
          type: "textarea",
          group: "Description",
          required: false,
          showInCompact: false,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    age: {
      key: "age",
      label: "Âge",
      icon: "fa-solid fa-birthday-cake",
      format: "text",
      display: {
        views: DEFAULT_NPC_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: {
          type: "text",
          group: "Caractéristiques",
          placeholder: "Ex: 25 ans",
          required: false,
          showInCompact: false,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    size: {
      key: "size",
      label: "Taille",
      icon: "fa-solid fa-ruler",
      format: "text",
      display: {
        views: DEFAULT_NPC_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: {
          type: "text",
          group: "Caractéristiques",
          placeholder: "Ex: 1m75",
          required: false,
          showInCompact: false,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    created_at: {
      key: "created_at",
      label: "Créé le",
      icon: "fa-solid fa-calendar-plus",
      format: "date",
      visibleIf: () => canCreateAny,
      display: {
        views: DEFAULT_NPC_FIELD_VIEWS,
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
        views: DEFAULT_NPC_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
  };
}

