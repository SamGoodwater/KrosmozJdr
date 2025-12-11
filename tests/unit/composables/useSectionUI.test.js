/**
 * Tests unitaires pour useSectionUI
 */
import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { defineComponent } from 'vue';
import { useSectionUI } from '@/Pages/Organismes/section/composables/useSectionUI';
import { createMockSection } from '../../setup.js';

describe('useSectionUI', () => {
  it('devrait retourner les données UI adaptées', () => {
    const section = createMockSection({
      template: 'text',
      state: 'published',
    });

    const TestComponent = defineComponent({
      setup() {
        const { sectionModel, uiData, canEdit, templateInfo, stateInfo } = useSectionUI(section);
        return {
          sectionModel,
          uiData,
          canEdit,
          templateInfo,
          stateInfo,
        };
      },
      template: '<div></div>',
    });

    const wrapper = mount(TestComponent);

    expect(wrapper.vm.sectionModel).toBeDefined();
    expect(wrapper.vm.uiData.color).toBe('success');
    expect(wrapper.vm.canEdit).toBe(true);
    expect(wrapper.vm.templateInfo.value).toBe('text');
    expect(wrapper.vm.stateInfo.value).toBe('published');
  });

  it('devrait détecter si une section a du contenu', () => {
    const sectionWithContent = createMockSection({
      template: 'text',
      data: { content: 'Hello' },
    });

    const sectionEmpty = createMockSection({
      template: 'text',
      data: { content: '' },
    });

    const ComponentWithContent = defineComponent({
      setup() {
        return useSectionUI(sectionWithContent);
      },
      template: '<div></div>',
    });

    const ComponentEmpty = defineComponent({
      setup() {
        return useSectionUI(sectionEmpty);
      },
      template: '<div></div>',
    });

    const wrapperWithContent = mount(ComponentWithContent);
    const wrapperEmpty = mount(ComponentEmpty);

    expect(wrapperWithContent.vm.hasContent).toBe(true);
    expect(wrapperEmpty.vm.isEmpty).toBe(true);
  });

  it('devrait générer l\'URL de la section', () => {
    const section = createMockSection({
      id: 123,
      page: { slug: 'test-page' },
    });

    const TestComponent = defineComponent({
      setup() {
        return useSectionUI(section);
      },
      template: '<div></div>',
    });

    const wrapper = mount(TestComponent);

    expect(wrapper.vm.url).toContain('test-page');
    expect(wrapper.vm.url).toContain('section-123');
  });
});

