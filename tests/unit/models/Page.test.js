/**
 * Tests unitaires pour le modèle Page
 */
import { describe, it, expect } from 'vitest';
import { Page } from '@/Models';
import { createMockPage } from '../../setup.js';

describe('Page Model', () => {
  describe('Constructor', () => {
    it('devrait créer une instance Page à partir de données brutes', () => {
      const rawData = createMockPage();
      const page = new Page(rawData);

      expect(page).toBeInstanceOf(Page);
      expect(page.id).toBe(1);
      expect(page.title).toBe('Test Page');
    });

    it('devrait gérer les données dans .data (Resource)', () => {
      const rawData = {
        data: createMockPage(),
      };
      const page = new Page(rawData);

      expect(page.id).toBe(1);
    });
  });

  describe('Propriétés', () => {
    it('devrait exposer toutes les propriétés correctement', () => {
      const rawData = createMockPage({
        id: 123,
        title: 'My Page',
        slug: 'my-page',
        state: 'playable',
      });
      const page = new Page(rawData);

      expect(page.id).toBe(123);
      expect(page.title).toBe('My Page');
      expect(page.slug).toBe('my-page');
      expect(page.state).toBe('playable');
    });

    it('devrait gérer les sections', () => {
      const { createMockSection } = require('../../setup.js');
      const rawData = createMockPage({
        sections: [
          createMockSection({ id: 1 }),
          createMockSection({ id: 2 }),
        ],
      });
      const page = new Page(rawData);

      expect(page.sections).toHaveLength(2);
      expect(page.sections[0].id).toBe(1);
      expect(page.sections[1].id).toBe(2);
    });
  });

  describe('Méthodes', () => {
    it('devrait vérifier si la page est jouable', () => {
      const playablePage = new Page(createMockPage({ state: 'playable' }));
      const draftPage = new Page(createMockPage({ state: 'draft' }));

      expect(playablePage.isPlayable).toBe(true);
      expect(draftPage.isPlayable).toBe(false);
    });

    it('devrait vérifier si la page est dans le menu', () => {
      const menuPage = new Page(createMockPage({ in_menu: true }));
      const noMenuPage = new Page(createMockPage({ in_menu: false }));

      expect(menuPage.inMenu).toBe(true);
      expect(noMenuPage.inMenu).toBe(false);
    });

    it('devrait générer un slug depuis le titre', () => {
      const page = new Page(createMockPage({ title: 'Ma Page', id: 456 }));
      const slug = page.generateSlug();
      expect(slug).toBe('ma-page');
    });

    it('devrait générer un slug depuis l\'ID si pas de titre', () => {
      const page = new Page(createMockPage({ title: null, id: 456 }));
      const slug = page.generateSlug();
      expect(slug).toMatch(/^page-456-/);
    });

    it('devrait retourner le slug existant ou en générer un', () => {
      const pageWithSlug = new Page(createMockPage({ slug: 'existing-slug', id: 456 }));
      expect(pageWithSlug.getSlugOrGenerate()).toBe('existing-slug');

      const pageWithoutSlug = new Page(createMockPage({ slug: null, title: 'Test', id: 456 }));
      expect(pageWithoutSlug.getSlugOrGenerate()).toBe('test');
    });
  });
});

