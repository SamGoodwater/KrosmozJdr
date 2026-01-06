/**
 * Monster field descriptors (Option B)
 *
 * @description
 * Source de vérité côté frontend pour :
 * - l'affichage (cellules table + vues)
 * - l'édition (forms / bulk)
 *
 * ⚠️ Sécurité : ces descriptors ne sont que de l'UX. Le backend reste la vérité (Policies + filtrage des champs).
 */

/**
 * @typedef {Object} MonsterFieldDescriptor
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

export const DEFAULT_MONSTER_FIELD_VIEWS = Object.freeze({
  table: { size: "small" },
  text: { size: "normal" },
  compact: { size: "small" },
  minimal: { size: "small" },
  extended: { size: "large" },
});

/**
 * Ordre d'affichage "Monster" par vue.
 */
export const MONSTER_VIEW_FIELDS = Object.freeze({
  quickEdit: [
    "size",
    "is_boss",
    "boss_pa",
    "auto_update",
    "dofus_version",
    "dofusdb_id",
  ],
  compact: [
    "creature_name",
    "monster_race",
    "size",
    "is_boss",
    "dofusdb_id",
    "auto_update",
  ],
  extended: [
    "creature_name",
    "monster_race",
    "size",
    "is_boss",
    "boss_pa",
    "dofusdb_id",
    "dofus_version",
    "auto_update",
    "created_at",
    "updated_at",
  ],
});

/**
 * Descriptors "Monster".
 *
 * @param {Object} ctx
 * @returns {Record<string, MonsterFieldDescriptor>}
 */
export function getMonsterFieldDescriptors(ctx = {}) {
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
        views: DEFAULT_MONSTER_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
    creature_name: {
      key: "creature_name",
      label: "Créature",
      icon: "fa-solid fa-dragon",
      format: "text",
      display: {
        views: { ...DEFAULT_MONSTER_FIELD_VIEWS, table: { size: "small", mode: "route" } },
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
    monster_race: {
      key: "monster_race",
      label: "Race",
      icon: "fa-solid fa-users",
      format: "text",
      display: {
        views: DEFAULT_MONSTER_FIELD_VIEWS,
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
    size: {
      key: "size",
      label: "Taille",
      icon: "fa-solid fa-expand",
      format: "enum",
      display: {
        views: DEFAULT_MONSTER_FIELD_VIEWS,
        sizes: { small: { mode: "badge" }, normal: { mode: "badge" }, large: { mode: "badge" } },
      },
      edit: {
        form: {
          type: "select",
          group: "Caractéristiques",
          required: false,
          showInCompact: true,
          options: [
            { value: 0, label: "Minuscule" },
            { value: 1, label: "Petit" },
            { value: 2, label: "Moyen" },
            { value: 3, label: "Grand" },
            { value: 4, label: "Colossal" },
            { value: 5, label: "Gigantesque" },
          ],
          defaultValue: 2,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : Number(v)) },
        },
      },
    },
    is_boss: {
      key: "is_boss",
      label: "Boss",
      icon: "fa-solid fa-crown",
      format: "bool",
      display: {
        views: DEFAULT_MONSTER_FIELD_VIEWS,
        sizes: { small: { mode: "badge" }, normal: { mode: "badge" }, large: { mode: "badge" } },
      },
      edit: {
        form: {
          type: "checkbox",
          group: "Caractéristiques",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" || v === null ? null : Boolean(v)) },
        },
      },
    },
    boss_pa: {
      key: "boss_pa",
      label: "PA Boss",
      icon: "fa-solid fa-bolt",
      format: "text",
      display: {
        views: DEFAULT_MONSTER_FIELD_VIEWS,
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
    dofusdb_id: {
      key: "dofusdb_id",
      label: "DofusDB",
      icon: "fa-solid fa-link",
      format: "text",
      visibleIf: () => canUpdateAny,
      display: {
        views: DEFAULT_MONSTER_FIELD_VIEWS,
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
    dofus_version: {
      key: "dofus_version",
      label: "Version Dofus",
      icon: "fa-solid fa-code-branch",
      format: "text",
      display: {
        views: DEFAULT_MONSTER_FIELD_VIEWS,
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
    auto_update: {
      key: "auto_update",
      label: "Mise à jour auto",
      icon: "fa-solid fa-sync",
      format: "bool",
      display: {
        views: DEFAULT_MONSTER_FIELD_VIEWS,
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
    created_at: {
      key: "created_at",
      label: "Créé le",
      icon: "fa-solid fa-calendar-plus",
      format: "date",
      visibleIf: () => canCreateAny,
      display: {
        views: DEFAULT_MONSTER_FIELD_VIEWS,
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
        views: DEFAULT_MONSTER_FIELD_VIEWS,
        sizes: { small: { mode: "text" }, normal: { mode: "text" }, large: { mode: "text" } },
      },
    },
  };
}

