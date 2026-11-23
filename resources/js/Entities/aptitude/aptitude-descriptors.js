/**
 * Aptitude field descriptors.
 *
 * @example
 * import { getAptitudeFieldDescriptors } from "@/Entities/aptitude/aptitude-descriptors";
 */

import { getEntityStateOptions, getUserRoleOptions } from "@/Utils/Entity/SharedConstants";

const REUSE_OPTIONS = [
  { value: "unlimited", label: "Illimité" },
  { value: "short_rest", label: "Repos court" },
  { value: "long_rest", label: "Repos long" },
  { value: "timed", label: "Temporisé" },
];

const DURATION_OPTIONS = [
  { value: "instant", label: "Instantané" },
  { value: "timed", label: "Temporisé" },
  { value: "permanent", label: "Permanent" },
  { value: "while_condition", label: "Tant que…" },
];

const RANGE_OPTIONS = [
  { value: "self", label: "Soi" },
  { value: "distance", label: "Distance" },
  { value: "sense", label: "Sens" },
];

const TIME_UNIT_OPTIONS = [
  { value: "minute", label: "Minute(s)" },
  { value: "hour", label: "Heure(s)" },
  { value: "day", label: "Jour(s)" },
];

const DISTANCE_UNIT_OPTIONS = [
  { value: "m", label: "m" },
  { value: "km", label: "km" },
];

const SENSE_OPTIONS = [
  { value: "hearing", label: "Ouïe" },
  { value: "sight", label: "Vue" },
  { value: "touch", label: "Toucher" },
  { value: "custom", label: "Personnalisé" },
];

const badgeSizes = {
  xs: { mode: "badge" },
  sm: { mode: "badge" },
  md: { mode: "badge" },
  lg: { mode: "badge" },
  xl: { mode: "badge" },
};

/**
 * @param {Object} ctx
 * @returns {Record<string, Object>}
 */
export function getAptitudeFieldDescriptors(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || null;
  const canUpdateAny = Boolean(can?.updateAny);

  const entityValue = (context, key) =>
    context?.entity?.[key] ??
    context?.entity?._data?.[key] ??
    context?.form?.[key] ??
    context?.model?.[key] ??
    null;

  return {
    id: {
      key: "id",
      label: "ID",
      icon: "fa-solid fa-hashtag",
      visibleIf: (context = ctx) =>
        Boolean(context?.capabilities?.updateAny ?? context?.meta?.capabilities?.updateAny ?? false),
      table: {
        sortable: true,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
    },
    name: {
      key: "name",
      label: "Nom",
      icon: "fa-solid fa-font",
      table: {
        sortable: true,
        searchable: true,
        defaultVisible: { xs: true, sm: true, md: true, lg: true, xl: true },
        cell: {
          sizes: {
            xs: { mode: "route", truncate: 15 },
            sm: { mode: "route", truncate: 20 },
            md: { mode: "route", truncate: 30 },
            lg: { mode: "route", truncate: 40 },
            xl: { mode: "route" },
          },
        },
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
      edit: { form: { type: "text", required: true, showInCompact: true, bulk: { enabled: false } } },
    },
    description: {
      key: "description",
      label: "Description",
      icon: "fa-solid fa-align-left",
      table: {
        searchable: true,
        defaultVisible: { xs: false, sm: false, md: true, lg: true, xl: true },
        cell: {
          sizes: {
            xs: { mode: "text", truncate: 20 },
            sm: { mode: "text", truncate: 30 },
            md: { mode: "text", truncate: 50 },
            lg: { mode: "text" },
            xl: { mode: "text" },
          },
        },
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
      edit: { form: { type: "textarea", required: false, showInCompact: false, bulk: { enabled: true, nullable: true } } },
    },
    effect: {
      key: "effect",
      label: "Effet",
      icon: "fa-solid fa-bolt",
      table: {
        searchable: true,
        defaultVisible: { xs: false, sm: false, md: false, lg: true, xl: true },
        cell: {
          sizes: {
            xs: { mode: "text", truncate: 20 },
            sm: { mode: "text", truncate: 30 },
            md: { mode: "text", truncate: 50 },
            lg: { mode: "text" },
            xl: { mode: "text" },
          },
        },
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
      edit: { form: { type: "textarea", required: false, showInCompact: true, bulk: { enabled: true, nullable: true } } },
    },
    reuse_type: {
      key: "reuse_type",
      label: "Réutilisation",
      icon: "fa-solid fa-rotate",
      table: {
        sortable: true,
        filterable: { id: "reuse_type", type: "multi", defaultVisible: true, options: REUSE_OPTIONS },
        defaultVisible: { xs: false, sm: true, md: true, lg: true, xl: true },
        cell: { sizes: badgeSizes },
      },
      display: { sizes: badgeSizes },
      edit: {
        form: {
          type: "select",
          required: true,
          showInCompact: true,
          options: REUSE_OPTIONS,
          defaultValue: "unlimited",
          bulk: { enabled: true, nullable: false },
        },
      },
    },
    reuse_amount: {
      key: "reuse_amount",
      label: "Délai (quantité)",
      icon: "fa-solid fa-hashtag",
      visibleIf: (context = ctx) => entityValue(context, "reuse_type") === "timed",
      edit: {
        form: {
          type: "number",
          required: false,
          showInCompact: true,
          validation: { min: 1 },
          bulk: { enabled: false },
        },
      },
    },
    reuse_unit: {
      key: "reuse_unit",
      label: "Délai (unité)",
      icon: "fa-solid fa-clock",
      visibleIf: (context = ctx) => entityValue(context, "reuse_type") === "timed",
      edit: {
        form: {
          type: "select",
          required: false,
          showInCompact: true,
          options: TIME_UNIT_OPTIONS,
          bulk: { enabled: false },
        },
      },
    },
    duration_type: {
      key: "duration_type",
      label: "Durée",
      icon: "fa-solid fa-hourglass-half",
      table: {
        sortable: true,
        filterable: { id: "duration_type", type: "multi", defaultVisible: true, options: DURATION_OPTIONS },
        defaultVisible: { xs: false, sm: true, md: true, lg: true, xl: true },
        cell: { sizes: badgeSizes },
      },
      display: { sizes: badgeSizes },
      edit: {
        form: {
          type: "select",
          required: true,
          showInCompact: true,
          options: DURATION_OPTIONS,
          defaultValue: "permanent",
          bulk: { enabled: true, nullable: false },
        },
      },
    },
    duration_amount: {
      key: "duration_amount",
      label: "Durée (quantité)",
      icon: "fa-solid fa-hashtag",
      visibleIf: (context = ctx) => entityValue(context, "duration_type") === "timed",
      edit: {
        form: {
          type: "number",
          required: false,
          showInCompact: true,
          validation: { min: 1 },
          bulk: { enabled: false },
        },
      },
    },
    duration_unit: {
      key: "duration_unit",
      label: "Durée (unité)",
      icon: "fa-solid fa-clock",
      visibleIf: (context = ctx) => entityValue(context, "duration_type") === "timed",
      edit: {
        form: {
          type: "select",
          required: false,
          showInCompact: true,
          options: TIME_UNIT_OPTIONS,
          bulk: { enabled: false },
        },
      },
    },
    duration_note: {
      key: "duration_note",
      label: "Condition de durée",
      icon: "fa-solid fa-comment",
      visibleIf: (context = ctx) => entityValue(context, "duration_type") === "while_condition",
      edit: {
        form: {
          type: "text",
          required: false,
          showInCompact: true,
          help: "Ex. « tant qu’il y a une foule »",
          bulk: { enabled: false },
        },
      },
    },
    range_type: {
      key: "range_type",
      label: "Portée",
      icon: "fa-solid fa-bullseye",
      table: {
        sortable: true,
        filterable: { id: "range_type", type: "multi", defaultVisible: true, options: RANGE_OPTIONS },
        defaultVisible: { xs: false, sm: true, md: true, lg: true, xl: true },
        cell: { sizes: badgeSizes },
      },
      display: { sizes: badgeSizes },
      edit: {
        form: {
          type: "select",
          required: true,
          showInCompact: true,
          options: RANGE_OPTIONS,
          defaultValue: "self",
          bulk: { enabled: true, nullable: false },
        },
      },
    },
    range_amount: {
      key: "range_amount",
      label: "Distance",
      icon: "fa-solid fa-ruler",
      visibleIf: (context = ctx) => entityValue(context, "range_type") === "distance",
      edit: {
        form: {
          type: "number",
          required: false,
          showInCompact: true,
          validation: { min: 1 },
          bulk: { enabled: false },
        },
      },
    },
    range_unit: {
      key: "range_unit",
      label: "Unité de distance",
      icon: "fa-solid fa-ruler-horizontal",
      visibleIf: (context = ctx) => entityValue(context, "range_type") === "distance",
      edit: {
        form: {
          type: "select",
          required: false,
          showInCompact: true,
          options: DISTANCE_UNIT_OPTIONS,
          defaultValue: "m",
          bulk: { enabled: false },
        },
      },
    },
    range_sense: {
      key: "range_sense",
      label: "Sens",
      icon: "fa-solid fa-ear-listen",
      visibleIf: (context = ctx) => entityValue(context, "range_type") === "sense",
      edit: {
        form: {
          type: "select",
          required: false,
          showInCompact: true,
          options: SENSE_OPTIONS,
          bulk: { enabled: false },
        },
      },
    },
    range_note: {
      key: "range_note",
      label: "Note de portée",
      icon: "fa-solid fa-comment",
      visibleIf: (context = ctx) => entityValue(context, "range_type") === "sense",
      edit: {
        form: {
          type: "text",
          required: false,
          showInCompact: true,
          help: "Ex. « tant qu’on t’entend »",
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
          defaultValue: ["playable"],
          options: getEntityStateOptions(),
        },
        defaultVisible: { xs: false, sm: true, md: true, lg: true, xl: true },
        cell: { sizes: badgeSizes },
      },
      display: { sizes: badgeSizes },
      edit: {
        form: {
          type: "select",
          group: "Statut",
          required: false,
          showInCompact: true,
          options: getEntityStateOptions,
          defaultValue: "draft",
          bulk: { enabled: true, nullable: false },
        },
      },
    },
    read_level: {
      key: "read_level",
      label: "Lecture (min.)",
      icon: "fa-solid fa-eye",
      table: {
        sortable: true,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: badgeSizes },
      },
      display: { sizes: badgeSizes },
      edit: {
        form: {
          type: "select",
          group: "Statut",
          required: false,
          showInCompact: true,
          options: getUserRoleOptions,
          defaultValue: 0,
          bulk: { enabled: true, nullable: true },
        },
      },
    },
    write_level: {
      key: "write_level",
      label: "Écriture (min.)",
      icon: "fa-solid fa-pen-to-square",
      table: {
        sortable: true,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: badgeSizes },
      },
      display: { sizes: badgeSizes },
      edit: {
        form: {
          type: "select",
          group: "Statut",
          required: false,
          showInCompact: true,
          options: getUserRoleOptions,
          defaultValue: 4,
          bulk: { enabled: true, nullable: true },
        },
      },
    },
    image: {
      key: "image",
      label: "Image",
      icon: "fa-solid fa-image",
      table: {
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "image" }, sm: { mode: "image" }, md: { mode: "image" }, lg: { mode: "image" }, xl: { mode: "image" } } },
      },
      display: { sizes: { xs: { mode: "image" }, sm: { mode: "image" }, md: { mode: "image" }, lg: { mode: "image" }, xl: { mode: "image" } } },
      edit: { form: { type: "file", group: "Médias", required: false, showInCompact: false, bulk: { enabled: false } } },
    },
    created_by: {
      key: "created_by",
      label: "Créé par",
      icon: "fa-solid fa-user",
      visibleIf: () => canUpdateAny,
      table: {
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
    },
    created_at: {
      key: "created_at",
      label: "Créé le",
      icon: "fa-solid fa-calendar-plus",
      visibleIf: () => canUpdateAny,
      table: {
        sortable: true,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
    },
    updated_at: {
      key: "updated_at",
      label: "Modifié le",
      icon: "fa-solid fa-calendar-check",
      visibleIf: () => canUpdateAny,
      table: {
        sortable: true,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
    },
    _tableConfig: {
      id: "aptitudes.index",
      entityType: "aptitudes",
      actions: {
        enabled: true,
        permission: "view",
        available: ["view", "edit", "delete", "copy-link", "download-pdf", "refresh"],
        defaultVisible: { xs: false, sm: true, md: true, lg: true, xl: true },
      },
      features: {
        search: { enabled: true, placeholder: "Rechercher une aptitude…", debounceMs: 200 },
        filters: { enabled: true },
        pagination: { enabled: true, perPage: { default: 25, options: [10, 25, 50, 100] } },
        selection: { enabled: true, checkboxMode: "auto", clickToSelect: true },
        columnVisibility: { enabled: true, persist: true },
        export: { csv: true, filename: "aptitudes.csv" },
      },
      ui: { density: "comfortable", stickyHeader: true },
    },
  };
}
