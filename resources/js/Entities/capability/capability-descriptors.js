/**
 * Capability field descriptors — Version simplifiée
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
 * import { getCapabilityFieldDescriptors } from "@/Entities/capability/capability-descriptors";
 * const descriptors = getCapabilityFieldDescriptors({ meta });
 */

import { getEntityStateOptions, getUserRoleOptions } from "@/Utils/Entity/SharedConstants";
import { getElementOptions } from "@/Utils/Entity/Elements";

/**
 * @typedef {Object} CapabilityFieldDescriptor
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
 * Retourne les descripteurs de tous les champs de l'entité "Capability".
 * 
 * @param {Object} ctx - Contexte d'exécution
 * @param {Object} [ctx.capabilities] - Permissions disponibles (ou ctx.meta.capabilities)
 * @returns {Record<string, CapabilityFieldDescriptor>} Objet avec tous les descripteurs
 */
export function getCapabilityFieldDescriptors(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || null;
  const canUpdateAny = Boolean(can?.updateAny);
  const canCreateAny = Boolean(can?.createAny);

  return {
    id: {
      key: "id",
      label: "ID",
      icon: "fa-solid fa-hashtag",
      visibleIf: () => canCreateAny,
      table: {
        order: 5,
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
        order: 100,
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
          placeholder: "Ex: 1",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true },
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
          bulk: { enabled: true, nullable: true },
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
      edit: {
        form: {
          type: "text",
          group: "Métier",
          placeholder: "Ex: 0",
          required: false,
          showInCompact: false,
          bulk: { enabled: true, nullable: true },
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
          bulk: { enabled: true, nullable: false },
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
        filterable: { id: "element", type: "multi", defaultVisible: true },
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
          options: () => getElementOptions(),
          defaultValue: 0,
          bulk: { enabled: true, nullable: true },
        },
      },
    },
    description: {
      key: "description",
      label: "Description",
      icon: "fa-solid fa-align-left",
      table: {
        order: 15,
        sortable: true,
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
          bulk: { enabled: true, nullable: true },
        },
      },
    },
    effect: {
      key: "effect",
      label: "Effets (texte riche)",
      icon: "fa-solid fa-magic",
      table: {
        order: 35,
        sortable: true,
        searchable: true,
        filterable: { id: "effect", type: "text", defaultVisible: false },
        defaultVisible: { xs: false, sm: false, md: false, lg: true, xl: true },
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
          type: "richtext",
          group: "Contenu",
          required: false,
          showInCompact: false,
          help:
            "Contrairement aux sorts, les effets d’une capacité se décrivent ici en texte riche (pas d’éditeur d’effets / sous-effets).",
          bulk: { enabled: false },
        },
      },
    },
    time_before_use_again: {
      key: "time_before_use_again",
      label: "Temps avant réutilisation",
      icon: "fa-solid fa-clock",
      table: {
        order: 161,
        sortable: true,
        filterable: { id: "time_before_use_again", type: "text", defaultVisible: false },
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
          showInCompact: false,
          help: "Délai minimum avant de pouvoir réutiliser cette capacité (ex. « 1 tour », « 24h »).",
          bulk: { enabled: false },
        },
      },
    },
    casting_time: {
      key: "casting_time",
      label: "Temps d'incantation",
      icon: "fa-solid fa-hourglass",
      table: {
        order: 162,
        sortable: true,
        filterable: { id: "casting_time", type: "text", defaultVisible: false },
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
          showInCompact: false,
          help: "Temps nécessaire pour lancer la capacité (ex. « 1 » instantané, « 10m » rituel).",
          bulk: { enabled: false },
        },
      },
    },
    duration: {
      key: "duration",
      label: "Durée",
      icon: "fa-solid fa-stopwatch",
      table: {
        order: 163,
        sortable: true,
        filterable: { id: "duration", type: "text", defaultVisible: false },
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
          showInCompact: false,
          help: "Durée pendant laquelle la capacité reste active ou son effet persiste.",
          bulk: { enabled: false },
        },
      },
    },
    capability_summary_cast: {
      key: "capability_summary_cast",
      label: "Détails de lancer",
      icon: "fa-solid fa-layer-group",
      table: {
        order: 25,
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
    capability_summary_metier: {
      key: "capability_summary_metier",
      label: "PA / PO / Magie",
      icon: "fa-solid fa-bolt",
      table: {
        order: 20,
        type: "chips",
        searchable: true,
        sortable: false,
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
    capability_summary_relations: {
      key: "capability_summary_relations",
      label: "Invocation",
      icon: "fa-solid fa-link",
      table: {
        order: 28,
        type: "chips",
        searchable: true,
        sortable: false,
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
    is_magic: {
      key: "is_magic",
      label: "Physique / Wakfu",
      icon: "fa-solid fa-wand-magic",
      table: {
        order: 154,
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
          type: "checkbox",
          group: "Métier",
          required: false,
          showInCompact: false,
          defaultValue: true,
          bulk: { enabled: false },
        },
      },
    },
    ritual_available: {
      key: "ritual_available",
      label: "Rituel disponible",
      icon: "fa-solid fa-book",
      table: {
        order: 167,
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
          showInCompact: false,
          defaultValue: true,
          bulk: { enabled: false },
        },
      },
    },
    is_passive: {
      key: "is_passive",
      label: "Passif",
      icon: "fa-solid fa-moon",
      tooltip: "Capacité passive : typiquement sans coût PA/PO ni lancer, distincte des capacités activables.",
      table: {
        order: 168,
        sortable: true,
        filterable: { id: "is_passive", type: "boolean", defaultVisible: false },
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
          defaultValue: false,
          help: "À cocher pour les passifs de classe (affichage prioritaire sur les fiches Breed).",
          bulk: { enabled: true, nullable: false },
        },
      },
    },
    powerful: {
      key: "powerful",
      label: "Puissance",
      icon: "fa-solid fa-star",
      table: {
        order: 170,
        sortable: true,
        filterable: { id: "powerful", type: "text", defaultVisible: false },
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
      edit: {
        form: {
          type: "text",
          group: "Métier",
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
        order: 210,
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
          bulk: { enabled: true, nullable: false },
        },
      },
    },
    read_level: {
      key: "read_level",
      label: "Lecture (min.)",
      icon: "fa-solid fa-eye",
      table: {
        order: 220,
        sortable: true,
        filterable: { id: "read_level", type: "multi", defaultVisible: false },
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
          bulk: { enabled: true, nullable: true },
        },
      },
    },
    write_level: {
      key: "write_level",
      label: "Écriture (min.)",
      icon: "fa-solid fa-pen-to-square",
      table: {
        order: 221,
        sortable: true,
        filterable: { id: "write_level", type: "multi", defaultVisible: false },
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
          bulk: { enabled: true, nullable: true },
        },
      },
    },
    image: {
      key: "image",
      label: "Image",
      icon: "fa-solid fa-image",
      table: {
        order: 175,
        sortable: false,
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
          type: "file",
          group: "Médias",
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
      visibleIf: () => canCreateAny,
      table: {
        order: 800,
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
    created_at: {
      key: "created_at",
      label: "Créé le",
      icon: "fa-solid fa-calendar-plus",
      visibleIf: () => canCreateAny,
      table: {
        order: 810,
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
        order: 820,
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
      id: "capabilities.index",
      entityType: "capability",
      quickEdit: {
        enabled: true,
        permission: "updateAny",
      },
      actions: {
        enabled: true,
        permission: "view",
        available: ["view", "edit", "quick-edit", "delete", "copy-link", "download-pdf", "refresh"],
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
          placeholder: "Rechercher une capacité…",
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
          filename: "capabilities.csv",
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
        "po",
        "po_editable",
        "element",
        "is_passive",
        "state",
        "read_level",
        "write_level",
        "description",
        "effect",
      ],
    },};
}
