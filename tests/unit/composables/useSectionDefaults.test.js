/**
 * Tests unitaires pour useSectionDefaults
 */
import { describe, it, expect } from 'vitest';
import { useSectionDefaults } from '@/Pages/Organismes/section/composables/useSectionDefaults';

describe('useSectionDefaults', () => {
  const { getDefaults, getDefaultSettings, getDefaultData } = useSectionDefaults();

  describe('getDefaults', () => {
    it('devrait retourner les valeurs par défaut pour text', () => {
      const defaults = getDefaults('text');
      expect(defaults).toEqual({
        settings: { align: 'left', size: 'md' },
        data: { content: '' },
      });
    });

    it('devrait retourner les valeurs par défaut pour image', () => {
      const defaults = getDefaults('image');
      expect(defaults).toEqual({
        settings: { align: 'center', size: 'md' },
        data: { src: '', alt: '', caption: '' },
      });
    });

    it('devrait retourner les valeurs par défaut pour gallery', () => {
      const defaults = getDefaults('gallery');
      expect(defaults).toEqual({
        settings: { columns: 3, gap: 'md' },
        data: { images: [] },
      });
    });

    it('devrait retourner les valeurs par défaut pour video', () => {
      const defaults = getDefaults('video');
      expect(defaults).toEqual({
        settings: { autoplay: false, controls: true },
        data: { src: '', type: 'youtube' },
      });
    });

    it('devrait retourner les valeurs par défaut pour entity_table', () => {
      const defaults = getDefaults('entity_table');
      expect(defaults).toEqual({
        settings: {},
        data: { entity: '', filters: {}, columns: [] },
      });
    });

    it('devrait retourner des valeurs vides pour un template inconnu', () => {
      const defaults = getDefaults('unknown');
      expect(defaults).toEqual({
        settings: {},
        data: {},
      });
    });
  });

  describe('getDefaultSettings', () => {
    it('devrait retourner uniquement les settings pour text', () => {
      const settings = getDefaultSettings('text');
      expect(settings).toEqual({ align: 'left', size: 'md' });
    });
  });

  describe('getDefaultData', () => {
    it('devrait retourner uniquement les data pour text', () => {
      const data = getDefaultData('text');
      expect(data).toEqual({ content: '' });
    });
  });
});

