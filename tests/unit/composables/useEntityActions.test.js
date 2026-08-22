/**
 * Tests unitaires pour useEntityActions
 *
 * @description
 * Vérifie que :
 * - Le filtrage des actions selon les permissions fonctionne
 * - Le filtrage whitelist/blacklist fonctionne
 * - Le groupement des actions fonctionne
 * - Le contexte (inPanel) est pris en compte
 */

import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import { defineComponent } from 'vue';
import { useEntityActions } from '@/Composables/entity/useEntityActions';

// Mock usePermissions
const mockPermissions = {
  can: vi.fn((entityType, ability) => {
    // Par défaut, toutes les permissions sont accordées sauf manageAny
    if (ability === 'manageAny') return false;
    return true;
  }),
  canViewAny: vi.fn(() => true),
  canUpdateAny: vi.fn(() => true),
  canDeleteAny: vi.fn(() => true),
  canManageAny: vi.fn(() => false),
  isAdmin: { value: false },
  authUser: { value: null },
};

const readAvailableActions = (wrapper) => {
  return wrapper.vm.availableActions?.value ?? wrapper.vm.availableActions ?? [];
};

const readGroupedActions = (wrapper) => {
  return wrapper.vm.groupedActions?.value ?? wrapper.vm.groupedActions ?? {};
};

vi.mock('@/Composables/permissions/usePermissions', () => ({
  usePermissions: () => mockPermissions,
}));

// Mock entity-actions-config
const mockActionsConfig = {
  view: {
    key: 'view',
    label: 'Afficher',
    tooltip: 'Afficher',
    icon: 'fa-solid fa-eye',
    permission: 'canView',
    requiresEntity: true,
    group: 'navigation',
    getLabel: (ctx) => (ctx?.inModal ? 'Agrandir' : 'Afficher'),
    getTooltip: (ctx) => (ctx?.inModal ? 'Agrandir' : 'Afficher'),
    getIcon: (ctx) => (ctx?.inModal ? 'fa-solid fa-expand' : 'fa-solid fa-eye'),
    visibleIf: (ctx) => {
      if (ctx?.inMinimal || ctx?.inLine || ctx?.viewMode === 'line' || ctx?.viewMode === 'minimal') return false;
      if (ctx?.inPage) return ctx?.pageMode === 'edit';
      return true;
    },
  },
  'quick-view': {
    key: 'quick-view',
    label: 'Afficher',
    tooltip: 'Afficher',
    icon: 'fa-solid fa-eye',
    permission: 'canView',
    requiresEntity: true,
    group: 'navigation',
    getLabel: () => 'Afficher',
    getTooltip: () => 'Afficher',
    visibleIf: (ctx) => !ctx?.inModal && !ctx?.inPage,
  },
  edit: {
    key: 'edit',
    label: 'Éditer',
    tooltip: 'Éditer',
    icon: 'fa-solid fa-pen-to-square',
    permission: 'canUpdate',
    requiresEntity: true,
    group: 'edition',
    getLabel: (ctx) => (ctx?.inModal && ctx?.modalMode === 'edit' ? 'Agrandir' : 'Éditer'),
    getTooltip: (ctx) => (ctx?.inModal && ctx?.modalMode === 'edit' ? 'Agrandir' : 'Éditer'),
    getIcon: (ctx) => (ctx?.inModal && ctx?.modalMode === 'edit' ? 'fa-solid fa-expand' : 'fa-solid fa-pen-to-square'),
    visibleIf: (ctx) => {
      if (ctx?.inPage) return ctx?.pageMode !== 'edit';
      return true;
    },
  },
  'quick-edit': {
    key: 'quick-edit',
    label: 'Éditer',
    tooltip: 'Éditer',
    icon: 'fa-solid fa-pen-to-square',
    permission: 'canUpdate',
    requiresEntity: true,
    group: 'edition',
    getLabel: () => 'Éditer',
    getTooltip: () => 'Éditer',
    visibleIf: () => false,
  },
  'copy-link': {
    key: 'copy-link',
    label: 'Copier le lien',
    icon: 'fa-solid fa-link',
    permission: null,
    requiresEntity: true,
    group: 'tools',
  },
  favorite: {
    key: 'favorite',
    label: 'Ajouter aux favoris',
    icon: 'fa-regular fa-star',
    permission: null,
    requiresEntity: true,
    group: 'tools',
  },
  state: {
    key: 'state',
    label: 'État',
    icon: 'fa-solid fa-circle',
    permission: null,
    requiresEntity: true,
    group: 'status',
    visibleIf: (_ctx, entity) => Object.prototype.hasOwnProperty.call(entity || {}, 'state'),
  },
  pin: {
    key: 'pin',
    label: 'Épingler',
    icon: 'fa-solid fa-thumbtack',
    permission: null,
    requiresEntity: true,
    group: 'tools',
  },
  'download-pdf': {
    key: 'download-pdf',
    label: 'Télécharger PDF',
    icon: 'fa-solid fa-file-pdf',
    permission: null,
    requiresEntity: true,
    group: 'tools',
  },
  refresh: {
    key: 'refresh',
    label: 'Rafraîchir',
    icon: 'fa-solid fa-arrow-rotate-right',
    permission: 'canManage',
    requiresEntity: true,
    group: 'tools',
    visibleIf: (ctx) => Boolean(ctx?.inModal || ctx?.inPage),
  },
  minimize: {
    key: 'minimize',
    label: 'Minimiser',
    icon: 'fa-solid fa-window-minimize',
    permission: null,
    requiresEntity: false,
    group: 'tools',
  },
  delete: {
    key: 'delete',
    label: 'Supprimer',
    icon: 'fa-solid fa-trash',
    permission: 'canDelete',
    requiresEntity: true,
    variant: 'error',
    group: 'destructive',
  },
};

vi.mock('@/Entities/entity-actions-config', () => ({
  getActionsForEntityType: vi.fn(() => mockActionsConfig),
  ACTION_GROUPS_ORDER: ['status', 'navigation', 'edition', 'tools', 'destructive'],
  ENTITY_ACTION_CONTEXT_PRESETS: {
    minimalLine: ['state', 'pin', 'favorite', 'copy-link', 'quick-view', 'edit'],
    modalDetail: ['state', 'favorite', 'copy-link', 'view', 'edit', 'refresh', 'delete'],
    pageDetail: ['state', 'favorite', 'copy-link', 'view', 'edit', 'refresh', 'delete'],
    tableDropdown: ['state', 'pin', 'favorite', 'copy-link', 'quick-view', 'edit'],
  },
  normalizeActionEntityType: (entityType) => entityType,
  isScrappableEntityType: (entityType) => ['spells', 'items'].includes(entityType),
}));

describe('useEntityActions', () => {
  beforeEach(() => {
    vi.clearAllMocks();
    // Reset permissions par défaut
    mockPermissions.canViewAny.mockReturnValue(true);
    mockPermissions.canUpdateAny.mockReturnValue(true);
    mockPermissions.canDeleteAny.mockReturnValue(true);
    mockPermissions.canManageAny.mockReturnValue(false);
    mockPermissions.isAdmin.value = false;
    mockPermissions.can.mockImplementation((entityType, ability) => {
      if (ability === 'manageAny') return false;
      return true;
    });
  });

  describe('Filtrage par permissions', () => {
    it('filtre les actions selon les permissions', () => {
      const TestComponent = defineComponent({
        setup() {
          const { availableActions } = useEntityActions('spells', { id: 1 }, {
            context: { inModal: true },
          });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      // Toutes les actions devraient être disponibles (permissions par défaut)
      const actions = readAvailableActions(wrapper);
      expect(actions.length).toBeGreaterThan(0);
      expect(actions.some((a) => a.key === 'view')).toBe(true);
      expect(actions.some((a) => a.key === 'edit')).toBe(true);
    });

    it('exclut les actions sans permission canView', () => {
      mockPermissions.canViewAny.mockReturnValue(false);

      const TestComponent = defineComponent({
        setup() {
          const { availableActions } = useEntityActions('spells', { id: 1 }, {
            context: { inModal: true },
          });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const actionKeys = readAvailableActions(wrapper).map((a) => a.key);
      expect(actionKeys).not.toContain('view');
      expect(actionKeys).not.toContain('quick-view');
    });

    it('exclut les actions sans permission canUpdate', () => {
      mockPermissions.canUpdateAny.mockReturnValue(false);

      const TestComponent = defineComponent({
        setup() {
          const { availableActions } = useEntityActions('spells', { id: 1 }, {
            context: { inModal: true },
          });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const actionKeys = readAvailableActions(wrapper).map((a) => a.key);
      expect(actionKeys).not.toContain('edit');
      expect(actionKeys).not.toContain('quick-edit');
    });

    it('exclut les actions sans permission canDelete', () => {
      mockPermissions.canDeleteAny.mockReturnValue(false);

      const TestComponent = defineComponent({
        setup() {
          const { availableActions } = useEntityActions('spells', { id: 1 }, {
            context: { inModal: true },
          });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const actionKeys = readAvailableActions(wrapper).map((a) => a.key);
      expect(actionKeys).not.toContain('delete');
    });

    it('exclut les actions sans permission canManage', () => {
      mockPermissions.canManageAny.mockReturnValue(false);
      mockPermissions.canUpdateAny.mockReturnValue(false);
      mockPermissions.can.mockImplementation((entityType, ability) => {
        if (ability === 'manageAny') return false;
        return true;
      });
      mockPermissions.isAdmin.value = false;

      const TestComponent = defineComponent({
        setup() {
          const { availableActions } = useEntityActions('spells', { id: 1 }, {
            context: { inModal: true },
          });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const actionKeys = readAvailableActions(wrapper).map((a) => a.key);
      expect(actionKeys).not.toContain('refresh');
    });
  });

  describe('Filtrage whitelist/blacklist', () => {
    it('filtre avec whitelist', () => {
      const TestComponent = defineComponent({
        setup() {
          const { availableActions } = useEntityActions('spells', { id: 1 }, {
            whitelist: ['view', 'edit'],
            context: { inModal: true, modalMode: 'view' },
          });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const actionKeys = readAvailableActions(wrapper).map((a) => a.key);
      expect(actionKeys).toContain('view');
      expect(actionKeys).toContain('edit');
      expect(actionKeys).not.toContain('delete');
      expect(actionKeys).not.toContain('copy-link');
    });

    it('filtre avec blacklist', () => {
      const TestComponent = defineComponent({
        setup() {
          const { availableActions } = useEntityActions('spells', { id: 1 }, {
            blacklist: ['delete', 'refresh'],
            context: { inModal: true },
          });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const actionKeys = readAvailableActions(wrapper).map((a) => a.key);
      expect(actionKeys).not.toContain('delete');
      expect(actionKeys).not.toContain('refresh');
      expect(actionKeys).toContain('view');
      expect(actionKeys).toContain('edit');
    });
  });

  describe('Contexte inPanel', () => {
    it('exclut minimize si inPanel est false', () => {
      const TestComponent = defineComponent({
        setup() {
          const { availableActions } = useEntityActions('spells', { id: 1 }, {
            context: { inPanel: false },
          });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const actionKeys = readAvailableActions(wrapper).map((a) => a.key);
      expect(actionKeys).not.toContain('minimize');
    });

    it('inclut minimize si inPanel est true', () => {
      const TestComponent = defineComponent({
        setup() {
          const { availableActions } = useEntityActions('spells', { id: 1 }, {
            whitelist: ['minimize'],
            context: { inPanel: true },
          });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const actionKeys = readAvailableActions(wrapper).map((a) => a.key);
      expect(actionKeys).toContain('minimize');
    });
  });

  describe('Matrice de contexte', () => {
    it('utilise le preset minimal/line pour les vues compactes', () => {
      const TestComponent = defineComponent({
        setup() {
          const { availableActions } = useEntityActions('spells', { id: 1 }, {
            context: { viewMode: 'line' },
          });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);
      const actionKeys = readAvailableActions(wrapper).map((a) => a.key);
      expect(actionKeys).toEqual(['pin', 'favorite', 'copy-link', 'quick-view', 'edit']);
    });

    it('expose les intentions modales et page', () => {
      const TestComponent = defineComponent({
        setup() {
          const { availableActions } = useEntityActions('spells', { id: 1 }, {
            context: { inModal: true },
          });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);
      const byKey = Object.fromEntries(readAvailableActions(wrapper).map((a) => [a.key, a.intent]));
      expect(byKey.view).toBe('open-page');
      expect(byKey.edit).toBe('edit-page');
      expect(byKey.favorite).toBe('favorite');
    });

    it('harmonise les icônes et libellés afficher/éditer selon le contexte', () => {
      const TestComponent = defineComponent({
        setup() {
          const minimal = useEntityActions('spells', { id: 1 }, { context: { viewMode: 'minimal' } }).availableActions;
          const modalView = useEntityActions('spells', { id: 1 }, { context: { inModal: true, modalMode: 'view' } }).availableActions;
          const modalEdit = useEntityActions('spells', { id: 1 }, { context: { inModal: true, modalMode: 'edit' } }).availableActions;
          const pageView = useEntityActions('spells', { id: 1 }, { context: { inPage: true, pageMode: 'view' } }).availableActions;
          const pageEdit = useEntityActions('spells', { id: 1 }, { context: { inPage: true, pageMode: 'edit' } }).availableActions;
          return { minimal, modalView, modalEdit, pageView, pageEdit };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);
      const getAction = (bucket, key) => {
        const actions = wrapper.vm[bucket]?.value ?? wrapper.vm[bucket];
        return actions.find((a) => a.key === key);
      };

      expect(getAction('minimal', 'quick-view')).toMatchObject({
        label: 'Afficher',
        tooltip: 'Afficher',
        icon: 'fa-solid fa-eye',
        intent: 'open-modal',
      });
      expect(getAction('minimal', 'edit')).toMatchObject({
        label: 'Éditer',
        tooltip: 'Éditer',
        icon: 'fa-solid fa-pen-to-square',
        intent: 'edit-page',
      });
      expect(getAction('modalView', 'view')).toMatchObject({
        label: 'Agrandir',
        tooltip: 'Agrandir',
        icon: 'fa-solid fa-expand',
        intent: 'open-page',
      });
      expect(getAction('modalView', 'edit')).toMatchObject({
        label: 'Éditer',
        tooltip: 'Éditer',
        icon: 'fa-solid fa-pen-to-square',
        intent: 'edit-page',
      });
      expect(getAction('modalEdit', 'edit')).toMatchObject({
        label: 'Agrandir',
        tooltip: 'Agrandir',
        icon: 'fa-solid fa-expand',
        intent: 'edit-page',
      });
      expect(getAction('modalEdit', 'quick-edit')).toBeUndefined();
      expect(getAction('pageView', 'edit')).toMatchObject({
        label: 'Éditer',
        tooltip: 'Éditer',
        icon: 'fa-solid fa-pen-to-square',
        intent: 'edit-page',
      });
      expect(getAction('pageEdit', 'view')).toMatchObject({
        label: 'Afficher',
        tooltip: 'Afficher',
        icon: 'fa-solid fa-eye',
        intent: 'open-page',
      });
      expect(getAction('pageEdit', 'edit')).toBeUndefined();
    });

    it('ajoute l\'action état si l\'entité expose un state', () => {
      const TestComponent = defineComponent({
        setup() {
          const { availableActions } = useEntityActions('spells', { id: 1, state: 'draft' }, {
            context: { viewMode: 'line', inPage: true },
          });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);
      const stateAction = readAvailableActions(wrapper).find((a) => a.key === 'state');
      expect(stateAction).toBeTruthy();
      expect(stateAction.intent).toBe('state');
      expect(stateAction.stateValue).toBe('draft');
      expect(stateAction.canUpdateState).toBe(true);
      expect(stateAction.showStateLabel).toBe(false);
    });

    it('affiche le label état dans les contextes full, modal et page', () => {
      const TestComponent = defineComponent({
        setup() {
          const full = useEntityActions('spells', { id: 1, state: 'draft' }, { context: { viewMode: 'full' } }).availableActions;
          const modal = useEntityActions('spells', { id: 1, state: 'draft' }, { context: { inModal: true } }).availableActions;
          const page = useEntityActions('spells', { id: 1, state: 'draft' }, { context: { inPage: true } }).availableActions;
          return { full, modal, page };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);
      for (const key of ['full', 'modal', 'page']) {
        const actions = wrapper.vm[key]?.value ?? wrapper.vm[key];
        expect(actions.find((a) => a.key === 'state')?.showStateLabel).toBe(true);
      }
    });

    it('masque l\'action état si l\'entité n\'expose pas de state', () => {
      const TestComponent = defineComponent({
        setup() {
          const { availableActions } = useEntityActions('spells', { id: 1 }, {
            context: { viewMode: 'line' },
          });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);
      expect(readAvailableActions(wrapper).map((a) => a.key)).not.toContain('state');
    });
  });

  describe('Rafraîchissement scrapping', () => {
    it('n\'affiche refresh que pour une entité scrappable avec droit manage/admin', () => {
      mockPermissions.can.mockImplementation((entityType, ability) => ability === 'manageAny');

      const TestComponent = defineComponent({
        setup() {
          const spells = useEntityActions('spells', { id: 1 }, { context: { inModal: true } }).availableActions;
          const conditions = useEntityActions('conditions', { id: 1 }, { context: { inModal: true } }).availableActions;
          return { spells, conditions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);
      expect((wrapper.vm.spells?.value ?? wrapper.vm.spells).map((a) => a.key)).toContain('refresh');
      expect((wrapper.vm.conditions?.value ?? wrapper.vm.conditions).map((a) => a.key)).not.toContain('refresh');
    });
  });

  describe('Groupement des actions', () => {
    it('groupe les actions correctement', () => {
      const TestComponent = defineComponent({
        setup() {
          const { groupedActions } = useEntityActions('spells', { id: 1 }, {
            context: { inModal: true },
          });
          return { groupedActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const groups = readGroupedActions(wrapper);
      expect(groups).toHaveProperty('navigation');
      expect(groups).toHaveProperty('edition');
      expect(groups).toHaveProperty('tools');
      expect(groups).toHaveProperty('destructive');
    });

    it('respecte l\'ordre des groupes', () => {
      const TestComponent = defineComponent({
        setup() {
          const { groupedActions } = useEntityActions('spells', { id: 1 }, {
            context: { inModal: true },
          });
          return { groupedActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const groupKeys = Object.keys(readGroupedActions(wrapper));
      expect(groupKeys[0]).toBe('navigation');
      expect(groupKeys[1]).toBe('edition');
      expect(groupKeys[2]).toBe('tools');
      expect(groupKeys[3]).toBe('destructive');
    });
  });

  describe('Actions nécessitant une entité', () => {
    it('exclut les actions nécessitant une entité si entity est null', () => {
      const TestComponent = defineComponent({
        setup() {
          const { availableActions } = useEntityActions('spells', null, {
            whitelist: ['minimize'],
            context: { inPanel: true },
          });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const actionKeys = readAvailableActions(wrapper).map((a) => a.key);
      // Les actions nécessitant une entité ne devraient pas être présentes
      expect(actionKeys).not.toContain('view');
      expect(actionKeys).not.toContain('edit');
      expect(actionKeys).not.toContain('delete');
      // Minimize ne nécessite pas d'entité
      expect(actionKeys).toContain('minimize');
    });
  });
});

