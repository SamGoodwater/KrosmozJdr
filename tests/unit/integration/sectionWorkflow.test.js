/**
 * Tests d'intégration pour le workflow complet de sections
 */
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import { defineComponent } from 'vue';
import { useSectionUI } from '@/Pages/Organismes/section/composables/useSectionUI';
import { useSectionDefaults } from '@/Pages/Organismes/section/composables/useSectionDefaults';
import { useSectionStyles } from '@/Pages/Organismes/section/composables/useSectionStyles';
import { mapToSectionModel } from '@/Pages/Organismes/section/mappers/sectionMapper';
import { adaptSectionToUI } from '@/Pages/Organismes/section/adapters/sectionUIAdapter';
import { createMockSection } from '../../setup.js';

describe('Section Workflow - Intégration', () => {
  describe('Flux complet : Entity → Model → UI', () => {
    it('devrait transformer une section brute en données UI complètes', () => {
      // 1. Données brutes (Entity)
      const rawSection = createMockSection({
        template: 'text',
        state: 'playable',
        data: { content: 'Hello World' },
      });

      // 2. Mapper vers Model
      const sectionModel = mapToSectionModel(rawSection);
      expect(sectionModel).toBeDefined();
      expect(sectionModel.template).toBe('text');

      // 3. Adapter vers UI
      const uiData = adaptSectionToUI(sectionModel);
      expect(uiData.color).toBe('success');
      expect(uiData.badge.text).toBe('Publié');
      expect(uiData.metadata.hasContent).toBe(true);
    });

    it('devrait utiliser useSectionUI pour un flux complet', () => {
      const rawSection = createMockSection({
        template: 'image',
        state: 'draft',
      });

      const TestComponent = defineComponent({
        setup() {
          const {
            sectionModel,
            uiData,
            canEdit,
            templateInfo,
            stateInfo,
            hasContent,
            url,
          } = useSectionUI(rawSection);

          return {
            sectionModel,
            uiData,
            canEdit,
            templateInfo,
            stateInfo,
            hasContent,
            url,
          };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      // Vérifier que tout est accessible
      expect(wrapper.vm.sectionModel).toBeDefined();
      expect(wrapper.vm.uiData.color).toBe('warning');
      expect(wrapper.vm.canEdit).toBe(true);
      expect(wrapper.vm.templateInfo.value).toBe('image');
      expect(wrapper.vm.stateInfo.value).toBe('draft');
      expect(wrapper.vm.url).toBeDefined();
    });
  });

  describe('Workflow de création de section', () => {
    it('devrait créer une section avec les valeurs par défaut', () => {
      const { getDefaults } = useSectionDefaults();

      // Obtenir les valeurs par défaut pour un template
      const defaults = getDefaults('text');
      expect(defaults.settings).toEqual({ align: 'left', size: 'md' });
      expect(defaults.data).toEqual({ content: '' });

      // Créer une section avec ces valeurs
      const section = createMockSection({
        template: 'text',
        settings: defaults.settings,
        data: defaults.data,
      });

      expect(section.settings).toEqual(defaults.settings);
      expect(section.data).toEqual(defaults.data);
    });
  });

  describe('Workflow de styles dynamiques', () => {
    it('devrait appliquer les styles selon les settings', () => {
      const settings = {
        align: 'center',
        size: 'lg',
        classes: 'custom-class',
      };

      const TestComponent = defineComponent({
        setup() {
          const { containerClasses } = useSectionStyles(settings);
          return { containerClasses };
        },
        template: '<div></div>',
      });

      const wrapper = mount(TestComponent);

      const classes = wrapper.vm.containerClasses;
      expect(classes).toContain('text-center');
      expect(classes).toContain('text-lg');
      expect(classes).toContain('custom-class');
    });
  });

  describe('Workflow complet : Création → Affichage → Édition', () => {
    it('devrait gérer le cycle de vie complet d\'une section', () => {
      // 1. Création avec valeurs par défaut
      const { getDefaults } = useSectionDefaults();
      const defaults = getDefaults('text');

      // 2. Section créée
      const section = createMockSection({
        template: 'text',
        settings: defaults.settings,
        data: defaults.data,
        state: 'draft',
      });

      // 3. Mapper vers Model
      const sectionModel = mapToSectionModel(section);

      // 4. Adapter vers UI
      const uiData = adaptSectionToUI(sectionModel);

      // 5. Vérifications
      expect(sectionModel.template).toBe('text');
      expect(uiData.badge.text).toBe('Brouillon');
      expect(uiData.metadata.isEmpty).toBe(true);

      // 6. Ajouter du contenu
      section.data.content = 'Hello World';
      const updatedModel = mapToSectionModel(section);
      const updatedUI = adaptSectionToUI(updatedModel);

      expect(updatedUI.metadata.hasContent).toBe(true);
      expect(updatedUI.metadata.isEmpty).toBe(false);
    });
  });
});

