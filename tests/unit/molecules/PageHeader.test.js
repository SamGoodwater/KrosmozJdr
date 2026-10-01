import { describe, it, expect, vi } from 'vitest';
import { computed, ref } from 'vue';
import { mount } from '@vue/test-utils';
import PageHeader from '@/Pages/Molecules/layout/PageHeader.vue';
import PageSaveActions from '@/Pages/Molecules/action/PageSaveActions.vue';
import { usePageTitle } from '@/Composables/layout/usePageTitle';

window.matchMedia = window.matchMedia || ((query) => ({
    matches: false,
    media: query,
    addEventListener: () => {},
    removeEventListener: () => {},
}));

const stubs = {
    Route: { props: ['href'], template: '<a :href="href"><slot /></a>' },
    ConfirmModal: { props: ['open'], template: '<div v-if="open" data-testid="leave-modal" />' },
};

function fakeForms({ dirty = false } = {}) {
    return {
        isDirty: computed(() => dirty),
        processing: ref(false),
        status: ref('idle'),
        pendingLeave: ref(false),
        saveAll: vi.fn(),
        discardAll: vi.fn(),
        confirmLeave: vi.fn(),
        cancelLeave: vi.fn(),
    };
}

describe('PageHeader', () => {
    it('affiche titre, sous-titre et synchronise le titre de page', () => {
        const wrapper = mount(PageHeader, {
            props: { title: 'Langues', subtitle: 'Langues des créatures' },
            global: { stubs },
        });
        expect(wrapper.find('h1').text()).toBe('Langues');
        expect(wrapper.text()).toContain('Langues des créatures');
        const { pageTitle, hasPageHeader } = usePageTitle();
        expect(pageTitle.value).toBe('Langues');
        expect(hasPageHeader.value).toBe(true);
        wrapper.unmount();
        expect(hasPageHeader.value).toBe(false);
    });

    it('affiche le retour quand une URL est fournie', () => {
        const wrapper = mount(PageHeader, {
            props: { title: 'Retour utilisateur', backHref: '/admin/feedback' },
            global: { stubs },
        });
        expect(wrapper.find('a').attributes('href')).toBe('/admin/feedback');
        expect(wrapper.text()).toContain('Retour');
    });

    it('branche Enregistrer et Annuler les modifications sur forms', async () => {
        const forms = fakeForms({ dirty: true });
        const wrapper = mount(PageHeader, {
            props: { title: 'IA métier', forms },
            global: { stubs },
        });
        await wrapper.find('[data-testid="page-save-primary"]').trigger('click');
        expect(forms.saveAll).toHaveBeenCalledTimes(1);
        await wrapper.find('[data-testid="page-save-discard"]').trigger('click');
        expect(forms.discardAll).toHaveBeenCalledTimes(1);
    });

    it('le slot primary remplace le bloc d’enregistrement', () => {
        const wrapper = mount(PageHeader, {
            props: { title: 'Sauvegarde', forms: fakeForms() },
            slots: { primary: '<button data-testid="run">Lancer</button>' },
            global: { stubs },
        });
        expect(wrapper.find('[data-testid="run"]').exists()).toBe(true);
        expect(wrapper.findComponent(PageSaveActions).exists()).toBe(false);
    });
});

describe('PageSaveActions', () => {
    it('désactive Enregistrer sans modification et masque Annuler les modifications', () => {
        const wrapper = mount(PageSaveActions, { props: { dirty: false } });
        expect(wrapper.find('[data-testid="page-save-primary"]').attributes('disabled')).toBeDefined();
        expect(wrapper.find('[data-testid="page-save-discard"]').exists()).toBe(false);
    });

    it('émet save via Ctrl+S quand des modifications sont en attente', async () => {
        const wrapper = mount(PageSaveActions, { props: { dirty: true }, attachTo: document.body });
        window.dispatchEvent(new KeyboardEvent('keydown', { key: 's', ctrlKey: true }));
        expect(wrapper.emitted('save')).toHaveLength(1);
        wrapper.unmount();
    });

    it('affiche le libellé de traitement pendant l’enregistrement', () => {
        const wrapper = mount(PageSaveActions, { props: { dirty: true, processing: true } });
        expect(wrapper.find('[data-testid="page-save-primary"]').text()).toContain('Enregistrement…');
    });

    it('garde l’échec visible tant que des modifications restent en attente', () => {
        const wrapper = mount(PageSaveActions, { props: { dirty: true, status: 'error' } });
        expect(wrapper.find('[data-testid="page-save-dirty"]').exists()).toBe(true);
        expect(wrapper.text()).toContain('Échec de l’enregistrement');
    });
});
