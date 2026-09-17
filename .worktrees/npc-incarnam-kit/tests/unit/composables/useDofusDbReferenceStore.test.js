/**
 * Tests — store + ouverture panneau DofusDB
 */
import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';
import { setActivePinia, createPinia } from 'pinia';
import { mount } from '@vue/test-utils';
import { defineComponent, h, nextTick } from 'vue';
import { useDofusDbReferenceStore } from '@/Composables/store/useDofusDbReferenceStore';
import EntityActionButton from '@/Pages/Atoms/action/EntityActionButton.vue';
import DofusDbReferencePanel from '@/Pages/Molecules/entity/DofusDbReferencePanel.vue';

describe('useDofusDbReferenceStore', () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });

    it('openPanel met isOpen à true et calcule l’URL', () => {
        const store = useDofusDbReferenceStore();
        store.openPanel('spells', { id: 880, name: 'Test', dofusdb_id: 201 });
        expect(store.isOpen).toBe(true);
        expect(store.dofusdbId).toBe('201');
        expect(store.dofusDbUrl).toBe('https://dofusdb.fr/fr/database/spells/201');
        expect(store.entityLabel).toBe('Test');
    });
});

describe('EntityActionButton view-dofusdb', () => {
    beforeEach(() => {
        setActivePinia(createPinia());
        vi.stubGlobal('open', vi.fn(() => ({ focus: vi.fn(), opener: null })));
    });

    afterEach(() => {
        vi.unstubAllGlobals();
    });

    it('ouvre le store au clic sans dépendre du parent', async () => {
        const store = useDofusDbReferenceStore();
        const entity = { id: 880, name: 'Sort test', _data: { dofusdb_id: 42, name: 'Sort test' } };

        const wrapper = mount(EntityActionButton, {
            props: {
                action: {
                    key: 'view-dofusdb',
                    label: 'DofusDB',
                    tooltip: 'Voir DofusDB',
                    icon: '/images/logos/dofus.png',
                },
                entityType: 'spells',
                entity,
                display: 'icon-only',
            },
            global: {
                stubs: {
                    Icon: true,
                },
            },
        });

        await wrapper.get('[data-testid="entity-action-view-dofusdb"]').trigger('click');
        expect(store.isOpen).toBe(true);
        expect(store.dofusDbUrl).toContain('/spells/42');
        expect(window.open).not.toHaveBeenCalled();
    });
});

describe('DofusDbReferencePanel', () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });

    it('affiche le panneau quand le store est ouvert', async () => {
        const store = useDofusDbReferenceStore();
        const Host = defineComponent({
            setup() {
                return () => h(DofusDbReferencePanel);
            },
        });

        const wrapper = mount(Host, {
            attachTo: document.body,
            global: {
                stubs: {
                    Icon: true,
                    Btn: { template: '<button><slot /></button>' },
                },
            },
        });

        expect(document.querySelector('[data-testid="dofusdb-reference-panel"]')).toBeNull();

        store.openPanel('spells', { name: 'X', dofusdb_id: 9 });
        await nextTick();

        const panel = document.querySelector('[data-testid="dofusdb-reference-panel"]');
        expect(panel).not.toBeNull();
        expect(panel.textContent).toContain('Référence DofusDB');
        expect(panel.textContent).toContain('9');

        wrapper.unmount();
    });
});
