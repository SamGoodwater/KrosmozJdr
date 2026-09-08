/**
 * NPC field descriptors — Version simplifiée
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
 * import { getNpcFieldDescriptors } from "@/Entities/npc/npc-descriptors";
 * const descriptors = getNpcFieldDescriptors({ meta });
 */

import { getEntityStateOptions, getUserRoleOptions } from '@/Utils/Entity/SharedConstants.js';

/**
 * @typedef {Object} NpcFieldDescriptor
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

/**
 * Retourne les descripteurs de tous les champs de l'entité "NPC".
 * 
 * @param {Object} ctx - Contexte d'exécution
 * @param {Object} [ctx.capabilities] - Permissions disponibles (ou ctx.meta.capabilities)
 * @param {Array} [ctx.breeds] - Liste des breeds / classes (ou ctx.meta.breeds)
 * @param {Array} [ctx.specializations] - Liste des spécialisations (ou ctx.meta.specializations)
 * @returns {Record<string, NpcFieldDescriptor>} Objet avec tous les descripteurs
 */
export function getNpcFieldDescriptors(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || null;
  const canUpdateAny = Boolean(can?.updateAny);
  const canCreateAny = Boolean(can?.createAny);
  
  const breeds = Array.isArray(ctx?.breeds)
    ? ctx.breeds
    : (Array.isArray(ctx?.meta?.breeds) ? ctx.meta.breeds : []);
  
  const specializations = Array.isArray(ctx?.specializations) 
    ? ctx.specializations 
    : (Array.isArray(ctx?.meta?.specializations) ? ctx.meta.specializations : []);

  return {
    id: {
      key: "id",
      label: "ID",
      icon: "fa-solid fa-hashtag",
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
    creature_name: {
      key: "creature_name",
      label: "Nom",
      icon: "fa-solid fa-user",
      table: {
        sortable: true,
        searchable: true,
        defaultVisible: { xs: true, sm: true, md: true, lg: true, xl: true },
        cell: { sizes: { xs: { mode: "text", truncate: 15 }, sm: { mode: "text", truncate: 20 }, md: { mode: "text", truncate: 30 }, lg: { mode: "text", truncate: 40 }, xl: { mode: "text" } } },
      },
      display: {
        sizes: {
          xs: { mode: "text", truncate: 15 },
          sm: { mode: "text", truncate: 20 },
          md: { mode: "text", truncate: 30 },
          lg: { mode: "text", truncate: 40 },
          xl: { mode: "text" },
        },
      },
    },
    name: {
      key: "name",
      label: "Nom",
      icon: "fa-solid fa-user",
      edit: {
        form: {
          type: "text",
          group: "Identité",
          required: true,
          showInCompact: true,
          bulk: { enabled: false },
        },
      },
    },
    location: {
      key: "location",
      label: "Lieu",
      icon: "fa-solid fa-map-marker-alt",
      edit: {
        form: {
          type: "text",
          group: "Identité",
          required: false,
          showInCompact: true,
          bulk: { enabled: false },
        },
      },
    },
    level: {
      key: "level",
      label: "Niveau",
      icon: "fa-solid fa-level-up-alt",
      edit: {
        form: {
          type: "text",
          group: "Identité",
          required: false,
          showInCompact: true,
          bulk: { enabled: false },
        },
      },
    },
    hostility: {
      key: "hostility",
      label: "Hostilité",
      icon: "fa-solid fa-mask",
      edit: {
        form: {
          type: "select",
          group: "Identité",
          required: false,
          showInCompact: true,
          options: [
            { value: 0, label: "Amical" },
            { value: 1, label: "Curieux" },
            { value: 2, label: "Neutre" },
            { value: 3, label: "Hostile" },
            { value: 4, label: "Agressif" },
          ],
          bulk: { enabled: false },
        },
      },
    },
    description: {
      key: "description",
      label: "Description",
      icon: "fa-solid fa-align-left",
      edit: {
        form: {
          type: "textarea",
          group: "Identité",
          required: false,
          showInCompact: false,
          bulk: { enabled: false },
        },
      },
    },
    breed: {
      key: "breed",
      label: "Classe",
      icon: "fa-solid fa-user-tie",
      table: {
        sortable: true,
        searchable: true,
        filterable: { id: "breed_id", type: "multi", defaultVisible: false },
        defaultVisible: { xs: false, sm: true, md: true, lg: true, xl: true },
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
    breed_id: {
      key: "breed_id",
      label: "Classe",
      icon: "fa-solid fa-user-tie",
      edit: {
        form: {
          type: "select",
          group: "Identité",
          required: false,
          showInCompact: true,
          options: () => [{ value: "", label: "—" }, ...breeds.map((b) => ({ value: b.id, label: b.name }))],
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : Number(v)) },
        },
      },
    },
    creature_level: {
      key: "creature_level",
      label: "Niveau",
      icon: "fa-solid fa-level-up-alt",
      table: {
        sortable: true,
        searchable: true,
        filterable: {
          id: "creature_level",
          type: "range",
          defaultVisible: true,
          ui: { min: 1, max: 200, step: 1 },
        },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "badge" }, sm: { mode: "badge" }, md: { mode: "badge" }, lg: { mode: "badge" }, xl: { mode: "badge" } } },
      },
      display: { sizes: { xs: { mode: "badge" }, sm: { mode: "badge" }, md: { mode: "badge" }, lg: { mode: "badge" }, xl: { mode: "badge" } } },
    },
    creature_life: {
      key: "creature_life",
      label: "Vie",
      icon: "fa-solid fa-heart",
      general: { label: "Vie", icon: "fa-solid fa-heart", tooltip: "Points de vie de la créature" },
      table: {
        sortable: true,
        searchable: true,
        filterable: { id: "creature_life", type: "range", defaultVisible: false, ui: { min: 0, max: 500, step: 1 } },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
    },
    creature_pa: {
      key: "creature_pa",
      label: "PA",
      icon: "fa-solid fa-bolt",
      general: { label: "PA", icon: "fa-solid fa-bolt", tooltip: "Points d’action de la créature" },
      table: {
        sortable: true,
        filterable: { id: "creature_pa", type: "range", defaultVisible: false, ui: { min: 0, max: 20, step: 1 } },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
    },
    creature_pm: {
      key: "creature_pm",
      label: "PM",
      icon: "fa-solid fa-shoe-prints",
      general: { label: "PM", icon: "fa-solid fa-shoe-prints", tooltip: "Points de mouvement de la créature" },
      table: {
        sortable: true,
        filterable: { id: "creature_pm", type: "range", defaultVisible: false, ui: { min: 0, max: 20, step: 1 } },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
    },
    creature_po: {
      key: "creature_po",
      label: "PO",
      icon: "fa-solid fa-crosshairs",
      general: { label: "PO", icon: "fa-solid fa-crosshairs", tooltip: "Portée de la créature" },
      table: {
        sortable: true,
        filterable: { id: "creature_po", type: "range", defaultVisible: false, ui: { min: 0, max: 20, step: 1 } },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
    },
    creature_ini: {
      key: "creature_ini",
      label: "Initiative",
      icon: "fa-solid fa-clock",
      general: { label: "Initiative", icon: "fa-solid fa-clock", tooltip: "Initiative de la créature" },
      table: {
        sortable: true,
        filterable: { id: "creature_ini", type: "range", defaultVisible: false, ui: { min: 0, max: 200, step: 1 } },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
    },
    creature_ca: {
      key: "creature_ca",
      label: "CA",
      icon: "fa-solid fa-shield-halved",
      general: { label: "CA", icon: "fa-solid fa-shield-halved", tooltip: "Classe d’armure de la créature" },
      table: {
        sortable: true,
        filterable: { id: "creature_ca", type: "range", defaultVisible: false, ui: { min: 0, max: 50, step: 1 } },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
    },
    creature_hostility: {
      key: "creature_hostility",
      label: "Hostilité",
      icon: "fa-solid fa-mask",
      general: { label: "Hostilité", icon: "fa-solid fa-mask", tooltip: "Niveau d’hostilité de la créature" },
      table: {
        sortable: true,
        filterable: { id: "creature_hostility", type: "multi", defaultVisible: false },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
    },
    creature_image: {
      key: "creature_image",
      label: "Image",
      icon: "fa-solid fa-image",
      general: { label: "Image", icon: "fa-solid fa-image", tooltip: "Image de la créature" },
      table: {
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "thumb" }, sm: { mode: "thumb" }, md: { mode: "thumb" }, lg: { mode: "thumb" }, xl: { mode: "thumb" } } },
      },
      display: { sizes: { xs: { mode: "thumb" }, sm: { mode: "thumb" }, md: { mode: "thumb" }, lg: { mode: "thumb" }, xl: { mode: "thumb" } } },
    },
    creature_strong: {
      key: "creature_strong",
      label: "Force",
      icon: "fa-solid fa-dumbbell",
      general: { label: "Force", icon: "fa-solid fa-dumbbell", tooltip: "Force de la créature" },
      table: {
        sortable: true,
        filterable: { id: "creature_strong", type: "range", defaultVisible: false, ui: { min: 0, max: 400, step: 1 } },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
    },
    creature_intel: {
      key: "creature_intel",
      label: "Intelligence",
      icon: "fa-solid fa-brain",
      general: { label: "Intelligence", icon: "fa-solid fa-brain", tooltip: "Intelligence de la créature" },
      table: {
        sortable: true,
        filterable: { id: "creature_intel", type: "range", defaultVisible: false, ui: { min: 0, max: 400, step: 1 } },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
    },
    creature_agi: {
      key: "creature_agi",
      label: "Agilité",
      icon: "fa-solid fa-wind",
      general: { label: "Agilité", icon: "fa-solid fa-wind", tooltip: "Agilité de la créature" },
      table: {
        sortable: true,
        filterable: { id: "creature_agi", type: "range", defaultVisible: false, ui: { min: 0, max: 400, step: 1 } },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
    },
    creature_chance: {
      key: "creature_chance",
      label: "Chance",
      icon: "fa-solid fa-clover",
      general: { label: "Chance", icon: "fa-solid fa-clover", tooltip: "Chance de la créature" },
      table: {
        sortable: true,
        filterable: { id: "creature_chance", type: "range", defaultVisible: false, ui: { min: 0, max: 400, step: 1 } },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
    },
    creature_vitality: {
      key: "creature_vitality",
      label: "Vitalité",
      icon: "fa-solid fa-heart-pulse",
      general: { label: "Vitalité", icon: "fa-solid fa-heart-pulse", tooltip: "Vitalité de la créature" },
      table: {
        sortable: true,
        filterable: { id: "creature_vitality", type: "range", defaultVisible: false, ui: { min: 0, max: 400, step: 1 } },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
    },
    creature_critical_hit: {
      key: "creature_critical_hit",
      label: "Bonus critique",
      icon: "fa-solid fa-crosshairs",
      general: { label: "Bonus critique", icon: "fa-solid fa-crosshairs", tooltip: "Seuil de critique (0=nat 20, 3=dès 17)" },
      table: {
        sortable: true,
        filterable: { id: "creature_critical_hit", type: "range", defaultVisible: false, ui: { min: 0, max: 50, step: 1 } },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
    },
    creature_heal_bonus: {
      key: "creature_heal_bonus",
      label: "Bonus soins",
      icon: "fa-solid fa-hand-holding-medical",
      general: { label: "Bonus soins", icon: "fa-solid fa-hand-holding-medical", tooltip: "Bonus ajouté à chaque soin" },
      table: {
        sortable: true,
        filterable: { id: "creature_heal_bonus", type: "range", defaultVisible: false, ui: { min: 0, max: 50, step: 1 } },
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
    },
    creature_state: {
      key: "creature_state",
      label: "État",
      icon: "fa-solid fa-toggle-on",
      table: {
        sortable: true,
        searchable: true,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: { sizes: { xs: { mode: "text" }, sm: { mode: "text" }, md: { mode: "text" }, lg: { mode: "text" }, xl: { mode: "text" } } },
    },
    specialization: {
      key: "specialization",
      label: "Spécialisation",
      icon: "fa-solid fa-star",
      table: {
        sortable: true,
        searchable: true,
        filterable: { id: "specialization_id", type: "multi", defaultVisible: false },
        defaultVisible: { xs: false, sm: false, md: true, lg: true, xl: true },
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
    specialization_id: {
      key: "specialization_id",
      label: "Spécialisation",
      icon: "fa-solid fa-star",
      edit: {
        form: {
          type: "select",
          group: "Identité",
          required: false,
          showInCompact: false,
          options: () => [{ value: "", label: "—" }, ...specializations.map((s) => ({ value: s.id, label: s.name }))],
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : Number(v)) },
        },
      },
    },
    npc_role: {
      key: "npc_role",
      label: "Rôle",
      icon: "fa-solid fa-masks-theater",
      table: {
        sortable: true,
        filterable: {
          id: "npc_role",
          type: "multi",
          defaultVisible: true,
        },
        defaultVisible: { xs: false, sm: true, md: true, lg: true, xl: true },
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
          group: "Identité",
          required: false,
          showInCompact: true,
          options: [
            { value: "", label: "—" },
            { value: "social", label: "Social" },
            { value: "merchant", label: "Marchand" },
            { value: "guard", label: "Garde" },
            { value: "ally", label: "Allié" },
            { value: "enemy", label: "Ennemi" },
            { value: "other", label: "Autre" },
          ],
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    creature_location: {
      key: "creature_location",
      label: "Lieu",
      icon: "fa-solid fa-map-marker-alt",
      table: {
        sortable: true,
        searchable: true,
        defaultVisible: { xs: false, sm: false, md: true, lg: true, xl: true },
        cell: { sizes: { xs: { mode: "text", truncate: 15 }, sm: { mode: "text", truncate: 20 }, md: { mode: "text", truncate: 30 }, lg: { mode: "text" }, xl: { mode: "text" } } },
      },
      display: {
        sizes: {
          xs: { mode: "text", truncate: 15 },
          sm: { mode: "text", truncate: 20 },
          md: { mode: "text", truncate: 30 },
          lg: { mode: "text" },
          xl: { mode: "text" },
        },
      },
    },
    creature_summary_combat: {
      key: "creature_summary_combat",
      label: "Combat",
      icon: "fa-solid fa-bolt",
      table: {
        type: "chips",
        sortable: true,
        searchable: true,
        defaultVisible: { xs: false, sm: false, md: true, lg: true, xl: true },
        cell: { sizes: { xs: { mode: "chips" }, sm: { mode: "chips" }, md: { mode: "chips" }, lg: { mode: "chips" }, xl: { mode: "chips" } } },
      },
      display: { sizes: { xs: { mode: "chips" }, sm: { mode: "chips" }, md: { mode: "chips" }, lg: { mode: "chips" }, xl: { mode: "chips" } } },
    },
    creature_summary_resistance: {
      key: "creature_summary_resistance",
      label: "Résistances",
      icon: "fa-solid fa-shield-halved",
      table: {
        type: "chips",
        sortable: true,
        searchable: true,
        defaultVisible: { xs: false, sm: false, md: true, lg: true, xl: true },
        cell: { sizes: { xs: { mode: "chips" }, sm: { mode: "chips" }, md: { mode: "chips" }, lg: { mode: "chips" }, xl: { mode: "chips" } } },
      },
      display: { sizes: { xs: { mode: "chips" }, sm: { mode: "chips" }, md: { mode: "chips" }, lg: { mode: "chips" }, xl: { mode: "chips" } } },
    },
    creature_summary_damage: {
      key: "creature_summary_damage",
      label: "Dommages",
      icon: "fa-solid fa-hand-back-fist",
      table: {
        type: "chips",
        sortable: true,
        searchable: true,
        defaultVisible: { xs: false, sm: false, md: true, lg: true, xl: true },
        cell: { sizes: { xs: { mode: "chips" }, sm: { mode: "chips" }, md: { mode: "chips" }, lg: { mode: "chips" }, xl: { mode: "chips" } } },
      },
      display: { sizes: { xs: { mode: "chips" }, sm: { mode: "chips" }, md: { mode: "chips" }, lg: { mode: "chips" }, xl: { mode: "chips" } } },
    },
    creature_summary_stats: {
      key: "creature_summary_stats",
      label: "Caractéristiques",
      icon: "fa-solid fa-chart-simple",
      table: {
        type: "chips",
        sortable: true,
        searchable: true,
        defaultVisible: { xs: false, sm: false, md: true, lg: true, xl: true },
        cell: { sizes: { xs: { mode: "chips" }, sm: { mode: "chips" }, md: { mode: "chips" }, lg: { mode: "chips" }, xl: { mode: "chips" } } },
      },
      display: { sizes: { xs: { mode: "chips" }, sm: { mode: "chips" }, md: { mode: "chips" }, lg: { mode: "chips" }, xl: { mode: "chips" } } },
    },
    creature_summary_control: {
      key: "creature_summary_control",
      label: "Contrôle",
      icon: "fa-solid fa-shield",
      table: {
        type: "chips",
        sortable: true,
        searchable: true,
        defaultVisible: { xs: false, sm: false, md: true, lg: true, xl: true },
        cell: { sizes: { xs: { mode: "chips" }, sm: { mode: "chips" }, md: { mode: "chips" }, lg: { mode: "chips" }, xl: { mode: "chips" } } },
      },
      display: { sizes: { xs: { mode: "chips" }, sm: { mode: "chips" }, md: { mode: "chips" }, lg: { mode: "chips" }, xl: { mode: "chips" } } },
    },
    creature_characteristics: {
      key: "creature_characteristics",
      label: "Caractéristiques (tout)",
      icon: "fa-solid fa-chart-simple",
      table: {
        type: "chips",
        sortable: true,
        searchable: true,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: { sizes: { xs: { mode: "chips" }, sm: { mode: "chips" }, md: { mode: "chips" }, lg: { mode: "chips" }, xl: { mode: "chips" } } },
      },
      display: { sizes: { xs: { mode: "chips" }, sm: { mode: "chips" }, md: { mode: "chips" }, lg: { mode: "chips" }, xl: { mode: "chips" } } },
    },
    npc_summary_relations: {
      key: "npc_summary_relations",
      label: "Relations",
      icon: "fa-solid fa-link",
      table: {
        type: "chips",
        sortable: false,
        searchable: true,
        defaultVisible: { xs: false, sm: false, md: true, lg: true, xl: true },
        cell: { sizes: { xs: { mode: "chips" }, sm: { mode: "chips" }, md: { mode: "chips" }, lg: { mode: "chips" }, xl: { mode: "chips" } } },
      },
      display: { sizes: { xs: { mode: "chips" }, sm: { mode: "chips" }, md: { mode: "chips" }, lg: { mode: "chips" }, xl: { mode: "chips" } } },
    },
    story: {
      key: "story",
      label: "Histoire",
      icon: "fa-solid fa-book",
      table: {
        searchable: true,
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
          type: "textarea",
          group: "Description",
          required: false,
          showInCompact: false,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    historical: {
      key: "historical",
      label: "Historique",
      icon: "fa-solid fa-scroll",
      table: {
        searchable: true,
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
          type: "textarea",
          group: "Description",
          required: false,
          showInCompact: false,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    age: {
      key: "age",
      label: "Âge",
      icon: "fa-solid fa-birthday-cake",
      table: {
        sortable: true,
        defaultVisible: { xs: false, sm: false, md: true, lg: true, xl: true },
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
          group: "Caractéristiques",
          required: false,
          showInCompact: true,
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : String(v)) },
        },
      },
    },
    size: {
      key: "size",
      label: "Taille",
      icon: "fa-solid fa-expand",
      table: {
        sortable: true,
        filterable: {
          id: "size",
          type: "multi",
          ui: { searchable: false },
          defaultVisible: false,
        },
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
          group: "Caractéristiques",
          required: false,
          showInCompact: true,
          defaultValue: 2,
          options: [
            { value: 0, label: "Minuscule" },
            { value: 1, label: "Petit" },
            { value: 2, label: "Moyen" },
            { value: 3, label: "Grand" },
            { value: 4, label: "Colossal" },
            { value: 5, label: "Gigantesque" },
          ],
          bulk: { enabled: true, nullable: true, build: (v) => (v === "" ? null : Number(v)) },
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
    read_level: {
      key: "read_level",
      label: "Lecture (min.)",
      icon: "fa-solid fa-eye",
      table: {
        sortable: true,
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
        sortable: true,
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
          defaultValue: 3,
          bulk: { enabled: true, nullable: false, build: (v) => Number(v) },
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
      id: "npcs.index",
      entityType: "npc",
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
          placeholder: "Rechercher un PNJ",
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
          filename: "npcs.csv",
        },
      },
      ui: {
        skeletonRows: 10,
      },
    },
  };
}
