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
 * @property {string} key - Clé unique du champ (obligatoire)
 * 
 * @property {Object} general - Métadonnées générales (obligatoire)
 * @property {string} general.label - Libellé affiché partout
 * @property {string} [general.icon] - Icône FontAwesome
 * @property {string} [general.tooltip] - Tooltip général (utilisé par défaut si non spécifié ailleurs)
 * 
 * @property {Object} [permissions] - Permissions pour voir et éditer
 * @property {(ctx: any) => boolean} [permissions.visibleIf] - Fonction conditionnelle pour la visibilité en mode read
 * @property {(ctx: any) => boolean} [permissions.editableIf] - Fonction conditionnelle pour l'édition (utilisé par défaut pour edition.form.editableIf)
 * 
 * @property {Object} [table] - Configuration spécifique pour les tableaux
 * @property {Object} [table.header] - Configuration de l'en-tête de colonne
 * @property {string} [table.header.label] - Label spécifique pour l'en-tête (optionnel, utilise general.label par défaut)
 * @property {string} [table.header.icon] - Icône spécifique pour l'en-tête (optionnel, utilise general.icon par défaut)
 * @property {string} [table.header.tooltip] - Tooltip pour l'en-tête (optionnel)
 * @property {Object} [table.cell] - Configuration de la cellule
 * @property {Record<"xs"|"sm"|"md"|"lg"|"xl", {mode?: string, truncate?: number}>} [table.cell.sizes] - Configuration par taille d'écran
 * @property {string|Object} [table.cell.component] - Composant Vue personnalisé (chemin relatif ou import)
 * @property {Object|Function} [table.cell.props] - Props à passer au composant (objet statique ou fonction)
 * @property {boolean} [table.cell.passEntity] - Passer l'entité complète comme prop (défaut: false)
 * @property {boolean} [table.cell.passValue] - Passer la valeur brute comme prop (défaut: true)
 * @property {Record<"xs"|"sm"|"md"|"lg"|"xl", boolean>} [table.defaultVisible] - Visibilité par défaut de la colonne (défaut: true pour tous)
 * @property {(ctx: any) => boolean} [table.visibleIf] - Fonction conditionnelle pour la visibilité de la colonne selon les permissions/rôles
 * 
 * @property {Object} [display] - Configuration pour les vues d'affichage (modal/page)
 * @property {string} [display.tooltip] - Tooltip pour les vues (optionnel, utilise general.tooltip par défaut)
 * @property {Object} [display.style] - Styles CSS selon le variant de vue
 * @property {string} [display.style.compact] - Classes CSS pour la vue compacte
 * @property {string} [display.style.large] - Classes CSS pour la vue large
 * @property {string} [display.style.minimal] - Classes CSS pour la vue minimal
 * @property {string} [display.style.text] - Classes CSS pour la vue text
 * @property {Object} [display.color] - Couleurs selon le variant de vue
 * @property {string} [display.color.compact] - Couleur pour la vue compacte (ex: "primary", "secondary", "accent")
 * @property {string} [display.color.large] - Couleur pour la vue large
 * @property {string} [display.color.minimal] - Couleur pour la vue minimal
 * @property {string} [display.color.text] - Couleur pour la vue text
 * @property {Function|string} [display.format] - Fonction de formatage de la valeur ou clé de formatter (ex: "rarity", "visibility")
 * 
 * @property {Object} [edition] - Configuration pour l'édition
 * @property {Object} [edition.form] - Configuration du formulaire d'édition (commune à Large, Compact, QuickEdit)
 * @property {"text"|"textarea"|"select"|"checkbox"|"number"|"date"|"file"} [edition.form.type] - Type de champ
 * @property {string} [edition.form.label] - Libellé spécifique pour le formulaire (optionnel, utilise general.label par défaut)
 * @property {string} [edition.form.group] - Groupe de champs (pour organisation dans les formulaires)
 * @property {string} [edition.form.help] - Texte d'aide affiché sous le champ
 * @property {string} [edition.form.placeholder] - Placeholder pour les inputs
 * @property {boolean} [edition.form.required] - Champ obligatoire
 * @property {any} [edition.form.defaultValue] - Valeur par défaut
 * @property {Array<{value: any, label: string}>|Function} [edition.form.options] - Options pour les selects (constante ou fonction(ctx))
 * @property {(ctx: any) => boolean} [edition.form.editableIf] - Permission spécifique pour l'édition (optionnel, utilise permissions.editableIf par défaut)
 * @property {Object} [edition.form.validation] - Règles de validation
 * @property {string|RegExp|Function} [edition.form.validation.pattern] - Pattern de validation (regex ou fonction)
 * @property {number} [edition.form.validation.min] - Valeur minimale (pour number, date)
 * @property {number} [edition.form.validation.max] - Valeur maximale (pour number, date)
 * @property {number} [edition.form.validation.minLength] - Longueur minimale (pour text, textarea)
 * @property {number} [edition.form.validation.maxLength] - Longueur maximale (pour text, textarea)
 * @property {Function} [edition.form.validation.validator] - Fonction de validation personnalisée (value, ctx) => boolean|string
 * @property {string} [edition.form.validation.message] - Message d'erreur personnalisé
 * @property {number} [edition.form.rows] - Nombre de lignes pour textarea
 * @property {number} [edition.form.cols] - Nombre de colonnes pour textarea
 * @property {string} [edition.form.accept] - Types de fichiers acceptés pour file (ex: "image/*", ".pdf")
 * @property {boolean} [edition.form.multiple] - Permettre la sélection multiple (pour select, file)
 * @property {number} [edition.form.step] - Pas pour number (ex: 0.1, 1, 10)
 * @property {Object} [edition.bulk] - Configuration pour l'édition en masse
 * @property {boolean} [edition.bulk.enabled] - Activer l'édition en masse
 * @property {boolean} [edition.bulk.nullable] - Permettre null/vide en bulk
 * ⚠️ Pas de `build` : les transformations sont gérées par ResourceMapper
 * ⚠️ Pas de `showInCompact` : c'est la vue qui décide quels champs afficher
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
import { getRarityOptions } from '@/Utils/Entity/SharedConstants.js';

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
    id: {
      key: "id",
      general: {
        label: "ID",
        icon: "fa-solid fa-hashtag",
        tooltip: "Identifiant unique de la ressource",
      },
      permissions: {
        // Seuls les admins peuvent voir l'ID
        visibleIf: (ctx) => {
          const can = ctx?.capabilities?.createAny || ctx?.meta?.capabilities?.createAny || false;
          return can;
        },
      },
      table: {
        // Colonne cachée par défaut (seulement visible pour les admins)
        defaultVisible: {
          xs: false,
          sm: false,
          md: false,
          lg: false,
          xl: false,
        },
        // Vérification supplémentaire de visibilité selon les permissions
        visibleIf: (ctx) => {
          const can = ctx?.capabilities?.createAny || ctx?.meta?.capabilities?.createAny || false;
          return can;
        },
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
      // Pas de section edition : champ système, non éditable
    },
    
    image: {
      key: "image",
      general: {
        label: "Image",
        icon: "fa-solid fa-image",
        tooltip: "Image de la ressource",
      },
      table: {
        cell: {
          sizes: {
            xs: { mode: "thumb" },
            sm: { mode: "thumb" },
            md: { mode: "thumb" },
            lg: { mode: "thumb" },
            xl: { mode: "thumb" },
          },
        },
      },
      display: {
        tooltip: "Image de la ressource",
      },
      edition: {
        form: {
          type: "text",
          label: "Image (URL)",
          group: "Image",
          required: false,
        },
        bulk: {
          enabled: true,
          nullable: true,
        },
      },
    },
    
    name: {
      key: "name",
      general: {
        label: "Nom",
        icon: "fa-solid fa-font",
        tooltip: "Nom de la ressource",
      },
      table: {
        cell: {
          sizes: {
            xs: { mode: "route", truncate: 20 },
            sm: { mode: "route", truncate: 30 },
            md: { mode: "route", truncate: 44 },
            lg: { mode: "route", truncate: 60 },
            xl: { mode: "route" },
          },
        },
      },
      display: {
        tooltip: "Nom de la ressource",
      },
      edition: {
        form: { 
          type: "text",
          group: "Informations générales",
          required: true,
        },
        bulk: {
          enabled: false, // Le nom ne peut pas être modifié en bulk
        },
      },
    },
    
    description: {
      key: "description",
      general: {
        label: "Description",
        icon: "fa-solid fa-align-left",
        tooltip: "Description de la ressource",
      },
      table: {
        cell: {
          sizes: {
            xs: { mode: "text", truncate: 30 },
            sm: { mode: "text", truncate: 50 },
            md: { mode: "text", truncate: 80 },
            lg: { mode: "text", truncate: 120 },
            xl: { mode: "text" },
          },
        },
      },
      display: {
        tooltip: "Description de la ressource",
      },
      edition: {
        form: {
          type: "textarea",
          group: "Contenu",
          placeholder: "Description détaillée de la ressource...",
          help: "Description complète de la ressource",
          required: false,
          rows: 4,
          validation: {
            maxLength: 20,
            message: "La description ne peut pas dépasser 2000 caractères",
          },
        },
        bulk: {
          enabled: true,
          nullable: true,
        },
      },
    },
    
    level: {
      key: "level",
      general: {
        label: "Niveau", // Utilise FIELD_LABELS.level depuis SharedConstants
        icon: "fa-solid fa-level-up-alt", // Utilise FIELD_ICONS.level depuis SharedConstants
        tooltip: "Niveau de la ressource",
      },
      table: {
        cell: {
          sizes: {
            xs: { mode: "badge" },
            sm: { mode: "badge" },
            md: { mode: "badge" },
            lg: { mode: "badge" },
            xl: { mode: "badge" },
          },
          // Exemple de composant personnalisé (commenté par défaut)
          // component: '@/Pages/Atoms/data-display/CustomLevelCell.vue',
          // props: {
          //   format: 'number',
          //   showIcon: true,
          // },
          // passEntity: false, // Ne pas passer l'entité complète
          // passValue: true, // Passer la valeur (défaut)
        },
      },
      display: {
        tooltip: "Niveau de la ressource",
      },
      edition: {
        form: {
          type: "number",
          group: "Métier",
          placeholder: "Ex: 5",
          help: "Niveau de la ressource (entier positif max 20)",
          required: false,
          validation: {
            min: 0,
            max: 200,
            message: "Le niveau doit être entre 0 et 200",
          },
          step: 1,
        },
        bulk: {
          enabled: true,
          nullable: true,
        },
      },
    },
    
    resource_type: {
      key: "resource_type",
      general: {
        label: "Type",
        icon: "fa-solid fa-tag",
        tooltip: "Type (métier) de la ressource",
      },
      table: {
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
        tooltip: "Type (métier) de la ressource",
      },
      // Pas de section edition : champ en lecture seule (relation)
    },
    
    resource_type_id: {
      key: "resource_type_id",
      general: {
        label: "Type de ressource",
        icon: "fa-solid fa-tag",
        tooltip: "Type (métier) de la ressource",
      },
      table: {
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
        tooltip: "Type (métier) de la ressource",
      },
      edition: {
        form: {
          type: "select",
          searchable: true, // Activer le select avec recherche pour les listes longues
          group: "Métier",
          help: "Définit le type (métier) de la ressource.",
          placeholder: "Sélectionner un type",
          required: false,
          // Options dynamiques : fonction qui reçoit le contexte
          options: (ctx) => {
            const resourceTypes = ctx?.resourceTypes || ctx?.meta?.resourceTypes || [];
            return [
              { value: '', label: '—' },
              ...resourceTypes.map(t => ({ value: t.id, label: t.name }))
            ];
          },
        },
        bulk: {
          enabled: true,
          nullable: true,
        },
      },
    },
    
    rarity: {
      key: "rarity",
      general: {
        label: "Rareté",
        icon: "fa-solid fa-star",
        tooltip: "Niveau de rareté de la ressource (0-5)",
      },
      table: {
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
        tooltip: "Niveau de rareté de la ressource (0-5)",
      },
      edition: {
        form: {
          type: "select",
          group: "Métier",
          help: "La rareté est un entier (0..5). En bulk, laisser vide n'applique aucun changement.",
          required: false,
          // Utiliser la constante depuis SharedConstants (pas de duplication)
          options: getRarityOptions().map(({ value, label }) => ({ value, label })),
        },
        bulk: {
          enabled: true,
          nullable: true,
        },
      },
    },
    
    price: {
      key: "price",
      general: {
        label: "Prix",
        icon: "fa-solid fa-coins",
        tooltip: "Prix de la ressource",
      },
      table: {
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
        tooltip: "Prix de la ressource",
      },
      edition: {
        form: {
          type: "number",
          group: "Métadonnées",
          placeholder: "Ex: 100",
          help: "Prix de la ressource en kamas",
          required: false,
          validation: {
            min: 0,
            message: "Le prix doit être positif",
          },
          step: 1,
        },
        bulk: {
          enabled: true,
          nullable: true,
        },
      },
    },
    
    weight: {
      key: "weight",
      general: {
        label: "Poids",
        icon: "fa-solid fa-weight-hanging",
        tooltip: "Poids de la ressource",
      },
      table: {
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
        tooltip: "Poids de la ressource",
      },
      edition: {
        form: {
          type: "number",
          group: "Métadonnées",
          placeholder: "Ex: 1.5",
          help: "Poids de la ressource",
          required: false,
          validation: {
            min: 0,
            message: "Le poids doit être positif",
          },
          step: 0.1,
        },
        bulk: {
          enabled: true,
          nullable: true,
        },
      },
    },
    
    dofus_version: {
      key: "dofus_version",
      general: {
        label: "Version Dofus",
        icon: "fa-solid fa-code-branch",
        tooltip: "Version du jeu Dofus pour laquelle la ressource est disponible",
      },
      permissions: {
        // Seuls les admins peuvent voir la version Dofus
        visibleIf: (ctx) => {
          const can = ctx?.capabilities?.updateAny || ctx?.meta?.capabilities?.updateAny || false;
          return can;
        },
      },
      table: {
        // Colonne cachée par défaut (seulement visible pour les admins)
        defaultVisible: {
          xs: false,
          sm: false,
          md: false,
          lg: false,
          xl: false,
        },
        // Vérification supplémentaire de visibilité selon les permissions
        visibleIf: (ctx) => {
          const can = ctx?.capabilities?.updateAny || ctx?.meta?.capabilities?.updateAny || false;
          return can;
        },
        cell: {
          sizes: {
            xs: { mode: "text", truncate: 10 }, // Pas de badge, texte simple
            sm: { mode: "text", truncate: 15 },
            md: { mode: "text", truncate: 20 },
            lg: { mode: "text" },
            xl: { mode: "text" },
          },
        },
      },
      display: {
        tooltip: "Version du jeu Dofus pour laquelle la ressource est disponible",
      },
      edition: {
        form: {
          type: "text",
          group: "Métadonnées",
          required: false,
        },
        bulk: {
          enabled: true,
          nullable: true,
        },
      },
    },
    
    is_visible: {
      key: "is_visible",
      general: {
        label: "Visibilité",
        icon: "fa-solid fa-eye",
        tooltip: "Contrôle la visibilité de la ressource côté frontend",
      },
      permissions: {
        // Seuls les admins peuvent voir la visibilité
        visibleIf: (ctx) => {
          const can = ctx?.capabilities?.updateAny || ctx?.meta?.capabilities?.updateAny || false;
          return can;
        },
      },
      table: {
        // Colonne cachée par défaut (seulement visible pour les admins)
        defaultVisible: {
          xs: false,
          sm: false,
          md: false,
          lg: false,
          xl: false,
        },
        // Vérification supplémentaire de visibilité selon les permissions
        visibleIf: (ctx) => {
          const can = ctx?.capabilities?.updateAny || ctx?.meta?.capabilities?.updateAny || false;
          return can;
        },
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
        tooltip: "Contrôle la visibilité de la ressource côté frontend",
      },
      edition: {
        form: {
          type: "select",
          group: "Statut",
          help: "Contrôle la visibilité côté front. Le backend reste la vérité sécurité.",
          required: false,
          // Utiliser la constante du formatter (pas de duplication)
          options: VisibilityFormatter.options.map(({ value, label }) => ({ value, label })),
        },
        bulk: {
          enabled: true,
          nullable: false,
        },
      },
    },
    
    usable: {
      key: "usable",
      general: {
        label: "Utilisable",
        icon: "fa-solid fa-check",
        tooltip: "Indique si la ressource peut être utilisée",
      },
      table: {
        cell: {
          sizes: {
            xs: { mode: "boolIcon" },
            sm: { mode: "boolIcon" },
            md: { mode: "boolBadge" },
            lg: { mode: "boolBadge" },
            xl: { mode: "boolBadge" },
          },
        },
      },
      display: {
        tooltip: "Indique si la ressource peut être utilisée",
      },
      edition: {
        form: {
          type: "checkbox",
          group: "Statut",
          required: false,
          defaultValue: false,
        },
        bulk: {
          enabled: true,
          nullable: false,
        },
      },
    },
    
    auto_update: {
      key: "auto_update",
      general: {
        label: "Auto-update",
        icon: "fa-solid fa-arrows-rotate",
        tooltip: "Active la mise à jour automatique depuis DofusDB",
      },
      permissions: {
        visibleIf: (ctx) => Boolean(ctx?.capabilities?.updateAny ?? ctx?.meta?.capabilities?.updateAny),
      },
      table: {
        // Colonne cachée par défaut (seulement visible pour les admins)
        defaultVisible: {
          xs: false,
          sm: false,
          md: false,
          lg: false,
          xl: false,
        },
        // Vérification supplémentaire de visibilité selon les permissions
        visibleIf: (ctx) => Boolean(ctx?.capabilities?.updateAny ?? ctx?.meta?.capabilities?.updateAny),
        cell: {
          sizes: {
            xs: { mode: "boolIcon" },
            sm: { mode: "boolIcon" },
            md: { mode: "boolBadge" },
            lg: { mode: "boolBadge" },
            xl: { mode: "boolBadge" },
          },
        },
      },
      display: {
        tooltip: "Active la mise à jour automatique depuis DofusDB",
      },
      edition: {
        form: {
          type: "checkbox",
          group: "Statut",
          required: false,
          defaultValue: false,
        },
        bulk: {
          enabled: true,
          nullable: false,
        },
      },
    },
    
    dofusdb_id: {
      key: "dofusdb_id",
      general: {
        label: "DofusDB",
        icon: "fa-solid fa-arrow-up-right-from-square",
        tooltip: "Identifiant externe de la ressource dans DofusDB",
      },
      permissions: {
        visibleIf: (ctx) => Boolean(ctx?.capabilities?.updateAny ?? ctx?.meta?.capabilities?.updateAny),
      },
      table: {
        // Colonne cachée par défaut (seulement visible pour les admins)
        defaultVisible: {
          xs: false,
          sm: false,
          md: false,
          lg: false,
          xl: false,
        },
        // Vérification supplémentaire de visibilité selon les permissions
        visibleIf: (ctx) => Boolean(ctx?.capabilities?.updateAny ?? ctx?.meta?.capabilities?.updateAny),
        cell: {
          sizes: {
            xs: { mode: "routeExternal", truncate: 10 },
            sm: { mode: "routeExternal", truncate: 15 },
            md: { mode: "routeExternal", truncate: 20 },
            lg: { mode: "routeExternal" },
            xl: { mode: "routeExternal" },
          },
        },
      },
      display: {
        tooltip: "Identifiant externe de la ressource dans DofusDB",
      },
      edition: {
        form: {
          type: "text",
          group: "Métadonnées",
          help: "ID externe DofusDB. Généralement géré automatiquement par le scrapping.",
          required: false,
        },
        bulk: {
          enabled: true,
          nullable: true,
        },
      },
    },
    
    official_id: {
      key: "official_id",
      general: {
        label: "ID Officiel",
        icon: "fa-solid fa-id-card",
        tooltip: "Identifiant officiel de la ressource",
      },
      table: {
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
        tooltip: "Identifiant officiel de la ressource",
      },
      edition: {
        form: {
          type: "text",
          group: "Métadonnées",
          required: false,
        },
        bulk: {
          enabled: true,
          nullable: true,
        },
      },
    },
    
    created_by: {
      key: "created_by",
      general: {
        label: "Créé par",
        icon: "fa-solid fa-user",
        tooltip: "Utilisateur ayant créé la ressource",
      },
      permissions: {
        visibleIf: (ctx) => Boolean(ctx?.capabilities?.createAny ?? ctx?.meta?.capabilities?.createAny),
      },
      table: {
        // Colonne cachée par défaut (seulement visible pour les admins)
        defaultVisible: {
          xs: false,
          sm: false,
          md: false,
          lg: false,
          xl: false,
        },
        // Vérification supplémentaire de visibilité selon les permissions
        visibleIf: (ctx) => Boolean(ctx?.capabilities?.createAny ?? ctx?.meta?.capabilities?.createAny),
        cell: {
          sizes: {
            xs: { mode: "text", truncate: 10 },
            sm: { mode: "text", truncate: 15 },
            md: { mode: "text", truncate: 20 },
            lg: { mode: "text" },
            xl: { mode: "text" },
          },
        },
      },
      display: {
        tooltip: "Utilisateur ayant créé la ressource",
      },
      // Pas de section edition : champ système, non éditable
    },
    
    created_at: {
      key: "created_at",
      general: {
        label: "Créé le",
        icon: "fa-solid fa-calendar",
        tooltip: "Date de création de la ressource",
      },
      permissions: {
        visibleIf: (ctx) => Boolean(ctx?.capabilities?.createAny ?? ctx?.meta?.capabilities?.createAny),
      },
      table: {
        // Colonne cachée par défaut (seulement visible pour les admins)
        defaultVisible: {
          xs: false,
          sm: false,
          md: false,
          lg: false,
          xl: false,
        },
        // Vérification supplémentaire de visibilité selon les permissions
        visibleIf: (ctx) => Boolean(ctx?.capabilities?.createAny ?? ctx?.meta?.capabilities?.createAny),
        cell: {
          sizes: {
            xs: { mode: "dateShort" },
            sm: { mode: "dateShort" },
            md: { mode: "dateTime" },
            lg: { mode: "dateTime" },
            xl: { mode: "dateTime" },
          },
        },
      },
      display: {
        tooltip: "Date de création de la ressource",
      },
      // Pas de section edition : champ système, non éditable
    },
    
    updated_at: {
      key: "updated_at",
      general: {
        label: "Modifié le",
        icon: "fa-solid fa-clock",
        tooltip: "Date de dernière modification de la ressource",
      },
      permissions: {
        visibleIf: (ctx) => Boolean(ctx?.capabilities?.createAny ?? ctx?.meta?.capabilities?.createAny),
      },
      table: {
        // Colonne cachée par défaut (seulement visible pour les admins)
        defaultVisible: {
          xs: false,
          sm: false,
          md: false,
          lg: false,
          xl: false,
        },
        // Vérification supplémentaire de visibilité selon les permissions
        visibleIf: (ctx) => Boolean(ctx?.capabilities?.createAny ?? ctx?.meta?.capabilities?.createAny),
        cell: {
          sizes: {
            xs: { mode: "dateShort" },
            sm: { mode: "dateShort" },
            md: { mode: "dateTime" },
            lg: { mode: "dateTime" },
            xl: { mode: "dateTime" },
          },
        },
      },
      display: {
        tooltip: "Date de dernière modification de la ressource",
      },
      // Pas de section edition : champ système, non éditable
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
