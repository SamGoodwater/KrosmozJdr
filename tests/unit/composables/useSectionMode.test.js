/**
 * Tests unitaires pour useSectionMode
 */
import { describe, it, expect, beforeEach, afterEach } from 'vitest';
import { mount } from '@vue/test-utils';
import { defineComponent, ref } from 'vue';
import { useSectionMode } from '@/Pages/Organismes/section/composables/useSectionMode';

describe('useSectionMode', () => {
  let sectionId;

  beforeEach(() => {
    localStorage.clear();
    sectionId = ref(1);
  });

  afterEach(() => {
    localStorage.clear();
  });

  it('devrait initialiser avec isEditing à false', () => {
    const TestComponent = defineComponent({
      setup() {
        const { isEditing } = useSectionMode(sectionId);
        return { isEditing };
      },
      template: '<div></div>',
    });

    const wrapper = mount(TestComponent);

    // Sur l'instance du composant, les computed sont déjà « unwrap » (pas de .value)
    expect(wrapper.vm.isEditing).toBe(false);
  });

  it('devrait basculer le mode édition', async () => {
    const TestComponent = defineComponent({
      setup() {
        const { isEditing, toggleEditMode } = useSectionMode(sectionId);
        return { isEditing, toggleEditMode };
      },
      template: '<div></div>',
    });

    const wrapper = mount(TestComponent);

    expect(wrapper.vm.isEditing).toBe(false);

    wrapper.vm.toggleEditMode();
    await wrapper.vm.$nextTick();

    expect(wrapper.vm.isEditing).toBe(true);

    wrapper.vm.toggleEditMode();
    await wrapper.vm.$nextTick();

    expect(wrapper.vm.isEditing).toBe(false);
  });

  it('devrait définir explicitement le mode édition', async () => {
    const TestComponent = defineComponent({
      setup() {
        const { isEditing, setEditMode } = useSectionMode(sectionId);
        return { isEditing, setEditMode };
      },
      template: '<div></div>',
    });

    const wrapper = mount(TestComponent);

    wrapper.vm.setEditMode(true);
    await wrapper.vm.$nextTick();

    expect(wrapper.vm.isEditing).toBe(true);

    wrapper.vm.setEditMode(false);
    await wrapper.vm.$nextTick();

    expect(wrapper.vm.isEditing).toBe(false);
  });

  it("devrait partager l'état d'édition entre composants pour le même sectionId", async () => {
    const sharedId = ref(1);
    const A = defineComponent({
      setup() {
        const { isEditing, setEditMode } = useSectionMode(sharedId);
        return { isEditing, setEditMode };
      },
      template: '<div></div>',
    });
    const B = defineComponent({
      setup() {
        const { isEditing } = useSectionMode(sharedId);
        return { isEditing };
      },
      template: '<div></div>',
    });

    const wa = mount(A);
    const wb = mount(B);

    expect(wb.vm.isEditing).toBe(false);
    wa.vm.setEditMode(true);
    await wa.vm.$nextTick();
    await wb.vm.$nextTick();

    expect(wb.vm.isEditing).toBe(true);
  });
});
