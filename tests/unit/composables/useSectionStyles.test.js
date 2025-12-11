/**
 * Tests unitaires pour useSectionStyles
 */
import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { defineComponent } from 'vue';
import { useSectionStyles } from '@/Pages/Organismes/section/composables/useSectionStyles';

describe('useSectionStyles', () => {
  it('devrait générer les classes d\'alignement', () => {
    const TestComponent = defineComponent({
      setup() {
        const { alignClasses } = useSectionStyles({ align: 'center' });
        return { alignClasses };
      },
      template: '<div></div>',
    });

    const wrapper = mount(TestComponent);
    expect(wrapper.vm.alignClasses).toBe('text-center');
  });

  it('devrait générer les classes de taille', () => {
    const TestComponent = defineComponent({
      setup() {
        const { sizeClasses } = useSectionStyles({ size: 'lg' });
        return { sizeClasses };
      },
      template: '<div></div>',
    });

    const wrapper = mount(TestComponent);
    expect(wrapper.vm.sizeClasses).toBe('text-lg');
  });

  it('devrait générer les classes combinées pour conteneur', () => {
    const TestComponent = defineComponent({
      setup() {
        const { containerClasses } = useSectionStyles({
          align: 'right',
          size: 'xl',
          classes: 'custom-class',
        });
        return { containerClasses };
      },
      template: '<div></div>',
    });

    const wrapper = mount(TestComponent);
    const classes = wrapper.vm.containerClasses;
    
    expect(classes).toContain('text-right');
    expect(classes).toContain('text-xl');
    expect(classes).toContain('custom-class');
  });

  it('devrait générer les classes de galerie', () => {
    const TestComponent = defineComponent({
      setup() {
        const { galleryClasses } = useSectionStyles({
          columns: 4,
          gap: 'lg',
        });
        return { galleryClasses };
      },
      template: '<div></div>',
    });

    const wrapper = mount(TestComponent);
    const classes = wrapper.vm.galleryClasses;
    
    expect(classes).toContain('grid');
    expect(classes).toContain('grid-cols-4');
    expect(classes).toContain('gap-6');
  });
});

