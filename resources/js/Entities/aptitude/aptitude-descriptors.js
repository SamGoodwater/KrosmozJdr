/**
 * Aptitude field descriptors.
 *
 * @example
 * import { getAptitudeFieldDescriptors } from "@/Entities/aptitude/aptitude-descriptors";
 */

import { getEntityStateOptions, getUserRoleOptions } from "@/Utils/Entity/SharedConstants";

const badgeSizes = {
  xs: { mode: "badge" },
  sm: { mode: "badge" },
  md: { mode: "badge" },
  lg: { mode: "badge" },
  xl: { mode: "badge" },
};

const phraseTextSizes = {
  xs: { mode: "text", truncate: 18 },
  sm: { mode: "text", truncate: 24 },
  md: { mode: "text", truncate: 40 },
  lg: { mode: "text", truncate: 60 },
  xl: { mode: "text" },
};

/**
 * @param {Object} ctx
 * @returns {Record<string, Object>}
 */
export function getAptitudeFieldDescriptors(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || null;
  const canUpdateAny = Boolean(can?.updateAny);

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
    reuse: {
      key: "reuse",
      label: "Réutilisation",
      icon: "fa-solid fa-rotate",
      table: {
        sortable: true,
        searchable: true,
        filterable: { id: "reuse", type: "text", defaultVisible: true, placeholder: "Ex. repos court…" },
        defaultVisible: { xs: false, sm: true, md: true, lg: true, xl: true },
        cell: { sizes: phraseTextSizes },
      },
      display: { sizes: phraseTextSizes },
      edit: {
        form: {
          type: "textarea",
          required: false,
          showInCompact: true,
          help: "Phrase libre, ex. « Après un repos court » ou « 1 fois par scène tant que… ».",
          bulk: { enabled: true, nullable: true },
        },
      },
    },
    duration: {
      key: "duration",
      label: "Durée",
      icon: "fa-solid fa-hourglass-half",
      table: {
        sortable: true,
        searchable: true,
        filterable: { id: "duration", type: "text", defaultVisible: true, placeholder: "Ex. tant qu’il y a…" },
        defaultVisible: { xs: false, sm: true, md: true, lg: true, xl: true },
        cell: { sizes: phraseTextSizes },
      },
      display: { sizes: phraseTextSizes },
      edit: {
        form: {
          type: "textarea",
          required: false,
          showInCompact: true,
          help: "Phrase libre, ex. « Permanente », « 1 minute » ou « Tant qu’il y a une foule ».",
          bulk: { enabled: true, nullable: true },
        },
      },
    },
    range: {
      key: "range",
      label: "Portée",
      icon: "fa-solid fa-bullseye",
      table: {
        sortable: true,
        searchable: true,
        filterable: { id: "range", type: "text", defaultVisible: true, placeholder: "Ex. à portée d’ouïe…" },
        defaultVisible: { xs: false, sm: true, md: true, lg: true, xl: true },
        cell: { sizes: phraseTextSizes },
      },
      display: { sizes: phraseTextSizes },
      edit: {
        form: {
          type: "textarea",
          required: false,
          showInCompact: true,
          help: "Phrase libre, ex. « Soi », « 9 m » ou « Créatures qui t’entendent ».",
          bulk: { enabled: true, nullable: true },
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
