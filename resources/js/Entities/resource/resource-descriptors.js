/**
 * Resource field descriptors — Version refactorée selon les règles strictes
 *
 * @description
 * Schéma déclaratif pur qui permet au moteur de générer des outils génériques autour de Resource.
 * 
 * **Règles strictes respectées :**
 * - ✅ Aucune logique métier (pas de `build`, pas de calculs)
 * - ✅ Aucune description de vue (Large/Compact/Minimal/Text sont manuelles)
 * - ✅ Déterministe (même contexte = même résultat)
 * - ✅ Parle le langage du moteur (`sortable`, `filterable`, `editable`)
 *
 * ⚠️ Les vues (Large, Compact, Minimal, Text) sont des composants Vue manuels.
 * ⚠️ Sécurité : ces descriptors ne sont que de l'UX. Le backend reste la vérité (Policies + filtrage des champs).
 * ⚠️ Les transformations de données sont gérées par ResourceMapper, pas ici.
 *
 * @example
 * import { getResourceFieldDescriptors } from "@/Entities/resource/resource-descriptors";
 * const descriptors = getResourceFieldDescriptors({ meta });
 */

/**
 * @typedef {Object} ResourceFieldDescriptor
 * @property {string} key - Clé unique du champ
 * @property {string} label - Libellé affiché
 * @property {string} [icon] - Icône FontAwesome
 * @property {(ctx: any) => boolean} [visibleIf] - Fonction conditionnelle pour la visibilité
 * @property {(ctx: any) => boolean} [editableIf] - Fonction conditionnelle pour l'édition
 * @property {Object} [display] - Configuration de l'affichage dans les tableaux
 * @property {Record<"xs"|"sm"|"md"|"lg"|"xl", {mode?: string, truncate?: number}>} [display.sizes] - Configuration par taille d'écran
 * @property {Object} [display.cell] - Configuration du composant de cellule personnalisé
 * @property {string|Object} [display.cell.component] - Composant Vue à utiliser (chemin relatif ou import)
 * @property {Object|Function} [display.cell.props] - Props à passer au composant (objet statique ou fonction qui retourne un objet)
 * @property {boolean} [display.cell.passEntity] - Passer l'entité complète comme prop (défaut: false)
 * @property {boolean} [display.cell.passValue] - Passer la valeur brute comme prop (défaut: true)
 * @property {Object} [edit] - Configuration de l'édition
 * @property {Object} [edit.form] - Configuration du formulaire d'édition
 * @property {"text"|"textarea"|"select"|"checkbox"|"number"|"date"|"file"} [edit.form.type] - Type de champ
 * @property {string} [edit.form.label] - Libellé spécifique pour le formulaire
 * @property {string} [edit.form.group] - Groupe de champs
 * @property {string} [edit.form.help] - Texte d'aide
 * @property {boolean} [edit.form.required] - Champ obligatoire
 * @property {any} [edit.form.defaultValue] - Valeur par défaut
 * @property {Array<{value: any, label: string}>} [edit.form.options] - Options pour les selects (constante uniquement)
 * @property {Object} [edit.form.bulk] - Configuration pour l'édition en masse
 * @property {boolean} [edit.form.bulk.enabled] - Activer l'édition en masse
 * @property {boolean} [edit.form.bulk.nullable] - Permettre null/vide en bulk
 * ⚠️ Pas de `build` : les transformations sont gérées par ResourceMapper
 */

/**
 * Champs affichés dans le panneau d'édition rapide (sélection multiple).
 * ⚠️ IMPORTANT : Doit rester aligné avec le backend (bulk controller).
 */
export const RESOURCE_QUICK_EDIT_FIELDS = Object.freeze([
  "resource_type_id",
  "rarity",
  "level",
  "usable",
  "auto_update",
  "is_visible",
  "price",
  "weight",
  "dofus_version",
  "description",
  "image",
  "dofusdb_id",
]);

// Import des constantes des formatters (pour les options)
import { RarityFormatter } from '@/Utils/Formatters/RarityFormatter.js';
import { VisibilityFormatter } from '@/Utils/Formatters/VisibilityFormatter.js';

/**
 * Retourne les descripteurs de tous les champs de l'entité "Resource".
 * 
 * ⚠️ IMPORTANT : Cette fonction est pure et déterministe.
 * Elle ne contient aucune logique métier, uniquement de la déclaration.
 * 
 * @param {Object} ctx - Contexte d'exécution
 * @param {Object} [ctx.capabilities] - Permissions disponibles (ou ctx.meta.capabilities)
 * @param {Array} [ctx.resourceTypes] - Liste des types de ressources (ou ctx.meta.resourceTypes)
 * @returns {Record<string, ResourceFieldDescriptor>} Objet avec tous les descripteurs
 */
export function getResourceFieldDescriptors(ctx = {}) {
  // Extraire le contexte de manière pure (pas de calculs, pas de logique)
  const can = ctx?.capabilities || ctx?.meta?.capabilities || null;
  
  const resourceTypes = Array.isArray(ctx?.resourceTypes) 
    ? ctx.resourceTypes 
    : (Array.isArray(ctx?.meta?.resourceTypes) ? ctx.meta.resourceTypes : []);

  return {
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
          bulk: { enabled: true, nullable: true },
        },
      },
    },
    
    name: {
      key: "name",
      label: "Nom",
      icon: "fa-solid fa-font",
      display: {
        sizes: {
          xs: { mode: "route", truncate: 20 },
          sm: { mode: "route", truncate: 30 },
          md: { mode: "route", truncate: 44 },
          lg: { mode: "route", truncate: 60 },
          xl: { mode: "route" },
        },
      },
      edit: {
        form: { 
          type: "text",
          group: "Informations générales",
          required: true, 
          bulk: { enabled: false }, // Le nom ne peut pas être modifié en bulk
        },
      },
    },
    
    description: {
      key: "description",
      label: "Description",
      icon: "fa-solid fa-align-left",
      display: {
        sizes: {
          xs: { mode: "text", truncate: 30 },
          sm: { mode: "text", truncate: 50 },
          md: { mode: "text", truncate: 80 },
          lg: { mode: "text", truncate: 120 },
          xl: { mode: "text" },
        },
      },
      edit: {
        form: {
          type: "textarea",
          group: "Contenu",
          required: false,
          bulk: { enabled: true, nullable: true },
        },
      },
    },
    
    level: {
      key: "level",
      label: "Niveau",
      icon: "fa-solid fa-level-up-alt",
      display: {
        sizes: {
          xs: { mode: "badge" },
          sm: { mode: "badge" },
          md: { mode: "badge" },
          lg: { mode: "badge" },
          xl: { mode: "badge" },
        },
        // Exemple de composant personnalisé (commenté par défaut)
        // cell: {
        //   component: '@/Pages/Atoms/data-display/CustomLevelCell.vue',
        //   props: {
        //     format: 'number',
        //     showIcon: true,
        //   },
        //   passEntity: false, // Ne pas passer l'entité complète
        //   passValue: true, // Passer la valeur (défaut)
        // },
      },
      edit: {
        form: {
          type: "text",
          group: "Métier",
          placeholder: "Ex: 50",
          required: false,
          bulk: { enabled: true, nullable: true },
        },
      },
    },
    
    resource_type: {
      key: "resource_type",
      label: "Type",
      icon: "fa-solid fa-tag",
      display: {
        sizes: {
          xs: { mode: "badge" },
          sm: { mode: "badge" },
          md: { mode: "badge" },
          lg: { mode: "badge" },
          xl: { mode: "badge" },
        },
      },
      // Pas de section edit : champ en lecture seule (relation)
    },
    
    resource_type_id: {
      key: "resource_type_id",
      label: "Type de ressource",
      icon: "fa-solid fa-tag",
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
          type: "select",
          group: "Métier",
          help: "Définit le type (métier) de la ressource.",
          required: false,
          // Options dynamiques : seront construites à partir du contexte (resourceTypes)
          // Le descriptor ne contient pas de fonction, seulement la déclaration
          options: null, // Sera fourni par le contexte lors de l'utilisation
          bulk: { enabled: true, nullable: true },
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
          help: "La rareté est un entier (0..5). En bulk, laisser vide n'applique aucun changement.",
          required: false,
          // Utiliser la constante du formatter (pas de duplication)
          options: RarityFormatter.options.map(({ value, label }) => ({ value, label })),
          bulk: { enabled: true, nullable: true },
        },
      },
    },
    
    price: {
      key: "price",
      label: "Prix",
      icon: "fa-solid fa-coins",
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
          bulk: { enabled: true, nullable: true },
        },
      },
    },
    
    weight: {
      key: "weight",
      label: "Poids",
      icon: "fa-solid fa-weight-hanging",
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
          bulk: { enabled: true, nullable: true },
        },
      },
    },
    
    dofus_version: {
      key: "dofus_version",
      label: "Version Dofus",
      icon: "fa-solid fa-code-branch",
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
          required: false,
          bulk: { enabled: true, nullable: true },
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
          help: "Contrôle la visibilité côté front. Le backend reste la vérité sécurité.",
          required: false,
          // Utiliser la constante du formatter (pas de duplication)
          options: VisibilityFormatter.options.map(({ value, label }) => ({ value, label })),
          bulk: { enabled: true, nullable: false },
        },
      },
    },
    
    usable: {
      key: "usable",
      label: "Utilisable",
      icon: "fa-solid fa-check",
      display: {
        sizes: {
          xs: { mode: "boolIcon" },
          sm: { mode: "boolIcon" },
          md: { mode: "boolBadge" },
          lg: { mode: "boolBadge" },
          xl: { mode: "boolBadge" },
        },
      },
      edit: {
        form: {
          type: "checkbox",
          group: "Statut",
          required: false,
          defaultValue: false,
          bulk: { enabled: true, nullable: false },
        },
      },
    },
    
    auto_update: {
      key: "auto_update",
      label: "Auto-update",
      icon: "fa-solid fa-arrows-rotate",
      visibleIf: (ctx) => Boolean(ctx?.capabilities?.updateAny ?? ctx?.meta?.capabilities?.updateAny),
      display: {
        sizes: {
          xs: { mode: "boolIcon" },
          sm: { mode: "boolIcon" },
          md: { mode: "boolBadge" },
          lg: { mode: "boolBadge" },
          xl: { mode: "boolBadge" },
        },
      },
      edit: {
        form: {
          type: "checkbox",
          group: "Statut",
          required: false,
          defaultValue: false,
          bulk: { enabled: true, nullable: false },
        },
      },
    },
    
    dofusdb_id: {
      key: "dofusdb_id",
      label: "DofusDB",
      icon: "fa-solid fa-arrow-up-right-from-square",
      visibleIf: (ctx) => Boolean(ctx?.capabilities?.updateAny ?? ctx?.meta?.capabilities?.updateAny),
      display: {
        sizes: {
          xs: { mode: "routeExternal", truncate: 10 },
          sm: { mode: "routeExternal", truncate: 15 },
          md: { mode: "routeExternal", truncate: 20 },
          lg: { mode: "routeExternal" },
          xl: { mode: "routeExternal" },
        },
      },
      edit: {
        form: {
          type: "text",
          group: "Métadonnées",
          help: "ID externe DofusDB. Généralement géré automatiquement par le scrapping.",
          required: false,
          bulk: { enabled: true, nullable: true },
        },
      },
    },
    
    official_id: {
      key: "official_id",
      label: "ID Officiel",
      icon: "fa-solid fa-id-card",
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
          bulk: { enabled: true, nullable: true },
        },
      },
    },
    
    created_by: {
      key: "created_by",
      label: "Créé par",
      icon: "fa-solid fa-user",
      visibleIf: (ctx) => Boolean(ctx?.capabilities?.createAny ?? ctx?.meta?.capabilities?.createAny),
      display: {
        sizes: {
          xs: { mode: "text", truncate: 10 },
          sm: { mode: "text", truncate: 15 },
          md: { mode: "text", truncate: 20 },
          lg: { mode: "text" },
          xl: { mode: "text" },
        },
      },
      // Pas de section edit : champ système, non éditable
    },
    
    created_at: {
      key: "created_at",
      label: "Créé le",
      icon: "fa-solid fa-calendar",
      visibleIf: (ctx) => Boolean(ctx?.capabilities?.createAny ?? ctx?.meta?.capabilities?.createAny),
      display: {
        sizes: {
          xs: { mode: "dateShort" },
          sm: { mode: "dateShort" },
          md: { mode: "dateTime" },
          lg: { mode: "dateTime" },
          xl: { mode: "dateTime" },
        },
      },
      // Pas de section edit : champ système, non éditable
    },
    
    updated_at: {
      key: "updated_at",
      label: "Modifié le",
      icon: "fa-solid fa-clock",
      visibleIf: (ctx) => Boolean(ctx?.capabilities?.createAny ?? ctx?.meta?.capabilities?.createAny),
      display: {
        sizes: {
          xs: { mode: "dateShort" },
          sm: { mode: "dateShort" },
          md: { mode: "dateTime" },
          lg: { mode: "dateTime" },
          xl: { mode: "dateTime" },
        },
      },
      // Pas de section edit : champ système, non éditable
    },

    // Configuration globale du tableau
    _tableConfig: {
      id: "resources.index",
      entityType: "resource",
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
          placeholder: "Rechercher une ressource…",
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
          filename: "resources.csv",
        },
      },
      ui: {
        skeletonRows: 10,
      },
    },

    // Configuration globale du quickedit
    _quickeditConfig: {
      fields: RESOURCE_QUICK_EDIT_FIELDS,
    },

    // Support de la constante pour BulkConfig.fromDescriptors()
    _quickEditFields: RESOURCE_QUICK_EDIT_FIELDS,
  };
}

export default getResourceFieldDescriptors;
