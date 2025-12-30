/**
 * Spell field descriptors (Option B)
 *
 * @description
 * Source de vérité côté frontend pour :
 * - l'affichage (cellules table + vues)
 * - l'édition (forms / bulk)
 *
 * ⚠️ Sécurité : ces descriptors ne sont que de l'UX. Le backend reste la vérité (Policies + filtrage des champs).
 */

/**
 * @typedef {Object} SpellFieldDescriptor
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

export const DEFAULT_SPELL_FIELD_VIEWS = Object.freeze({
  table: { size: "small" },
  text: { size: "normal" },
  compact: { size: "small" },
  minimal: { size: "small" },
  extended: { size: "large" },
});

/**
 * Ordre d'affichage "Spell" par vue.
 */
export const SPELL_VIEW_FIELDS = Object.freeze({
  quickEdit: [
    "level",
    "pa",
    "po",
    "area",
    "usable",
    "auto_update",
    "is_visible",
    "description",
    "image",
  ],
  compact: [
    "level",
    "pa",
    "po",
    "area",
    "spell_types",
    "usable",
    "is_visible",
    "auto_update",
    "dofusdb_id",
  ],
  extended: [
    "level",
    "pa",
    "po",
    "area",
    "spell_types",
    "usable",
    "is_visible",
    "auto_update",
    "dofusdb_id",
    "created_by",
    "created_at",
    "updated_at",
  ],
});

/**
 * Descriptors "Spell".
 *
 * @param {Object} ctx
 * @returns {Record<string, SpellFieldDescriptor>}
 */
export function getSpellFieldDescriptors(ctx = {}) {
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
        views: DEFAULT_SPELL_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
    name: {
      key: "name",
      label: "Nom",
      icon: "fa-solid fa-font",
      format: "text",
      display: {
        views: { ...DEFAULT_SPELL_FIELD_VIEWS, table: { size: "small", mode: "route" } },
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
    description: {
      key: "description",
      label: "Description",
      icon: "fa-solid fa-align-left",
      format: "text",
      display: {
        views: DEFAULT_SPELL_FIELD_VIEWS,
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
      display: {
        views: DEFAULT_SPELL_FIELD_VIEWS,
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
    pa: {
      key: "pa",
      label: "PA",
      icon: "fa-solid fa-bolt",
      format: "number",
      display: {
        views: DEFAULT_SPELL_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: {
          type: "text",
          group: "Métier",
          placeholder: "Ex: 3",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    po: {
      key: "po",
      label: "PO",
      icon: "fa-solid fa-crosshairs",
      format: "number",
      display: {
        views: DEFAULT_SPELL_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: {
          type: "text",
          group: "Métier",
          placeholder: "Ex: 1-6",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    area: {
      key: "area",
      label: "Zone",
      icon: "fa-solid fa-expand",
      format: "number",
      display: {
        views: DEFAULT_SPELL_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: {
          type: "number",
          group: "Métier",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : Number(v)) },
        },
      },
    },
    spell_types: {
      key: "spell_types",
      label: "Types",
      icon: "fa-solid fa-tags",
      format: "enum",
      display: {
        views: DEFAULT_SPELL_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
      edit: {
        form: {
          type: "select",
          group: "Métier",
          required: false,
          showInCompact: false,
          multiple: true,
          bulk: { enabled: false },
        },
      },
    },
    dofusdb_id: {
      key: "dofusdb_id",
      label: "DofusDB",
      icon: "fa-solid fa-link",
      format: "link",
      visibleIf: () => canUpdateAny,
      display: {
        views: DEFAULT_SPELL_FIELD_VIEWS,
        sizes: { small: { mode: "route" }, normal: { mode: "route" }, large: { mode: "route" } },
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
        views: DEFAULT_SPELL_FIELD_VIEWS,
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
        views: DEFAULT_SPELL_FIELD_VIEWS,
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
        views: DEFAULT_SPELL_FIELD_VIEWS,
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
    image: {
      key: "image",
      label: "Image",
      icon: "fa-solid fa-image",
      color: "auto",
      format: "image",
      display: {
        views: DEFAULT_SPELL_FIELD_VIEWS,
        sizes: { small: { mode: "thumb" }, normal: { mode: "thumb" }, large: { mode: "thumb" } },
      },
      edit: {
        form: {
          type: "file",
          group: "Image",
          required: false,
          showInCompact: false,
          bulk: { enabled: false },
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
        views: DEFAULT_SPELL_FIELD_VIEWS,
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
        views: DEFAULT_SPELL_FIELD_VIEWS,
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
        views: DEFAULT_SPELL_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
  };
}

