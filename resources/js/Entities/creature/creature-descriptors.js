/**
 * Creature field descriptors — Version simplifiée
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
 * import { getCreatureFieldDescriptors } from "@/Entities/creature/creature-descriptors";
 * const descriptors = getCreatureFieldDescriptors({ meta });
 */

/**
 * @typedef {Object} CreatureFieldDescriptor
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
 * Retourne les descripteurs de tous les champs de l'entité "Creature".
 * 
 * @param {Object} ctx - Contexte d'exécution
 * @param {Object} [ctx.capabilities] - Permissions disponibles (ou ctx.meta.capabilities)
 * @returns {Record<string, CreatureFieldDescriptor>} Objet avec tous les descripteurs
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
      visibleIf: () => canCreateAny,
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
          required: false,
          showInCompact: false,
          bulk: { enabled: false },
        },
      },
    },
    level: {
      key: "level",
      label: "Niveau",
      icon: "fa-solid fa-level-up-alt",
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
          group: "Caractéristiques",
          required: false,
          showInCompact: true,
          options: [
            { value: 0, label: "Amical" },
            { value: 1, label: "Curieux" },
            { value: 2, label: "Neutre" },
            { value: 3, label: "Hostile" },
            { value: 4, label: "Aggressif" },
          ],
          defaultValue: 2,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : Number(v)) },
        },
      },
    },
    location: {
      key: "location",
      label: "Localisation",
      icon: "fa-solid fa-map-marker-alt",
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
          group: "Caractéristiques",
          required: false,
          showInCompact: false,
          bulk: { enabled: false },
        },
      },
    },
    life: {
      key: "life",
      label: "Vie",
      icon: "fa-solid fa-heart",
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
      icon: "fa-solid fa-running",
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
      icon: "fa-solid fa-walking",
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
          group: "Caractéristiques",
          placeholder: "Ex: 3",
          required: false,
          showInCompact: false,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    po: {
      key: "po",
      label: "PO",
      icon: "fa-solid fa-crosshairs",
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
          group: "Caractéristiques",
          placeholder: "Ex: 0",
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
    is_visible: {
      key: "is_visible",
      label: "Visible",
      icon: "fa-solid fa-eye",
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
      visibleIf: () => canCreateAny,
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
      id: "creatures.index",
      entityType: "creature",
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
          placeholder: "Rechercher une créature…",
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
          filename: "creatures.csv",
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
        "hostility",
        "life",
        "usable",
        "is_visible",
      ],
    },};
}
