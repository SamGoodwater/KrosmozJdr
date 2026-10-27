/**
 * Le menu Aside ne se recharge pas quand seule l’URL change.
 */
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { defineComponent, h, nextTick, reactive } from 'vue';
import { mount, flushPromises } from '@vue/test-utils';

const state = vi.hoisted(() => ({ page: null }));

vi.mock('@inertiajs/vue3', () => ({
    usePage: () => state.page,
}));

vi.mock('axios', () => ({
    default: {
        get: vi.fn(),
    },
}));

import axios from 'axios';
import { resetDynamicMenuForTests, useDynamicMenu } from '@/Composables/layout/useDynamicMenu';

const pageState = reactive({
    url: '/pages/accueil',
    props: {
        menu_revision: 0,
        auth: { user: null },
    },
});
state.page = pageState;

const sampleMenu = [
    {
        id: 'regles',
        title: 'Règles',
        url: '/pages/regles',
        children: [
            { id: 2, title: 'Combat', url: '/pages/combat', children: [] },
        ],
    },
];

function mountConsumer() {
    let api;
    const wrapper = mount(defineComponent({
        setup() {
            api = useDynamicMenu();
            return () => h('div');
        },
    }));
    return { api, wrapper };
}

describe('useDynamicMenu', () => {
    beforeEach(() => {
        resetDynamicMenuForTests();
        pageState.url = '/pages/accueil';
        pageState.props.menu_revision = 0;
        pageState.props.auth = { user: null };
        axios.get.mockReset();
        axios.get.mockResolvedValue({ data: { menu: sampleMenu } });
    });

    it('charge le menu une fois et ne le recharge pas au changement d’URL', async () => {
        const first = mountConsumer();
        const second = mountConsumer();
        await flushPromises();

        expect(axios.get).toHaveBeenCalledTimes(1);
        expect(first.api.menuItems.value).toEqual(sampleMenu);
        expect(second.api.menuItems.value).toEqual(sampleMenu);
        expect(first.api.loading.value).toBe(false);

        pageState.url = '/pages/combat';
        await nextTick();
        await flushPromises();

        expect(axios.get).toHaveBeenCalledTimes(1);
        expect(first.api.isPageActive(sampleMenu[0].children[0], pageState.url)).toBe(true);
        expect(first.api.shouldMenuBeOpen(sampleMenu[0], pageState.url)).toBe(true);
        expect(first.api.isPageActive(sampleMenu[0].children[0], '/pages/accueil')).toBe(false);

        first.wrapper.unmount();
        second.wrapper.unmount();
    });

    it('recharge en silence si la révision ou le compte change', async () => {
        const { api, wrapper } = mountConsumer();
        await flushPromises();
        expect(axios.get).toHaveBeenCalledTimes(1);

        let release;
        axios.get.mockImplementationOnce(() => new Promise((resolve) => {
            release = resolve;
        }));

        pageState.props.menu_revision = 1;
        await nextTick();

        expect(axios.get).toHaveBeenCalledTimes(2);
        expect(api.loading.value).toBe(false);
        expect(api.menuItems.value).toEqual(sampleMenu);

        release({ data: { menu: [{ id: 'infos', title: 'Informations', children: [] }] } });
        await flushPromises();
        expect(api.menuItems.value[0].title).toBe('Informations');

        pageState.props.auth = { user: { id: 4, role: 3 } };
        await nextTick();
        await flushPromises();
        expect(axios.get).toHaveBeenCalledTimes(3);

        wrapper.unmount();
    });
});
