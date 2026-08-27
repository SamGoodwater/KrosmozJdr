/**
 * Spell field descriptors — Version simplifiée
 *
 * @description
 * Source de vérité côté frontend pour :
 * - Configuration tableau (affichage des cellules selon la taille xs-xl)
 * - Configuration formulaires (édition simple et bulk)
 *
 * ⚠️ Les vues (Large, Compact, Minimal, Text) sont maintenant des composants Vue manuels.
 * ⚠️ Sécurité : ces descriptors ne sont que de l'UX. Le backend reste la vérité (Policies + filtrage des champs).
 *
 * @example
 * import { getSpellFieldDescriptors } from "@/Entities/spell/spell-descriptors";
 * const descriptors = getSpellFieldDescriptors({ meta });
 */

import {
  getEntityStateOptions,
  getUserRoleOptions,
  getSpellCategoryOptions,
} from "@/Utils/Entity/SharedConstants";

/** @returns {Array<{ value: string, label: string }>} */
export const SPELL_RESOLUTION_MODE_OPTIONS = () => [
  { value: "attack_roll", label: "Jet d'attaque (vs CA)" },
  { value: "saving_throw", label: "Jet de sauvegarde" },
  { value: "auto_success", label: "Réussite automatique" },
];

/** @returns {Array<{ value: string, label: string }>} */
export const SAVE_ABILITY_OPTIONS = () => [
  { value: "", label: "—" },
  { value: "strong", label: "Force" },
  { value: "intel", label: "Intelligence" },
  { value: "chance", label: "Chance" },
  { value: "agi", label: "Agilité" },
  { value: "sagesse", label: "Sagesse" },
  { value: "vitality", label: "Vitalité" },
];

/** @returns {Array<{ value: string, label: string }>} */
export const SPELL_TARGET_TYPE_OPTIONS = () => [
  { value: "", label: "Non renseigné" },
  { value: "direct", label: "Direct" },
  { value: "trap", label: "Piège" },
  { value: "glyph", label: "Glyphe" },
];

/**
 * @typedef {Object} SpellFieldDescriptor
 * @property {string} key - Clé unique du champ
 * @property {string} label - Libellé affiché
 * @property {string} [icon] - Icône FontAwesome
 * @property {(ctx: any) => boolean} [visibleIf] - Fonction conditionnelle pour la visibilité
 * @property {(ctx: any) => boolean} [editableIf] - Fonction conditionnelle pour l'édition
 * @property {Object} [display] - Configuration de l'affichage dans les tableaux
 * @property {Record<"xs"|"sm"|"md"|"lg"|"xl", {mode?: string, truncate?: number}>} [display.sizes] - Configuration par taille d'écran
 * @property {Object} [edit] - Configuration de l'édition
 * @property {Object} [edit.form] - Configuration du formulaire d'édition
 * @property {"text"|"textarea"|"select"|"checkbox"|"number"|"date"|"file"} [edit.form.type] - Type de champ
 * @property {string} [edit.form.label] - Libellé spécifique pour le formulaire
 * @property {string} [edit.form.group] - Groupe de champs
 * @property {string} [edit.form.help] - Texte d'aide
 * @property {boolean} [edit.form.required] - Champ obligatoire
 * @property {any} [edit.form.defaultValue] - Valeur par défaut
 * @property {Array<{value: any, label: string}>|Function} [edit.form.options] - Options pour les selects
 * @property {Object} [edit.form.bulk] - Configuration pour l'édition en masse
 * @property {boolean} [edit.form.bulk.enabled] - Activer l'édition en masse
 * @property {boolean} [edit.form.bulk.nullable] - Permettre null/vide en bulk
 * @property {Function} [edit.form.bulk.build] - ⚠️ DÉPRÉCIÉ : Les transformations sont maintenant dans les mappers (ex: ResourceMapper.fromBulkForm())
 */

/**
 * Champs affichés dans le panneau d'édition rapide (sélection multiple).
 * ⚠️ IMPORTANT : Doit rester aligné avec le backend (bulk controller).
 */
// Les champs quickedit sont maintenant définis dans _quickeditConfig.fields

/**
 * Retourne les descripteurs de tous les champs de l'entité "Spell".
 * 
 * @param {Object} ctx - Contexte d'exécution
 * @param {Object} [ctx.capabilities] - Permissions disponibles (ou ctx.meta.capabilities)
 * @param {Array} [ctx.spellTypes] - Liste des types de sorts (ou ctx.meta.spellTypes)
 * @returns {Record<string, SpellFieldDescriptor>} Objet avec tous les descripteurs
 */
export function getSpellFieldDescriptors(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || null;
  const canUpdateAny = Boolean(can?.updateAny);
  const canCreateAny = Boolean(can?.createAny);
  
  const spellTypes = Array.isArray(ctx?.spellTypes) 
    ? ctx.spellTypes 
    : (Array.isArray(ctx?.meta?.spellTypes) ? ctx.meta.spellTypes : []);

  return {
    id: {
      key: "id",
      label: "ID",
      icon: "fa-solid fa-hashtag",
      visibleIf: () => canCreateAny,
      table: {
        sortable: true,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        filterable: { id: "id", type: "text", defaultVisible: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: {
        sizes: {
          xs: { mode: "text" },
          sm: { mode: "text" },
          md: { mode: "text" },
          lg: { mode: "text" },
          xl: { mode: "text" },
        },
      },
    },
    name: {
      key: "name",
      label: "Nom",
      icon: "fa-solid fa-font",
      table: {
        order: 10,
        sortable: true,
        searchable: true,
        defaultVisible: { xs: true, sm: true, md: true, lg: true, xl: true },
        cell: { sizes: { xs: { mode: "route", truncate: 15 }, sm: { mode: "route", truncate: 20 }, md: { mode: "route", truncate: 30 }, lg: { mode: "route", truncate: 40 }, xl: { mode: "route" } } },
      },
      display: {
        sizes: {
          xs: { mode: "route", truncate: 15 },
          sm: { mode: "route", truncate: 20 },
          md: { mode: "route", truncate: 30 },
          lg: { mode: "route", truncate: 40 },
          xl: { mode: "route" },
        },
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
      table: {
        searchable: true,
        filterable: { id: "description", type: "text", defaultVisible: false },
        defaultVisible: { xs: false, sm: false, md: true, lg: true, xl: true },
        cell: { sizes: { xs: { mode: "text", truncate: 20 }, sm: { mode: "text", truncate: 30 }, md: { mode: "text", truncate: 50 }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: {
        sizes: {
          xs: { mode: "text", truncate: 20 },
          sm: { mode: "text", truncate: 30 },
          md: { mode: "text", truncate: 50 },
          lg: { mode: "text" },
          xl: { mode: "text" },
        },
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
      table: {
        order: 200,
        sortable: true,
        filterable: {
          id: "level",
          type: "multi",
          defaultVisible: true,
          ui: {
            optionsMode: "rows",
            maxOptions: 250,
          },
        },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "badge" }, sm: { mode: "badge" }, md: { mode: "badge" }, lg: { mode: "badge" }, xl: { mode: "badge" } } },
      },
      display: {
        sizes: {
          xs: { mode: "badge" },
          sm: { mode: "badge" },
          md: { mode: "badge" },
          lg: { mode: "badge" },
          xl: { mode: "badge" },
        },
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
      table: {
        order: 150,
        sortable: true,
        filterable: { id: "pa", type: "multi", defaultVisible: false },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: {
        sizes: {
          xs: { mode: "text" },
          sm: { mode: "text" },
          md: { mode: "text" },
          lg: { mode: "text" },
          xl: { mode: "text" },
        },
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
      table: {
        order: 151,
        sortable: true,
        filterable: { id: "po", type: "multi", defaultVisible: false },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: {
        sizes: {
          xs: { mode: "text" },
          sm: { mode: "text" },
          md: { mode: "text" },
          lg: { mode: "text" },
          xl: { mode: "text" },
        },
      },
    },
    po_min: {
      key: "po_min",
      label: "Portée min",
      icon: "fa-solid fa-crosshairs",
      edit: {
        form: {
          type: "text",
          group: "Métier",
          placeholder: "0 = soi, 1 = cac, ou formule [level]",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    po_max: {
      key: "po_max",
      label: "Portée max",
      icon: "fa-solid fa-crosshairs",
      edit: {
        form: {
          type: "text",
          group: "Métier",
          placeholder: "Valeur ou formule",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    area: {
      key: "area",
      label: "Zone d'effet",
      icon: "fa-solid fa-shapes",
      table: {
        order: 152,
        sortable: true,
        filterable: { id: "area", type: "multi", defaultVisible: false },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "chips" }, sm: { mode: "chips" }, md: { mode: "chips" }, lg: { mode: "chips" }, xl: { mode: "chips" } } },
      },
      display: {
        sizes: {
          xs: { mode: "chips" },
          sm: { mode: "chips" },
          md: { mode: "chips" },
          lg: { mode: "chips" },
          xl: { mode: "chips" },
        },
      },
      edit: {
        form: {
          type: "text",
          group: "Métier",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : Number(v)) },
        },
      },
    },
    allows_reaction: {
      key: "allows_reaction",
      label: "Réaction",
      icon: "fa-solid fa-bolt",
      table: {
        order: 151,
        sortable: true,
        filterable: { id: "allows_reaction", type: "boolean", defaultVisible: false },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: {
          sizes: {
            xs: { mode: "badge" },
            sm: { mode: "badge" },
            md: { mode: "badge" },
            lg: { mode: "badge" },
            xl: { mode: "badge" },
          },
        },
      },
      display: {
        sizes: {
          xs: { mode: "badge" },
          sm: { mode: "badge" },
          md: { mode: "badge" },
          lg: { mode: "badge" },
          xl: { mode: "badge" },
        },
      },
      edit: {
        form: {
          type: "checkbox",
          group: "Métier",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: false, build: (v) => Boolean(v) },
        },
      },
    },
    cast_per_turn: {
      key: "cast_per_turn",
      label: "Lancers / tour",
      icon: "fa-solid fa-repeat",
      table: {
        order: 160,
        sortable: true,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: {
        sizes: {
          xs: { mode: "text" },
          sm: { mode: "text" },
          md: { mode: "text" },
          lg: { mode: "text" },
          xl: { mode: "text" },
        },
      },
      edit: {
        form: {
          type: "text",
          group: "Métier",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    cast_per_target: {
      key: "cast_per_target",
      label: "Lancers / cible",
      icon: "fa-solid fa-bullseye",
      table: {
        order: 161,
        sortable: true,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: {
        sizes: {
          xs: { mode: "text" },
          sm: { mode: "text" },
          md: { mode: "text" },
          lg: { mode: "text" },
          xl: { mode: "text" },
        },
      },
      edit: {
        form: {
          type: "text",
          group: "Métier",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    number_between_two_cast: {
      key: "number_between_two_cast",
      label: "Délai relance",
      icon: "fa-solid fa-clock-rotate-left",
      table: {
        order: 162,
        sortable: true,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: {
        sizes: {
          xs: { mode: "text" },
          sm: { mode: "text" },
          md: { mode: "text" },
          lg: { mode: "text" },
          xl: { mode: "text" },
        },
      },
      edit: {
        form: {
          type: "text",
          group: "Métier",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    duration: {
      key: "duration",
      label: "Durée",
      icon: "fa-solid fa-hourglass",
      table: {
        order: 163,
        sortable: true,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: {
        sizes: {
          xs: { mode: "text" },
          sm: { mode: "text" },
          md: { mode: "text" },
          lg: { mode: "text" },
          xl: { mode: "text" },
        },
      },
      edit: {
        form: {
          type: "text",
          group: "Métier",
          required: false,
          showInCompact: true,
          placeholder: "Ex : instantané, 1 tour, 3 tours, concentration",
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    sight_line: {
      key: "sight_line",
      label: "Ligne de vue",
      icon: "fa-solid fa-eye",
      table: {
        order: 164,
        sortable: true,
        filterable: { id: "sight_line", type: "boolean", defaultVisible: false },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "badge" }, sm: { mode: "badge" }, md: { mode: "badge" }, lg: { mode: "badge" }, xl: { mode: "badge" } } },
      },
      display: {
        sizes: {
          xs: { mode: "badge" },
          sm: { mode: "badge" },
          md: { mode: "badge" },
          lg: { mode: "badge" },
          xl: { mode: "badge" },
        },
      },
      edit: {
        form: {
          type: "checkbox",
          group: "Métier",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: false, build: (v) => Boolean(v) },
        },
      },
    },
    cast_in_line: {
      key: "cast_in_line",
      label: "Lancer en ligne",
      icon: "fa-solid fa-arrows-left-right",
      edit: {
        form: {
          type: "checkbox",
          group: "Métier",
          required: false,
          defaultValue: false,
          bulk: { enabled: true, nullable: false, build: (v) => Boolean(v) },
        },
      },
    },
    cast_in_diagonal: {
      key: "cast_in_diagonal",
      label: "Lancer en diagonale",
      icon: "fa-solid fa-arrow-up-right",
      edit: {
        form: {
          type: "checkbox",
          group: "Métier",
          required: false,
          defaultValue: false,
          bulk: { enabled: true, nullable: false, build: (v) => Boolean(v) },
        },
      },
    },
    target_type: {
      key: "target_type",
      label: "Type de ciblage",
      icon: "fa-solid fa-bullseye",
      edit: {
        form: {
          type: "select",
          group: "Métier",
          required: false,
          options: SPELL_TARGET_TYPE_OPTIONS,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    max_stack: {
      key: "max_stack",
      label: "Cumul maximal",
      icon: "fa-solid fa-layer-group",
      edit: {
        form: {
          type: "number",
          group: "Métier",
          required: false,
          min: 0,
          max: 10,
          defaultValue: 0,
          help: "0 = non limité.",
          bulk: { enabled: true, nullable: false, build: (v) => Number(v) },
        },
      },
    },
    global_cooldown: {
      key: "global_cooldown",
      label: "Relance globale",
      icon: "fa-solid fa-clock",
      edit: {
        form: {
          type: "number",
          group: "Métier",
          required: false,
          min: 0,
          max: 10,
          defaultValue: 0,
          bulk: { enabled: true, nullable: false, build: (v) => Number(v) },
        },
      },
    },
    po_editable: {
      key: "po_editable",
      label: "PO modifiable",
      icon: "fa-solid fa-arrows-left-right",
      table: {
        order: 165,
        sortable: true,
        filterable: { id: "po_editable", type: "boolean", defaultVisible: false },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "badge" }, sm: { mode: "badge" }, md: { mode: "badge" }, lg: { mode: "badge" }, xl: { mode: "badge" } } },
      },
      display: {
        sizes: {
          xs: { mode: "badge" },
          sm: { mode: "badge" },
          md: { mode: "badge" },
          lg: { mode: "badge" },
          xl: { mode: "badge" },
        },
      },
      edit: {
        form: {
          type: "checkbox",
          group: "Métier",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: false, build: (v) => Boolean(v) },
        },
      },
    },
    casting_time: {
      key: "casting_time",
      label: "Temps d'incantation",
      icon: "fa-solid fa-hourglass-half",
      table: {
        order: 166,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: {
        sizes: {
          xs: { mode: "text" },
          sm: { mode: "text" },
          md: { mode: "text" },
          lg: { mode: "text" },
          xl: { mode: "text" },
        },
      },
      edit: {
        form: {
          type: "text",
          group: "Métier",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    ritual_available: {
      key: "ritual_available",
      label: "Rituel disponible",
      icon: "fa-solid fa-book",
      table: {
        sortable: true,
        filterable: { id: "ritual_available", type: "boolean", defaultVisible: false },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "badge" }, sm: { mode: "badge" }, md: { mode: "badge" }, lg: { mode: "badge" }, xl: { mode: "badge" } } },
      },
      display: {
        sizes: {
          xs: { mode: "badge" },
          sm: { mode: "badge" },
          md: { mode: "badge" },
          lg: { mode: "badge" },
          xl: { mode: "badge" },
        },
      },
      edit: {
        form: {
          type: "checkbox",
          group: "Métier",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" || v === null ? null : Boolean(v)) },
        },
      },
    },
    is_magic: {
      key: "is_magic",
      label: "Physique / Wakfu",
      icon: "fa-solid fa-wand-magic",
      table: {
        sortable: true,
        filterable: { id: "is_magic", type: "boolean", defaultVisible: false },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "badge" }, sm: { mode: "badge" }, md: { mode: "badge" }, lg: { mode: "badge" }, xl: { mode: "badge" } } },
      },
      display: {
        sizes: {
          xs: { mode: "badge" },
          sm: { mode: "badge" },
          md: { mode: "badge" },
          lg: { mode: "badge" },
          xl: { mode: "badge" },
        },
      },
      edit: {
        form: {
          type: "physiqueWakfu",
          group: "Métier",
          required: false,
          showInCompact: true,
          defaultValue: true,
          bulk: { enabled: true, nullable: false, build: (v) => Boolean(v) },
        },
      },
    },
    element: {
      key: "element",
      label: "Élément",
      icon: "fa-solid fa-fire",
      table: {
        order: 153,
        sortable: true,
        filterable: { id: "element", type: "multi", defaultVisible: false },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "badge" }, sm: { mode: "badge" }, md: { mode: "badge" }, lg: { mode: "badge" }, xl: { mode: "badge" } } },
      },
      display: {
        sizes: {
          xs: { mode: "badge" },
          sm: { mode: "badge" },
          md: { mode: "badge" },
          lg: { mode: "badge" },
          xl: { mode: "badge" },
        },
      },
      edit: {
        form: {
          type: "elementPrimaries",
          group: "Métier",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : Number(v)) },
        },
      },
    },
    category: {
      key: "category",
      label: "Catégorie",
      icon: "fa-solid fa-tag",
      table: {
        sortable: true,
        filterable: { id: "category", type: "multi", defaultVisible: false },
        defaultVisible: { xs: false, sm: false, md: true, lg: true, xl: true },
        cell: { sizes: { xs: { mode: "badge" }, sm: { mode: "badge" }, md: { mode: "badge" }, lg: { mode: "badge" }, xl: { mode: "badge" } } },
      },
      display: {
        sizes: {
          xs: { mode: "badge" },
          sm: { mode: "badge" },
          md: { mode: "badge" },
          lg: { mode: "badge" },
          xl: { mode: "badge" },
        },
      },
      edit: {
        form: {
          type: "select",
          group: "Métier",
          required: false,
          showInCompact: true,
          options: getSpellCategoryOptions,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : Number(v)) },
        },
      },
    },
    resolution_mode: {
      key: "resolution_mode",
      label: "Résolution",
      icon: "fa-solid fa-dice-d20",
      edit: {
        form: {
          type: "select",
          group: "Combat",
          required: false,
          showInCompact: true,
          options: SPELL_RESOLUTION_MODE_OPTIONS,
          defaultValue: "attack_roll",
          bulk: { enabled: false },
        },
      },
    },
    attack_characteristic_key: {
      key: "attack_characteristic_key",
      label: "Carac. attaque",
      icon: "fa-solid fa-bullseye",
      edit: {
        form: {
          type: "select",
          group: "Combat",
          required: false,
          showInCompact: true,
          options: SAVE_ABILITY_OPTIONS,
          bulk: { enabled: false },
        },
      },
    },
    save_characteristic_key: {
      key: "save_characteristic_key",
      label: "Carac. sauvegarde",
      icon: "fa-solid fa-shield-halved",
      edit: {
        form: {
          type: "select",
          group: "Combat",
          required: false,
          showInCompact: true,
          options: SAVE_ABILITY_OPTIONS,
          bulk: { enabled: false },
        },
      },
    },
    save_dc_formula: {
      key: "save_dc_formula",
      label: "Formule DD",
      icon: "fa-solid fa-calculator",
      edit: {
        form: {
          type: "text",
          group: "Combat",
          required: false,
          showInCompact: true,
          placeholder: "10 + modificateur de caractéristique",
          bulk: { enabled: false },
        },
      },
    },
    save_success_note: {
      key: "save_success_note",
      label: "Effet si sauvegarde réussie",
      icon: "fa-solid fa-note-sticky",
      edit: {
        form: {
          type: "textarea",
          group: "Combat",
          required: false,
          showInCompact: false,
          bulk: { enabled: false },
        },
      },
    },
    state: {
      key: "state",
      label: "État",
      icon: "fa-solid fa-circle-info",
      table: {
        sortable: true,
        filterable: {
          id: "state",
          type: "multi",
          defaultVisible: true,
          options: getEntityStateOptions(),
        },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "badge" }, sm: { mode: "badge" }, md: { mode: "badge" }, lg: { mode: "badge" }, xl: { mode: "badge" } } },
      },
      display: {
        sizes: {
          xs: { mode: "badge" },
          sm: { mode: "badge" },
          md: { mode: "badge" },
          lg: { mode: "badge" },
          xl: { mode: "badge" },
        },
      },
      edit: {
        form: {
          type: "select",
          group: "Statut",
          required: false,
          showInCompact: true,
          options: getEntityStateOptions,
          defaultValue: "draft",
          bulk: { enabled: true, nullable: false, build: (v) => String(v) },
        },
      },
    },
    auto_update: {
      key: "auto_update",
      label: "Auto-update",
      icon: "fa-solid fa-arrows-rotate",
      visibleIf: () => canUpdateAny,
      table: {
        sortable: false,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "badge" }, sm: { mode: "badge" }, md: { mode: "badge" }, lg: { mode: "badge" }, xl: { mode: "badge" } } },
      },
      display: {
        sizes: {
          xs: { mode: "badge" },
          sm: { mode: "badge" },
          md: { mode: "badge" },
          lg: { mode: "badge" },
          xl: { mode: "badge" },
        },
      },
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
    read_level: {
      key: "read_level",
      label: "Lecture (min.)",
      icon: "fa-solid fa-eye",
      table: {
        sortable: false,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "badge" }, sm: { mode: "badge" }, md: { mode: "badge" }, lg: { mode: "badge" }, xl: { mode: "badge" } } },
      },
      display: {
        sizes: {
          xs: { mode: "badge" },
          sm: { mode: "badge" },
          md: { mode: "badge" },
          lg: { mode: "badge" },
          xl: { mode: "badge" },
        },
      },
      edit: {
        form: {
          type: "select",
          group: "Statut",
          required: false,
          showInCompact: true,
          options: getUserRoleOptions,
          defaultValue: 0,
          bulk: { enabled: true, nullable: false, build: (v) => Number(v) },
        },
      },
    },
    write_level: {
      key: "write_level",
      label: "Écriture (min.)",
      icon: "fa-solid fa-pen-to-square",
      table: {
        sortable: false,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "badge" }, sm: { mode: "badge" }, md: { mode: "badge" }, lg: { mode: "badge" }, xl: { mode: "badge" } } },
      },
      display: {
        sizes: {
          xs: { mode: "badge" },
          sm: { mode: "badge" },
          md: { mode: "badge" },
          lg: { mode: "badge" },
          xl: { mode: "badge" },
        },
      },
      edit: {
        form: {
          type: "select",
          group: "Statut",
          required: false,
          showInCompact: true,
          options: getUserRoleOptions,
          defaultValue: 4,
          bulk: { enabled: true, nullable: false, build: (v) => Number(v) },
        },
      },
    },
    image: {
      key: "image",
      label: "Image",
      icon: "fa-solid fa-image",
      table: {
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "thumb" }, sm: { mode: "thumb" }, md: { mode: "thumb" }, lg: { mode: "thumb" }, xl: { mode: "thumb" } } },
      },
      display: {
        sizes: {
          xs: { mode: "thumb" },
          sm: { mode: "thumb" },
          md: { mode: "thumb" },
          lg: { mode: "thumb" },
          xl: { mode: "thumb" },
        },
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
    spell_types: {
      key: "spell_types",
      label: "Types",
      icon: "fa-solid fa-tags",
      table: {
        sortable: true,
        filterable: { id: "types", type: "multi", defaultVisible: true, defaultByCatalog: true },
        defaultVisible: { xs: false, sm: false, md: false, lg: true, xl: true },
        cell: { sizes: { xs: { mode: "badge" }, sm: { mode: "badge" }, md: { mode: "badge" }, lg: { mode: "badge" }, xl: { mode: "badge" } } },
      },
      display: {
        sizes: {
          xs: { mode: "badge" },
          sm: { mode: "badge" },
          md: { mode: "badge" },
          lg: { mode: "badge" },
          xl: { mode: "badge" },
        },
      },
      edit: {
        form: {
          type: "select",
          group: "Métier",
          required: false,
          showInCompact: false,
          multiple: true,
          options: () => [
            { value: "", label: "—" },
            ...spellTypes.map((t) => ({
              value: t.id,
              label: t.name,
              color: t.color ?? null,
              icon: t.icon ?? null,
            })),
          ],
          bulk: { enabled: false },
        },
      },
    },
    effect_summary: {
      key: "effect_summary",
      label: "Effets",
      icon: "fa-solid fa-wand-magic-sparkles",
      table: {
        order: 30,
        type: "chips",
        searchable: true,
        filterable: { id: "sub_effect", type: "multi", defaultVisible: false, label: "Action (sous-effet)" },
        defaultVisible: { xs: false, sm: false, md: true, lg: true, xl: true },
        cell: { sizes: { xs: { mode: "chips" }, sm: { mode: "chips" }, md: { mode: "chips" }, lg: { mode: "chips" }, xl: { mode: "chips" } } },
      },
      display: {
        sizes: {
          xs: { mode: "chips" },
          sm: { mode: "chips" },
          md: { mode: "chips" },
          lg: { mode: "chips" },
          xl: { mode: "chips" },
        },
      },
    },
    spell_summary_profile: {
      key: "spell_summary_profile",
      label: "Profil",
      icon: "fa-solid fa-layer-group",
      table: {
        order: 20,
        type: "chips",
        searchable: true,
        defaultVisible: { xs: false, sm: false, md: true, lg: true, xl: true },
        cell: { sizes: { xs: { mode: "chips" }, sm: { mode: "chips" }, md: { mode: "chips" }, lg: { mode: "chips" }, xl: { mode: "chips" } } },
      },
      display: {
        sizes: {
          xs: { mode: "chips" },
          sm: { mode: "chips" },
          md: { mode: "chips" },
          lg: { mode: "chips" },
          xl: { mode: "chips" },
        },
      },
    },
    dofusdb_id: {
      key: "dofusdb_id",
      label: "DofusDB",
      icon: "fa-solid fa-up-right-from-square",
      visibleIf: () => canUpdateAny,
      table: {
        sortable: true,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "route" }, sm: { mode: "route" }, md: { mode: "route" }, lg: { mode: "route" }, xl: { mode: "route" } } },
      },
      display: {
        sizes: {
          xs: { mode: "route" },
          sm: { mode: "route" },
          md: { mode: "route" },
          lg: { mode: "route" },
          xl: { mode: "route" },
        },
      },
    },
    created_by: {
      key: "created_by",
      label: "Créé par",
      icon: "fa-solid fa-user",
      visibleIf: () => canCreateAny,
      table: {
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text", truncate: 10 }, sm: { mode: "text", truncate: 15 }, md: { mode: "text", truncate: 20 }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: {
        sizes: {
          xs: { mode: "text", truncate: 10 },
          sm: { mode: "text", truncate: 15 },
          md: { mode: "text", truncate: 20 },
          lg: { mode: "text" },
          xl: { mode: "text" },
        },
      },
    },
    created_at: {
      key: "created_at",
      label: "Créé le",
      icon: "fa-solid fa-calendar-plus",
      visibleIf: () => canCreateAny,
      table: {
        sortable: true,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: {
        sizes: {
          xs: { mode: "text" },
          sm: { mode: "text" },
          md: { mode: "text" },
          lg: { mode: "text" },
          xl: { mode: "text" },
        },
      },
    },
    updated_at: {
      key: "updated_at",
      label: "Modifié le",
      icon: "fa-solid fa-calendar-check",
      visibleIf: () => canCreateAny,
      table: {
        sortable: true,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: {
        sizes: {
          xs: { mode: "text" },
          sm: { mode: "text" },
          md: { mode: "text" },
          lg: { mode: "text" },
          xl: { mode: "text" },
        },
      },
    },

    // Configuration globale du tableau
    _tableConfig: {
      id: "spells.index",
      entityType: "spell",
      quickEdit: {
        enabled: true,
        permission: "updateAny",
      },
      actions: {
        enabled: true,
        permission: "view",
        available: ["view", "edit", "delete", "copy-link", "download-pdf", "refresh"],
        defaultVisible: {
          xs: false,
          sm: true,
          md: true,
          lg: true,
          xl: true,
        },
      },
      features: {
        search: {
          enabled: true,
          placeholder: "Rechercher un sort…",
          debounceMs: 200,
        },
        filters: { enabled: true },
        pagination: {
          enabled: true,
          perPage: { default: 25, options: [10, 25, 50, 100] },
        },
        selection: {
          enabled: true,
          checkboxMode: "auto",
          clickToSelect: true,
        },
        columnVisibility: {
          enabled: true,
          persist: true,
        },
        export: {
          csv: true,
          filename: "spells.csv",
        },
      },
      ui: {
        skeletonRows: 10,
      },
    },

    // Configuration globale du quickedit
    _quickeditConfig: {
      fields: [
        "level",
        "pa",
        "po_min",
        "po_max",
        "area",
        "state",
        "auto_update",
        "read_level",
        "write_level",
        "description",
        "image",
      ],
    },};
}
