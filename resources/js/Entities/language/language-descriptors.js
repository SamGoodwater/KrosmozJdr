/**
 * Language field descriptors — référentiel Bibliothèques (lecture) / admin (écriture).
 *
 * @example
 * import { getLanguageFieldDescriptors } from "@/Entities/language/language-descriptors";
 * const descriptors = getLanguageFieldDescriptors({ meta });
 */

/**
 * @param {Object} ctx
 * @returns {Record<string, Object>}
 */
export function getLanguageFieldDescriptors(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || null;
  const canUpdateAny = Boolean(can?.updateAny);

  return {
    id: {
      key: "id",
      label: "ID",
      icon: "fa-solid fa-hashtag",
      visibleIf: () => canUpdateAny,
      table: {
        sortable: true,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: {
          sizes: {
            xs: { mode: "text" },
            sm: { mode: "text" },
            md: { mode: "text" },
            lg: { mode: "text" },
            xl: { mode: "text" },
          },
        },
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
      icon: "fa-solid fa-language",
      table: {
        sortable: true,
        searchable: true,
        defaultVisible: { xs: true, sm: true, md: true, lg: true, xl: true },
        cell: {
          sizes: {
            xs: { mode: "text", truncate: 20 },
            sm: { mode: "text", truncate: 30 },
            md: { mode: "text", truncate: 40 },
            lg: { mode: "text" },
            xl: { mode: "text" },
          },
        },
      },
      display: {
        sizes: {
          xs: { mode: "text", truncate: 20 },
          sm: { mode: "text", truncate: 30 },
          md: { mode: "text", truncate: 40 },
          lg: { mode: "text" },
          xl: { mode: "text" },
        },
      },
      edit: {
        form: { type: "text", required: true, showInCompact: true, bulk: { enabled: false } },
      },
    },
    description: {
      key: "description",
      label: "Description",
      icon: "fa-solid fa-align-left",
      table: {
        searchable: true,
        defaultVisible: { xs: false, sm: true, md: true, lg: true, xl: true },
        cell: {
          sizes: {
            xs: { mode: "text", truncate: 40 },
            sm: { mode: "text", truncate: 60 },
            md: { mode: "text", truncate: 80 },
            lg: { mode: "text", truncate: 120 },
            xl: { mode: "text" },
          },
        },
      },
      display: {
        sizes: {
          xs: { mode: "text", truncate: 40 },
          sm: { mode: "text", truncate: 60 },
          md: { mode: "text", truncate: 80 },
          lg: { mode: "text", truncate: 120 },
          xl: { mode: "text" },
        },
      },
      edit: {
        form: { type: "textarea", required: false, showInCompact: true, bulk: { enabled: false } },
      },
    },
    color: {
      key: "color",
      label: "Couleur",
      icon: "fa-solid fa-palette",
      table: {
        sortable: true,
        defaultVisible: { xs: true, sm: true, md: true, lg: true, xl: true },
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
          type: "text",
          required: true,
          showInCompact: true,
          help: "Couleur hex (#RRGGBB).",
          bulk: { enabled: false },
        },
      },
    },
  };
}
