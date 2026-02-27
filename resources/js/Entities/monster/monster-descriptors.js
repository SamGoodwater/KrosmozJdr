/**
 * Monster field descriptors — Aligné sur la structure Resource
 *
 * @description
 * Source de vérité côté frontend pour :
 * - Configuration tableau (colonnes, cellules, visibilité)
 * - Configuration formulaires (édition simple et bulk)
 * - Permissions d'affichage (visibleIf)
 *
 * Structure identique à resource-descriptors : general, table, display, edition, permissions.
 *
 * @example
 * import { getMonsterFieldDescriptors } from "@/Entities/monster/monster-descriptors";
 * const descriptors = getMonsterFieldDescriptors({ capabilities, creatures, monsterRaces });
 */

/**
 * @typedef {Object} MonsterFieldDescriptor
 * @property {string} key - Clé unique du champ
 * @property {Object} general - Métadonnées générales
 * @property {string} general.label - Libellé affiché
 * @property {string} [general.icon] - Icône FontAwesome
 * @property {string} [general.tooltip] - Tooltip
 * @property {Object} [table] - Configuration tableau
 * @property {Object} [table.cell] - Configuration cellules
 * @property {Record<"xs"|"sm"|"md"|"lg"|"xl", {mode?: string, truncate?: number}>} [table.cell.sizes]
 * @property {Record<"xs"|"sm"|"md"|"lg"|"xl", boolean>} [table.defaultVisible]
 * @property {(ctx: any) => boolean} [table.visibleIf]
 * @property {Object} [display] - Configuration affichage
 * @property {string} [display.tooltip]
 * @property {Object} [edition] - Configuration édition
 * @property {Object} [edition.form] - Formulaire
 * @property {Object} [edition.bulk] - Édition en masse
 * @property {Object} [permissions] - Permissions
 * @property {(ctx: any) => boolean} [permissions.visibleIf]
 */

/**
 * Retourne les descripteurs de tous les champs de l'entité "Monster".
 *
 * @param {Object} ctx - Contexte d'exécution
 * @param {Object} [ctx.capabilities] - Permissions (updateAny, createAny, etc.)
 * @param {Array} [ctx.creatures] - Liste des créatures (select options)
 * @param {Array} [ctx.monsterRaces] - Liste des races de monstres (select options)
 * @returns {Record<string, MonsterFieldDescriptor>}
 */
export function getMonsterFieldDescriptors(ctx = {}) {
  const can = ctx?.capabilities || ctx?.meta?.capabilities || {};
  const canUpdateAny = Boolean(can?.updateAny);
  const canCreateAny = Boolean(can?.createAny);

  const creatures = Array.isArray(ctx?.creatures)
    ? ctx.creatures
    : Array.isArray(ctx?.meta?.creatures)
      ? ctx.meta.creatures
      : [];
  const monsterRaces = Array.isArray(ctx?.monsterRaces)
    ? ctx.monsterRaces
    : Array.isArray(ctx?.meta?.monsterRaces)
      ? ctx.meta.monsterRaces
      : [];

  const creatureOptions = () => [{ value: '', label: '—' }, ...creatures.map((c) => ({ value: c.id, label: c.name }))];
  const raceOptions = () => [{ value: '', label: '—' }, ...monsterRaces.map((r) => ({ value: r.id, label: r.name }))];

  return {
    id: {
      key: 'id',
      general: {
        label: 'ID',
        icon: 'fa-solid fa-hashtag',
        tooltip: 'Identifiant unique du monstre',
      },
      permissions: {
        visibleIf: () => canCreateAny,
      },
      table: {
        sortable: true,
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        visibleIf: () => canCreateAny,
        cell: {
          sizes: {
            xs: { mode: 'text' },
            sm: { mode: 'text' },
            md: { mode: 'text' },
            lg: { mode: 'text' },
            xl: { mode: 'text' },
          },
        },
      },
    },

    creature_name: {
      key: 'creature_name',
      general: {
        label: 'Créature',
        icon: 'fa-solid fa-dragon',
        tooltip: 'Nom de la créature associée au monstre',
      },
      table: {
        sortable: true,
        searchable: true,
        defaultVisible: { xs: true, sm: true, md: true, lg: true, xl: true },
        cell: {
          sizes: {
            xs: { mode: 'route', truncate: 15 },
            sm: { mode: 'route', truncate: 20 },
            md: { mode: 'route', truncate: 30 },
            lg: { mode: 'route', truncate: 40 },
            xl: { mode: 'route' },
          },
        },
      },
      display: {
        tooltip: 'Nom de la créature associée au monstre',
      },
      // Pas d'edition ici : le formulaire utilise creature_id (select)
    },

    creature_id: {
      key: 'creature_id',
      general: {
        label: 'Créature',
        icon: 'fa-solid fa-dragon',
        tooltip: 'Créature associée au monstre',
      },
      edition: {
        form: {
          type: 'select',
          group: 'Relations',
          required: true,
          options: creatureOptions,
        },
        bulk: { enabled: false },
      },
    },

    monster_race: {
      key: 'monster_race',
      general: {
        label: 'Race',
        icon: 'fa-solid fa-users',
        tooltip: 'Race du monstre',
      },
      table: {
        sortable: true,
        filterable: {
          id: 'monster_race_id',
          type: 'multi',
        },
        defaultVisible: { xs: false, sm: true, md: true, lg: true, xl: true },
        cell: {
          sizes: {
            xs: { mode: 'text', truncate: 10 },
            sm: { mode: 'text', truncate: 15 },
            md: { mode: 'text', truncate: 20 },
            lg: { mode: 'text' },
            xl: { mode: 'text' },
          },
        },
      },
      display: {
        tooltip: 'Race du monstre',
      },
      // Édition via monster_race_id
    },

    monster_race_id: {
      key: 'monster_race_id',
      general: {
        label: 'Race',
        icon: 'fa-solid fa-users',
        tooltip: 'Race du monstre',
      },
      edition: {
        form: {
          type: 'select',
          group: 'Relations',
          required: false,
          options: raceOptions,
        },
        bulk: { enabled: true, nullable: true },
      },
    },

    size: {
      key: 'size',
      general: {
        label: 'Taille',
        icon: 'fa-solid fa-expand',
        tooltip: 'Taille du monstre (0-5)',
      },
      table: {
        sortable: true,
        filterable: {
          id: 'size',
          type: 'select',
        },
        defaultVisible: { xs: false, sm: true, md: true, lg: true, xl: true },
        cell: {
          sizes: {
            xs: { mode: 'badge' },
            sm: { mode: 'badge' },
            md: { mode: 'badge' },
            lg: { mode: 'badge' },
            xl: { mode: 'badge' },
          },
        },
      },
      display: {
        tooltip: 'Taille du monstre (Minuscule à Gigantesque)',
      },
      edition: {
        form: {
          type: 'select',
          group: 'Caractéristiques',
          required: false,
          defaultValue: 2,
          options: [
            { value: 0, label: 'Minuscule' },
            { value: 1, label: 'Petit' },
            { value: 2, label: 'Moyen' },
            { value: 3, label: 'Grand' },
            { value: 4, label: 'Colossal' },
            { value: 5, label: 'Gigantesque' },
          ],
        },
        bulk: { enabled: true, nullable: true },
      },
    },

    is_boss: {
      key: 'is_boss',
      general: {
        label: 'Boss',
        icon: 'fa-solid fa-crown',
        tooltip: 'Indique si le monstre est un boss',
      },
      table: {
        sortable: true,
        filterable: {
          id: 'is_boss',
          type: 'boolean',
        },
        defaultVisible: { xs: false, sm: true, md: true, lg: true, xl: true },
        cell: {
          sizes: {
            xs: { mode: 'boolBadge' },
            sm: { mode: 'boolBadge' },
            md: { mode: 'boolBadge' },
            lg: { mode: 'boolBadge' },
            xl: { mode: 'boolBadge' },
          },
        },
      },
      display: {
        tooltip: 'Indique si le monstre est un boss',
      },
      edition: {
        form: {
          type: 'checkbox',
          group: 'Caractéristiques',
          required: false,
          defaultValue: false,
        },
        bulk: { enabled: true, nullable: false },
      },
    },

    boss_pa: {
      key: 'boss_pa',
      general: {
        label: 'PA Boss',
        icon: 'fa-solid fa-bolt',
        tooltip: 'Points d’action du boss',
      },
      table: {
        sortable: true,
        defaultVisible: { xs: false, sm: false, md: true, lg: true, xl: true },
        cell: {
          sizes: {
            xs: { mode: 'text' },
            sm: { mode: 'text' },
            md: { mode: 'text' },
            lg: { mode: 'text' },
            xl: { mode: 'text' },
          },
        },
      },
      display: {
        tooltip: 'Points d’action du boss',
      },
      edition: {
        form: {
          type: 'text',
          group: 'Caractéristiques',
          placeholder: 'Ex: 6',
          required: false,
        },
        bulk: { enabled: true, nullable: true },
      },
    },

    dofus_version: {
      key: 'dofus_version',
      general: {
        label: 'Version Dofus',
        icon: 'fa-solid fa-code-branch',
        tooltip: 'Version du jeu pour laquelle le monstre est disponible',
      },
      permissions: {
        visibleIf: () => canUpdateAny,
      },
      table: {
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        visibleIf: () => canUpdateAny,
        cell: {
          sizes: {
            xs: { mode: 'text', truncate: 10 },
            sm: { mode: 'text', truncate: 15 },
            md: { mode: 'text', truncate: 20 },
            lg: { mode: 'text' },
            xl: { mode: 'text' },
          },
        },
      },
      display: {
        tooltip: 'Version du jeu Dofus',
      },
      edition: {
        form: {
          type: 'text',
          group: 'Métadonnées',
          required: false,
        },
        bulk: { enabled: true, nullable: true },
      },
    },

    auto_update: {
      key: 'auto_update',
      general: {
        label: 'Auto-update',
        icon: 'fa-solid fa-arrows-rotate',
        tooltip: 'Mise à jour automatique depuis DofusDB',
      },
      permissions: {
        visibleIf: () => canUpdateAny,
      },
      table: {
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        visibleIf: () => canUpdateAny,
        cell: {
          sizes: {
            xs: { mode: 'boolIcon' },
            sm: { mode: 'boolIcon' },
            md: { mode: 'boolBadge' },
            lg: { mode: 'boolBadge' },
            xl: { mode: 'boolBadge' },
          },
        },
      },
      display: {
        tooltip: 'Mise à jour automatique depuis DofusDB',
      },
      edition: {
        form: {
          type: 'checkbox',
          group: 'Statut',
          required: false,
          defaultValue: false,
        },
        bulk: { enabled: true, nullable: false },
      },
    },

    dofusdb_id: {
      key: 'dofusdb_id',
      general: {
        label: 'DofusDB',
        icon: 'fa-solid fa-arrow-up-right-from-square',
        tooltip: 'Identifiant externe DofusDB',
      },
      permissions: {
        visibleIf: () => canUpdateAny,
      },
      table: {
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        visibleIf: () => canUpdateAny,
        cell: {
          sizes: {
            xs: { mode: 'routeExternal', truncate: 10 },
            sm: { mode: 'routeExternal', truncate: 15 },
            md: { mode: 'routeExternal', truncate: 20 },
            lg: { mode: 'routeExternal' },
            xl: { mode: 'routeExternal' },
          },
        },
      },
      display: {
        tooltip: 'Identifiant externe DofusDB',
      },
      edition: {
        form: {
          type: 'text',
          group: 'Métadonnées',
          help: 'ID externe DofusDB (géré par le scrapping).',
          required: false,
        },
        bulk: { enabled: true, nullable: true },
      },
    },

    official_id: {
      key: 'official_id',
      general: {
        label: 'ID Officiel',
        icon: 'fa-solid fa-id-card',
        tooltip: 'Identifiant officiel du monstre',
      },
      table: {
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        cell: {
          sizes: {
            xs: { mode: 'text' },
            sm: { mode: 'text' },
            md: { mode: 'text' },
            lg: { mode: 'text' },
            xl: { mode: 'text' },
          },
        },
      },
      edition: {
        form: {
          type: 'text',
          group: 'Métadonnées',
          required: false,
        },
        bulk: { enabled: true, nullable: true },
      },
    },

    created_at: {
      key: 'created_at',
      general: {
        label: 'Créé le',
        icon: 'fa-solid fa-calendar-plus',
        tooltip: 'Date de création',
      },
      permissions: {
        visibleIf: () => canCreateAny,
      },
      table: {
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        visibleIf: () => canCreateAny,
        cell: {
          sizes: {
            xs: { mode: 'dateShort' },
            sm: { mode: 'dateShort' },
            md: { mode: 'dateTime' },
            lg: { mode: 'dateTime' },
            xl: { mode: 'dateTime' },
          },
        },
      },
      display: {
        tooltip: 'Date de création',
      },
    },

    updated_at: {
      key: 'updated_at',
      general: {
        label: 'Modifié le',
        icon: 'fa-solid fa-clock',
        tooltip: 'Date de dernière modification',
      },
      permissions: {
        visibleIf: () => canCreateAny,
      },
      table: {
        defaultVisible: { xs: false, sm: false, md: false, lg: false, xl: false },
        visibleIf: () => canCreateAny,
        cell: {
          sizes: {
            xs: { mode: 'dateShort' },
            sm: { mode: 'dateShort' },
            md: { mode: 'dateTime' },
            lg: { mode: 'dateTime' },
            xl: { mode: 'dateTime' },
          },
        },
      },
      display: {
        tooltip: 'Date de dernière modification',
      },
    },

    _tableConfig: {
      id: 'monsters.index',
      entityType: 'monster',
      quickEdit: {
        enabled: true,
        permission: 'updateAny',
      },
      actions: {
        enabled: true,
        permission: 'view',
        available: ['view', 'quick-view', 'edit', 'quick-edit', 'delete', 'copy-link', 'download-pdf', 'refresh'],
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
          placeholder: 'Rechercher un monstre…',
          debounceMs: 200,
        },
        filters: { enabled: true },
        pagination: {
          enabled: true,
          perPage: { default: 25, options: [10, 25, 50, 100] },
        },
        selection: {
          enabled: true,
          checkboxMode: 'auto',
          clickToSelect: true,
        },
        columnVisibility: {
          enabled: true,
          persist: true,
        },
        export: {
          csv: true,
          filename: 'monsters.csv',
        },
      },
      ui: {
        skeletonRows: 10,
      },
    },

    _quickeditConfig: {
      fields: [
        'monster_race_id',
        'size',
        'is_boss',
        'boss_pa',
        'auto_update',
        'dofus_version',
        'dofusdb_id',
      ],
    },
  };
}

export default getMonsterFieldDescriptors;
