/**
 * Item field descriptors — Version simplifiée
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
 * import { getItemFieldDescriptors } from "@/Entities/item/item-descriptors";
 * const descriptors = getItemFieldDescriptors({ meta });
 */

/**
 * @typedef {Object} ItemFieldDescriptor
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
 * Retourne les descripteurs de tous les champs de l'entité "Item".
 * 
 * @param {Object} ctx - Contexte d'exécution
 * @param {Object} [ctx.capabilities] - Permissions disponibles (ou ctx.meta.capabilities)
 * @param {Array} [ctx.itemTypes] - Liste des types d'items (ou ctx.meta.itemTypes)
 * @returns {Record<string, ItemFieldDescriptor>} Objet avec tous les descripteurs
 */
export function getItemFieldDescriptors(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || null;
  const canUpdateAny = Boolean(can?.updateAny);
  const canCreateAny = Boolean(can?.createAny);
  
  const itemTypes = Array.isArray(ctx?.itemTypes) 
    ? ctx.itemTypes 
    : (Array.isArray(ctx?.meta?.itemTypes) ? ctx.meta.itemTypes : []);

  return {
    id: {
      key: "id",
      label: "ID",
      icon: "fa-solid fa-hashtag",
      visibleIf: () => canUpdateAny,
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
          lg: { mode: "text", truncate: 80 },
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
          placeholder: "Ex: 50",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    rarity: {
      key: "rarity",
      label: "Rareté",
      icon: "fa-solid fa-star",
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
          help: "Rareté stockée en base comme entier (0..5).",
          required: false,
          showInCompact: true,
          options: [
            { value: 0, label: "Commun" },
            { value: 1, label: "Peu commun" },
            { value: 2, label: "Rare" },
            { value: 3, label: "Très rare" },
            { value: 4, label: "Légendaire" },
            { value: 5, label: "Unique" },
          ],
          defaultValue: 0,
          bulk: { enabled: true, nullable: false, build: (v) => Number(v) },
        },
      },
    },
    usable: {
      key: "usable",
      label: "Utilisable",
      icon: "fa-solid fa-check",
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
    auto_update: {
      key: "auto_update",
      label: "Auto-update",
      icon: "fa-solid fa-arrows-rotate",
      visibleIf: () => canUpdateAny,
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
      label: "Visibilité",
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
            { value: "game_master", label: "Maître de jeu" },
            { value: "admin", label: "Administrateur" },
          ],
          defaultValue: "guest",
          bulk: { enabled: true, nullable: false, build: (v) => v },
        },
      },
    },
    price: {
      key: "price",
      label: "Prix",
      icon: "fa-solid fa-coins",
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
          group: "Métadonnées",
          placeholder: "Ex: 12 000 kamas",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    dofus_version: {
      key: "dofus_version",
      label: "Version Dofus",
      icon: "fa-solid fa-code-branch",
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
          group: "Métadonnées",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    item_type: {
      key: "item_type",
      label: "Type",
      icon: "fa-solid fa-tags",
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
    image: {
      key: "image",
      label: "Image",
      icon: "fa-solid fa-image",
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
    dofusdb_id: {
      key: "dofusdb_id",
      label: "DofusDB",
      icon: "fa-solid fa-up-right-from-square",
      visibleIf: () => canUpdateAny,
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
      visibleIf: () => canUpdateAny,
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
      visibleIf: () => canUpdateAny,
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
      visibleIf: () => canUpdateAny,
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
      id: "items.index",
      entityType: "item",
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
          placeholder: "Rechercher un item…",
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
          filename: "items.csv",
        },
      },
      ui: {
        skeletonRows: 10,
      },
    },

    // Configuration globale du quickedit
    _quickeditConfig: {
      fields: [
        "item_type_id",
        "rarity",
        "level",
        "usable",
        "auto_update",
        "is_visible",
        "price",
        "dofus_version",
        "description",
        "image",
        "dofusdb_id",
      ],
    },};
}
