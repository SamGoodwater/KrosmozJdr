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
};

vi.mock('@/Composables/permissions/usePermissions', () => ({
  usePermissions: () => mockPermissions,
}));

// Mock entity-actions-config
const mockActionsConfig = {
  view: {
    key: 'view',
    label: 'Ouvrir (page)',
    icon: 'fa-solid fa-eye',
    permission: 'canView',
    requiresEntity: true,
    group: 'navigation',
  },
  'quick-view': {
    key: 'quick-view',
    label: 'Ouvrir rapide',
    icon: 'fa-solid fa-window-maximize',
    permission: 'canView',
    requiresEntity: true,
    group: 'navigation',
  },
  edit: {
    key: 'edit',
    label: 'Modifier (page)',
    icon: 'fa-solid fa-pen',
    permission: 'canUpdate',
    requiresEntity: true,
    group: 'edition',
  },
  'quick-edit': {
    key: 'quick-edit',
    label: 'Modifier rapide',
    icon: 'fa-solid fa-bolt',
    permission: 'canUpdate',
    requiresEntity: true,
    group: 'edition',
  },
  'copy-link': {
    key: 'copy-link',
    label: 'Copier le lien',
    icon: 'fa-solid fa-link',
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
  getActionsForEntityType: vi.fn((entityType) => mockActionsConfig),
  ACTION_GROUPS_ORDER: ['navigation', 'edition', 'tools', 'destructive'],
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
          const { availableActions } = useEntityActions('spells', { id: 1 });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      // Toutes les actions devraient être disponibles (permissions par défaut)
      expect(wrapper.vm.availableActions.value.length).toBeGreaterThan(0);
      expect(wrapper.vm.availableActions.value.some((a) => a.key === 'view')).toBe(true);
      expect(wrapper.vm.availableActions.value.some((a) => a.key === 'edit')).toBe(true);
    });

    it('exclut les actions sans permission canView', () => {
      mockPermissions.canViewAny.mockReturnValue(false);

      const TestComponent = defineComponent({
        setup() {
          const { availableActions } = useEntityActions('spells', { id: 1 });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const actionKeys = wrapper.vm.availableActions.value.map((a) => a.key);
      expect(actionKeys).not.toContain('view');
      expect(actionKeys).not.toContain('quick-view');
    });

    it('exclut les actions sans permission canUpdate', () => {
      mockPermissions.canUpdateAny.mockReturnValue(false);

      const TestComponent = defineComponent({
        setup() {
          const { availableActions } = useEntityActions('spells', { id: 1 });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const actionKeys = wrapper.vm.availableActions.value.map((a) => a.key);
      expect(actionKeys).not.toContain('edit');
      expect(actionKeys).not.toContain('quick-edit');
    });

    it('exclut les actions sans permission canDelete', () => {
      mockPermissions.canDeleteAny.mockReturnValue(false);

      const TestComponent = defineComponent({
        setup() {
          const { availableActions } = useEntityActions('spells', { id: 1 });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const actionKeys = wrapper.vm.availableActions.value.map((a) => a.key);
      expect(actionKeys).not.toContain('delete');
    });

    it('exclut les actions sans permission canManage', () => {
      mockPermissions.canManageAny.mockReturnValue(false);
      mockPermissions.can.mockImplementation((entityType, ability) => {
        if (ability === 'manageAny') return false;
        return true;
      });
      mockPermissions.isAdmin.value = false;

      const TestComponent = defineComponent({
        setup() {
          const { availableActions } = useEntityActions('spells', { id: 1 });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const actionKeys = wrapper.vm.availableActions.value.map((a) => a.key);
      expect(actionKeys).not.toContain('refresh');
    });
  });

  describe('Filtrage whitelist/blacklist', () => {
    it('filtre avec whitelist', () => {
      const TestComponent = defineComponent({
        setup() {
          const { availableActions } = useEntityActions('spells', { id: 1 }, {
            whitelist: ['view', 'edit'],
          });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const actionKeys = wrapper.vm.availableActions.value.map((a) => a.key);
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
          });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const actionKeys = wrapper.vm.availableActions.value.map((a) => a.key);
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

      const actionKeys = wrapper.vm.availableActions.value.map((a) => a.key);
      expect(actionKeys).not.toContain('minimize');
    });

    it('inclut minimize si inPanel est true', () => {
      const TestComponent = defineComponent({
        setup() {
          const { availableActions } = useEntityActions('spells', { id: 1 }, {
            context: { inPanel: true },
          });
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const actionKeys = wrapper.vm.availableActions.value.map((a) => a.key);
      expect(actionKeys).toContain('minimize');
    });
  });

  describe('Groupement des actions', () => {
    it('groupe les actions correctement', () => {
      const TestComponent = defineComponent({
        setup() {
          const { groupedActions } = useEntityActions('spells', { id: 1 });
          return { groupedActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const groups = wrapper.vm.groupedActions.value;
      expect(groups).toHaveProperty('navigation');
      expect(groups).toHaveProperty('edition');
      expect(groups).toHaveProperty('tools');
      expect(groups).toHaveProperty('destructive');
    });

    it('respecte l\'ordre des groupes', () => {
      const TestComponent = defineComponent({
        setup() {
          const { groupedActions } = useEntityActions('spells', { id: 1 });
          return { groupedActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const groupKeys = Object.keys(wrapper.vm.groupedActions.value);
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
          const { availableActions } = useEntityActions('spells', null);
          return { availableActions };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const actionKeys = wrapper.vm.availableActions.value.map((a) => a.key);
      // Les actions nécessitant une entité ne devraient pas être présentes
      expect(actionKeys).not.toContain('view');
      expect(actionKeys).not.toContain('edit');
      expect(actionKeys).not.toContain('delete');
      // Minimize ne nécessite pas d'entité
      expect(actionKeys).toContain('minimize');
    });
  });
});

