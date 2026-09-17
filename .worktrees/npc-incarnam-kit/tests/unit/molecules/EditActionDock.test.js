import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import EditActionDock from '@/Pages/Molecules/action/EditActionDock.vue';

describe('EditActionDock', () => {
    it('affiche le libellé principal et émet primary au clic', async () => {
        const wrapper = mount(EditActionDock, {
            props: {
                primaryLabel: 'Enregistrer',
                processing: false,
                showSecondary: false,
                secondaryActions: [],
            },
        });

        expect(wrapper.text()).toContain('Enregistrer');

        const buttons = wrapper.findAll('button');
        const primary = buttons[buttons.length - 1];
        await primary.trigger('click');

        expect(wrapper.emitted('primary')).toBeTruthy();
        expect(wrapper.emitted('primary').length).toBe(1);
    });

    it('émet action avec la clé secondaire (mobile)', async () => {
        const wrapper = mount(EditActionDock, {
            props: {
                primaryLabel: 'Sauver',
                showSecondary: true,
                secondaryActions: [
                    { key: 'cancel', label: 'Annuler', variant: 'outline', color: '' },
                ],
            },
        });

        const cancelBtn = wrapper.findAll('button').find((b) => b.text().includes('Annuler'));
        expect(cancelBtn).toBeDefined();
        await cancelBtn.trigger('click');

        expect(wrapper.emitted('action')).toEqual([['cancel']]);
    });
});
