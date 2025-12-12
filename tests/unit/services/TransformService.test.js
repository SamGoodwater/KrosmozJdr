/**
 * Tests unitaires pour TransformService
 * 
 * @description
 * Tests pour toutes les transformations communes : slug, normalisation, enums, pivots, etc.
 */
import { describe, it, expect } from 'vitest';
import { TransformService } from '@/Utils/Services/TransformService';

describe('TransformService', () => {
  describe('generateSlugFromTitle', () => {
    it('devrait générer un slug depuis un titre simple', () => {
      const slug = TransformService.generateSlugFromTitle('Mon Super Titre');
      expect(slug).toBe('mon-super-titre');
    });

    it('devrait gérer les accents', () => {
      const slug = TransformService.generateSlugFromTitle('Café & Éléments');
      expect(slug).toBe('cafe-elements');
    });

    it('devrait gérer les caractères spéciaux', () => {
      const slug = TransformService.generateSlugFromTitle('Test !@#$%^&*()');
      expect(slug).toBe('test');
    });

    it('devrait gérer les espaces multiples', () => {
      const slug = TransformService.generateSlugFromTitle('Titre   avec   espaces');
      expect(slug).toBe('titre-avec-espaces');
    });

    it('devrait enlever les séparateurs en début et fin', () => {
      const slug = TransformService.generateSlugFromTitle('---Titre---');
      expect(slug).toBe('titre');
    });

    it('devrait retourner une chaîne vide si le titre est vide', () => {
      const slug = TransformService.generateSlugFromTitle('');
      expect(slug).toBe('');
    });

    it('devrait retourner une chaîne vide si le titre est null', () => {
      const slug = TransformService.generateSlugFromTitle(null);
      expect(slug).toBe('');
    });

    it('devrait respecter maxLength si fourni', () => {
      const slug = TransformService.generateSlugFromTitle('Un très long titre qui dépasse', { maxLength: 10 });
      expect(slug.length).toBeLessThanOrEqual(10);
    });

    it('devrait utiliser un séparateur personnalisé', () => {
      const slug = TransformService.generateSlugFromTitle('Mon Titre', { separator: '_' });
      expect(slug).toBe('mon_titre');
    });
  });

  describe('generateSlug', () => {
    it('devrait générer un slug depuis un titre', () => {
      const slug = TransformService.generateSlug('Mon Titre', 123);
      expect(slug).toBe('mon-titre');
    });

    it('devrait générer un slug depuis un ID si pas de titre', () => {
      const slug = TransformService.generateSlug(null, 123);
      expect(slug).toMatch(/^item-123-/);
      expect(slug.length).toBeGreaterThan(10); // Avec élément aléatoire
    });

    it('devrait utiliser un préfixe personnalisé', () => {
      const slug = TransformService.generateSlug(null, 123, { prefix: 'section' });
      expect(slug).toMatch(/^section-123-/);
    });

    it('devrait pouvoir désactiver l\'élément aléatoire', () => {
      const slug = TransformService.generateSlug(null, 123, { addRandom: false });
      expect(slug).toBe('item-123');
    });

    it('devrait retourner une chaîne vide si ni titre ni ID', () => {
      const slug = TransformService.generateSlug(null, null);
      expect(slug).toBe('');
    });
  });

  describe('generateRandomString', () => {
    it('devrait générer une chaîne aléatoire de la longueur demandée', () => {
      const random = TransformService.generateRandomString(10);
      expect(random).toHaveLength(10);
    });

    it('devrait générer des chaînes différentes à chaque appel', () => {
      const random1 = TransformService.generateRandomString(20);
      const random2 = TransformService.generateRandomString(20);
      // Probabilité très faible d'avoir la même chaîne
      expect(random1).not.toBe(random2);
    });

    it('devrait utiliser un charset personnalisé', () => {
      const random = TransformService.generateRandomString(10, 'abc');
      expect(random).toMatch(/^[abc]{10}$/);
    });
  });

  describe('normalizeText', () => {
    it('devrait normaliser un texte simple', () => {
      const normalized = TransformService.normalizeText('  Mon Texte  ');
      expect(normalized).toBe('mon texte');
    });

    it('devrait enlever les accents', () => {
      const normalized = TransformService.normalizeText('Café Éléments');
      expect(normalized).toBe('cafe elements');
    });

    it('devrait pouvoir désactiver la mise en minuscules', () => {
      const normalized = TransformService.normalizeText('Mon Texte', { lowercase: false });
      expect(normalized).toBe('Mon Texte');
    });

    it('devrait pouvoir désactiver l\'enlèvement des accents', () => {
      const normalized = TransformService.normalizeText('Café', { removeAccents: false });
      expect(normalized).toBe('café');
    });

    it('devrait pouvoir désactiver le trim', () => {
      const normalized = TransformService.normalizeText('  Texte  ', { trim: false });
      expect(normalized).toBe('  texte  ');
    });
  });

  describe('toEnum', () => {
    it('devrait retourner la valeur par défaut si null', () => {
      const result = TransformService.toEnum(null, null, 'default');
      expect(result).toBe('default');
    });

    it('devrait retourner la valeur par défaut si undefined', () => {
      const result = TransformService.toEnum(undefined, null, 'default');
      expect(result).toBe('default');
    });

    it('devrait retourner la valeur telle quelle si pas d\'enum', () => {
      const result = TransformService.toEnum('value', null, 'default');
      expect(result).toBe('value');
    });

    it('devrait utiliser un objet de mapping', () => {
      const mapping = { 'draft': 'brouillon', 'published': 'publié' };
      const result = TransformService.toEnum('draft', mapping, 'inconnu');
      expect(result).toBe('brouillon');
    });
  });

  describe('fromEnum', () => {
    it('devrait extraire la valeur d\'un objet avec .value', () => {
      const enumValue = { value: 'draft' };
      const result = TransformService.fromEnum(enumValue);
      expect(result).toBe('draft');
    });

    it('devrait retourner une chaîne telle quelle', () => {
      const result = TransformService.fromEnum('draft');
      expect(result).toBe('draft');
    });

    it('devrait retourner un nombre tel quel', () => {
      const result = TransformService.fromEnum(123);
      expect(result).toBe(123);
    });

    it('devrait retourner null si null', () => {
      const result = TransformService.fromEnum(null);
      expect(result).toBeNull();
    });
  });

  describe('normalizePivot', () => {
    it('devrait extraire les IDs d\'un tableau d\'objets', () => {
      const pivotData = [
        { id: 1, pivot: { role: 'admin' } },
        { id: 2, pivot: { role: 'user' } },
      ];
      const result = TransformService.normalizePivot(pivotData, { extractIds: true });
      expect(result).toEqual([1, 2]);
    });

    it('devrait garder les données pivot si extractIds est false', () => {
      const pivotData = [
        { id: 1, pivot: { role: 'admin' } },
        { id: 2, pivot: { role: 'user' } },
      ];
      const result = TransformService.normalizePivot(pivotData, { extractIds: false });
      expect(result).toEqual([
        { id: 1, role: 'admin' },
        { id: 2, role: 'user' },
      ]);
    });

    it('devrait gérer un tableau d\'IDs simples', () => {
      const pivotData = [1, 2, 3];
      const result = TransformService.normalizePivot(pivotData);
      expect(result).toEqual([1, 2, 3]);
    });

    it('devrait retourner un tableau vide si null', () => {
      const result = TransformService.normalizePivot(null);
      expect(result).toEqual([]);
    });

    it('devrait utiliser une clé personnalisée pour l\'ID', () => {
      const pivotData = [
        { user_id: 1, pivot: { role: 'admin' } },
        { user_id: 2, pivot: { role: 'user' } },
      ];
      const result = TransformService.normalizePivot(pivotData, { idKey: 'user_id', extractIds: true });
      expect(result).toEqual([1, 2]);
    });
  });

  describe('formatDate', () => {
    it('devrait formater une date au format DD/MM/YYYY', () => {
      const date = new Date('2025-01-15');
      const formatted = TransformService.formatDate(date);
      expect(formatted).toBe('15/01/2025');
    });

    it('devrait formater une date string', () => {
      const formatted = TransformService.formatDate('2025-01-15');
      expect(formatted).toBe('15/01/2025');
    });

    it('devrait retourner une chaîne vide si date invalide', () => {
      const formatted = TransformService.formatDate('invalid');
      expect(formatted).toBe('');
    });

    it('devrait retourner une chaîne vide si null', () => {
      const formatted = TransformService.formatDate(null);
      expect(formatted).toBe('');
    });
  });
});

