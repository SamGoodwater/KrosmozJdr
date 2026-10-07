import { describe, expect, it, vi, afterEach, beforeEach } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';
import { ref } from 'vue';
import EntityEditHeader from '@/Pages/Molecules/entity/shared/EntityEditHeader.vue';

vi.mock('@inertiajs/vue3', () => ({
    router: { visit: vi.fn(), delete: vi.fn() },
}));

/** @type {import('@vue/test-utils').VueWrapper[]} */
const wrappers = [];

beforeEach(() => {
    global.route = vi.fn((name, params, absolute) => {
        const path = `/${name}/${params ? Object.values(params).join('/') : ''}`;
        if (absolute === false) return path;
        return `http://example.test${path}`;
    });
});

afterEach(() => {
    while (wrappers.length) {
        wrappers.pop().unmount();
    }
});

function mountHeader(overrides = {}) {
    const submit = vi.fn();
    const resetForm = vi.fn();
    const cancel = vi.fn();
    const formRef = ref({
        submit,
        resetForm,
        cancel,
        processing: { value: false },
        primarySaveLabel: 'Enregistrer',
        stateField: null,
        form: {},
        markDirty: vi.fn(),
        getFieldLabel: () => 'État',
        getFieldValidation: () => null,
        dispatchEntityAction: vi.fn(),
    });

    const wrapper = mount(EntityEditHeader, {
        props: {
            entityType: 'items',
            entity: { id: 42, name: 'Épée' },
            formRef: formRef.value,
            listRouteName: 'entities.items.index',
            deleteRouteName: 'entities.items.delete',
            routeParamKey: 'item',
            canDelete: true,
            ...overrides.props,
        },
        global: {
            stubs: {
                EntityActions: true,
                EntityListBackButton: true,
                FormulaHelpHint: true,
                SelectField: true,
                Btn: {
                    props: ['disabled'],
                    emits: ['click'],
                    template: '<button type="button" @click="$emit(\'click\')"><slot /></button>',
                },
            },
        },
        ...overrides,
    });
    wrappers.push(wrapper);
    return { wrapper, submit, resetForm, cancel, formRef };
}

describe('EntityEditHeader', () => {
    it('affiche les actions et appelle submit / resetForm sur la ref', async () => {
        const { wrapper, submit, resetForm } = mountHeader();
        await flushPromises();

        expect(wrapper.find('[data-cy="entity-edit-header"]').exists()).toBe(true);
        expect(wrapper.text()).toContain('Épée');
        expect(wrapper.text()).toContain('Annuler');
        expect(wrapper.text()).toContain('Annuler les modifications');
        expect(wrapper.text()).toContain('Supprimer');
        expect(wrapper.text()).toContain('Enregistrer');

        await wrapper.find('[data-cy="entity-edit-save"]').trigger('click');
        expect(submit).toHaveBeenCalledTimes(1);

        const resetBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('Annuler les modifications'));
        expect(resetBtn).toBeTruthy();
        await resetBtn.trigger('click');
        expect(resetForm).toHaveBeenCalledTimes(1);
    });

    it('masque Supprimer sans droit', async () => {
        const { wrapper } = mountHeader({ props: { canDelete: false } });
        await flushPromises();
        expect(wrapper.find('[data-cy="entity-edit-delete"]').exists()).toBe(false);
        expect(wrapper.text()).not.toContain('Supprimer');
    });

    it('supprime via une URL relative Ziggy', async () => {
        const { router } = await import('@inertiajs/vue3');
        vi.spyOn(window, 'confirm').mockReturnValue(true);

        const { wrapper } = mountHeader();
        await flushPromises();
        await wrapper.find('[data-cy="entity-edit-delete"]').trigger('click');

        expect(global.route).toHaveBeenCalledWith(
            'entities.items.delete',
            { item: 42 },
            false,
        );
        expect(router.delete).toHaveBeenCalledWith(
            '/entities.items.delete/42',
            expect.objectContaining({ onSuccess: expect.any(Function) }),
        );
    });
});
