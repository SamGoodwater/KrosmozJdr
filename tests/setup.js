/**
 * Setup file pour Vitest
 * 
 * @description
 * Configuration globale pour les tests :
 * - Mocks globaux
 * - Helpers de test
 * - Configuration de l'environnement
 */
import { vi } from 'vitest';
import { config } from '@vue/test-utils';

// Polyfill pour URL si nécessaire
if (typeof URL === 'undefined') {
  global.URL = class URL {
    constructor(url, base) {
      this.href = url;
      this.origin = base || '';
      this.pathname = url;
    }
  };
}

// Mock de route() (Ziggy)
global.route = vi.fn((name, params) => {
  if (typeof params === 'string' || typeof params === 'number') {
    return `/route/${name}/${params}`;
  }
  if (params && typeof params === 'object') {
    const query = new URLSearchParams(params).toString();
    return `/route/${name}${query ? '?' + query : ''}`;
  }
  return `/route/${name}`;
});

// Mock de window.location
Object.defineProperty(window, 'location', {
  value: {
    origin: 'http://localhost:5173',
    href: 'http://localhost:5173',
    pathname: '/',
  },
  writable: true,
});

// Mock de Inertia router
global.router = {
  post: vi.fn(),
  patch: vi.fn(),
  delete: vi.fn(),
  get: vi.fn(),
  reload: vi.fn(),
};

// Configuration Vue Test Utils
config.global.mocks = {
  $route: {
    params: {},
    query: {},
  },
  $router: global.router,
  route: global.route,
};

// Helper pour créer des sections mockées
export function createMockSection(overrides = {}) {
  return {
    id: 1,
    page_id: 1,
    title: 'Test Section',
    slug: 'test-section',
    order: 0,
    template: 'text',
    settings: { align: 'left', size: 'md' },
    data: { content: 'Test content' },
    is_visible: 'guest',
    can_edit_role: 'admin',
    state: 'published',
    created_by: 1,
    created_at: '2025-01-01T00:00:00.000000Z',
    updated_at: '2025-01-01T00:00:00.000000Z',
    can: {
      update: true,
      delete: true,
      forceDelete: true,
      restore: true,
    },
    page: {
      id: 1,
      title: 'Test Page',
      slug: 'test-page',
    },
    ...overrides,
  };
}

// Helper pour créer des pages mockées
export function createMockPage(overrides = {}) {
  return {
    id: 1,
    title: 'Test Page',
    slug: 'test-page',
    is_visible: 'guest',
    can_edit_role: 'admin',
    in_menu: true,
    state: 'published',
    parent_id: null,
    menu_order: 0,
    created_by: 1,
    created_at: '2025-01-01T00:00:00.000000Z',
    updated_at: '2025-01-01T00:00:00.000000Z',
    can: {
      update: true,
      delete: true,
      forceDelete: true,
      restore: true,
    },
    sections: [],
    ...overrides,
  };
}

