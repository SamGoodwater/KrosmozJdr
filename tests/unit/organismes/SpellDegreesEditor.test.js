import { describe, expect, it, vi, afterEach } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';
import axios from 'axios';
import SpellDegreesEditor from '@/Pages/Organismes/entity/SpellDegreesEditor.vue';

vi.mock('axios', () => ({
    default: {
        get: vi.fn(() => Promise.resolve({ data: { data: { degrees: [], default_degree_id: null } } })),
        post: vi.fn(() => Promise.resolve({ data: { data: { degrees: [], default_degree_id: null } } })),
        patch: vi.fn(() => Promise.resolve({ data: { data: { degrees: [], default_degree_id: null } } })),
        delete: vi.fn(() => Promise.resolve({ data: { data: { degrees: [], default_degree_id: null } } })),
    },
}));

/** @type {import('@vue/test-utils').VueWrapper[]} */
const wrappers = [];

afterEach(() => {
    while (wrappers.length) {
        wrappers.pop().unmount();
    }
    vi.clearAllMocks();
});

const subEffects = [
    {
        id: 1,
        slug: 'frapper',
        type_slug: 'frapper',
        param_schema: {
            params: [
                { key: 'characteristic', categories: ['element'] },
                { key: 'value' },
                { key: 'life_steal_formula' },
            ],
        },
    },
    {
        id: 2,
        slug: 'booster',
        type_slug: 'booster',
        param_schema: {
            params: [{ key: 'characteristic' }, { key: 'value' }],
        },
    },
    {
        id: 3,
        slug: 'invoquer',
        type_slug: 'invoquer',
        param_schema: { params: [{ key: 'monster' }] },
    },
    {
        id: 4,
        slug: 'appliquer-etat',
        type_slug: 'appliquer-etat',
        param_schema: { params: [{ key: 'condition' }] },
    },
];

const effectFormOptions = {
    sub_effects: subEffects,
    characteristics: [
        { key: 'fire', label: 'Feu', category: 'element' },
        { key: 'earth', label: 'Terre', category: 'element' },
    ],
    characteristics_object: [
        { key: 'action_points_object', label: 'PA', category: 'object' },
        { key: 'vitality_object', label: 'Vitalité', category: 'object' },
    ],
    scopes: [
        { value: 'general', label: 'Général' },
        { value: 'combat', label: 'Combat' },
    ],
};

function baseDegree(overrides = {}) {
    return {
        id: 10,
        position: 1,
        required_level: 1,
        inherits_effects: false,
        properties: {
            pa: '3',
            po_min: '1',
            po_max: '6',
            sight_line: true,
            cast_per_turn: '1',
            number_between_two_cast: '2',
            casting_time: '0',
            global_cooldown: 0,
        },
        rows: [
            {
                sub_effect_id: 1,
                scope: 'general',
                crit_only: false,
                duration_formula: '1',
                logic_operator: '',
                params: {
                    characteristic: 'fire',
                    value_formula: '2d6',
                    value_formula_crit: '',
                    life_steal_formula: '',
                },
                sub_effect: subEffects[0],
            },
        ],
        ...overrides,
    };
}

function mountEditor(degrees = [baseDegree()], extra = {}) {
    const wrapper = mount(SpellDegreesEditor, {
        props: {
            spellId: 42,
            spellDegrees: { degrees, default_degree_id: degrees[0]?.id ?? null },
            effectFormOptions,
            ...extra,
        },
        global: {
            stubs: {
                SpellDegreeEffectRow: {
                    props: ['row', 'index', 'options'],
                    template:
                        '<div data-cy="spell-degree-effect-row" class="effect-stub">{{ row.sub_effect_id }}</div>',
                },
                SpellElementPrimariesField: true,
            },
        },
    });
    wrappers.push(wrapper);
    return wrapper;
}

describe('SpellDegreesEditor', () => {
    it('affiche une portée compacte unique', async () => {
        const wrapper = mountEditor();
        await flushPromises();
        await wrapper.find('button.btn-ghost').trigger('click');
        const po = wrapper.find('[data-cy="spell-degree-po-range"]');
        expect(po.exists()).toBe(true);
        expect(po.element.value).toBe('1-6');
        expect(wrapper.text()).toContain('PO 1-6');
    });

    it('expose les champs de fréquence dans le panneau propriétés', async () => {
        const wrapper = mountEditor();
        await flushPromises();
        await wrapper.find('button.btn-ghost').trigger('click');
        expect(wrapper.text()).toContain('Temps de relance');
        expect(wrapper.text()).toContain('Lancers / tour');
        expect(wrapper.text()).toContain('Lancers / cible');
        expect(wrapper.text()).toContain('Temps d’incantation');
    });

    it('préserve l’héritage des effets du degré précédent', async () => {
        const wrapper = mountEditor([
            baseDegree({ id: 1, position: 1, required_level: 1 }),
            baseDegree({
                id: 2,
                position: 2,
                required_level: 5,
                inherits_effects: true,
                rows: [],
            }),
        ]);
        await flushPromises();
        const tabs = wrapper.findAll('[role="tab"]');
        expect(tabs).toHaveLength(2);
        await tabs[1].trigger('click');
        expect(wrapper.text()).toContain('Reprendre les effets du degré précédent');
        expect(wrapper.text()).toContain('réutilise les effets');
    });

    it('demande confirmation avant de quitter un onglet sale', async () => {
        const confirmSpy = vi.spyOn(globalThis, 'confirm').mockReturnValue(false);
        const wrapper = mountEditor([
            baseDegree({ id: 1 }),
            baseDegree({ id: 2, position: 2, required_level: 3 }),
        ]);
        await flushPromises();
        await wrapper.find('button.btn-ghost').trigger('click');
        const po = wrapper.find('[data-cy="spell-degree-po-range"]');
        await po.setValue('2-8');
        await wrapper.findAll('[role="tab"]')[1].trigger('click');
        expect(confirmSpy).toHaveBeenCalled();
        expect(wrapper.findAll('[role="tab"]')[0].classes()).toContain('tab-active');
        confirmSpy.mockRestore();
    });

    it('flushSave envoie le PATCH lorsque dirty', async () => {
        axios.patch.mockResolvedValueOnce({
            data: {
                data: {
                    degrees: [baseDegree({ properties: { pa: '4', po_min: '2', po_max: '8' } })],
                    default_degree_id: 10,
                },
            },
        });
        const wrapper = mountEditor();
        await flushPromises();
        await wrapper.find('button.btn-ghost').trigger('click');
        await wrapper.find('[data-cy="spell-degree-po-range"]').setValue('2-8');
        const ok = await wrapper.vm.flushSave();
        expect(ok).toBe(true);
        expect(axios.patch).toHaveBeenCalled();
        const body = axios.patch.mock.calls[0][1];
        expect(body.po_min).toBe('2');
        expect(body.po_max).toBe('8');
    });
});
