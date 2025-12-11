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
    // Réinitialiser le localStorage
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

    expect(wrapper.vm.isEditing.value).toBe(false);
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

    expect(wrapper.vm.isEditing.value).toBe(false);

    wrapper.vm.toggleEditMode();
    await wrapper.vm.$nextTick();

    expect(wrapper.vm.isEditing.value).toBe(true);

    wrapper.vm.toggleEditMode();
    await wrapper.vm.$nextTick();

    expect(wrapper.vm.isEditing.value).toBe(false);
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

    expect(wrapper.vm.isEditing.value).toBe(true);

    wrapper.vm.setEditMode(false);
    await wrapper.vm.$nextTick();

    expect(wrapper.vm.isEditing.value).toBe(false);
  });

  it('devrait persister le mode édition dans localStorage', async () => {
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

    // Vérifier que c'est sauvegardé dans localStorage
    const saved = localStorage.getItem('section-edit-mode-1');
    expect(saved).toBe('true');
  });
});

