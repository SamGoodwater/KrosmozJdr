import { describe, it, expect, vi, beforeEach } from 'vitest';
import { defineComponent, h, reactive } from 'vue';
import { mount } from '@vue/test-utils';

const beforeHandlers = [];
vi.mock('@inertiajs/vue3', () => ({
    router: {
        on: vi.fn((event, handler) => {
            if (event === 'before') beforeHandlers.push(handler);
            return () => {
                const i = beforeHandlers.indexOf(handler);
                if (i !== -1) beforeHandlers.splice(i, 1);
            };
        }),
        visit: vi.fn(),
    },
}));

import { router } from '@inertiajs/vue3';
import { usePageForms } from '@/Composables/form/usePageForms';

function fakeForm(dirty = false) {
    return reactive({
        isDirty: dirty,
        reset: vi.fn(function reset() { this.isDirty = false; }),
        clearErrors: vi.fn(),
    });
}

function setup(options) {
    let api;
    const wrapper = mount(defineComponent({
        setup() {
            api = usePageForms(options);
            return () => h('div');
        },
    }));
    return { api, wrapper };
}

function fireBefore(visit) {
    const event = { detail: { visit }, preventDefault: vi.fn() };
    beforeHandlers.forEach((handler) => handler(event));
    return event;
}

describe('usePageForms', () => {
    beforeEach(() => {
        beforeHandlers.length = 0;
        router.visit.mockClear();
    });

    it('agrège isDirty et n’envoie que les formulaires modifiés', async () => {
        const { api } = setup();
        const clean = fakeForm(false);
        const dirty = fakeForm(true);
        const submitClean = vi.fn();
        const submitDirty = vi.fn(({ onSuccess }) => { dirty.isDirty = false; onSuccess(); });
        api.register('clean', clean, submitClean);
        api.register('dirty', dirty, submitDirty);

        expect(api.isDirty.value).toBe(true);
        await expect(api.saveAll()).resolves.toBe(true);
        expect(submitDirty).toHaveBeenCalledTimes(1);
        expect(submitClean).not.toHaveBeenCalled();
        expect(api.status.value).toBe('saved');
        expect(api.isDirty.value).toBe(false);
    });

    it('s’arrête à la première erreur', async () => {
        const { api } = setup();
        const a = fakeForm(true);
        const b = fakeForm(true);
        const submitA = vi.fn(({ onError }) => onError({ name: 'requis' }));
        const submitB = vi.fn();
        api.register('a', a, submitA);
        api.register('b', b, submitB);

        await expect(api.saveAll()).resolves.toBe(false);
        expect(submitB).not.toHaveBeenCalled();
        expect(api.status.value).toBe('error');
        expect(api.processing.value).toBe(false);
    });

    it('discardAll réinitialise tous les formulaires', () => {
        const { api } = setup();
        const a = fakeForm(true);
        api.register('a', a, vi.fn());
        api.discardAll();
        expect(a.reset).toHaveBeenCalled();
        expect(a.clearErrors).toHaveBeenCalled();
        expect(api.isDirty.value).toBe(false);
    });

    it('bloque une navigation GET vers une autre page puis la rejoue après confirmation', () => {
        const { api } = setup();
        api.register('a', fakeForm(true), vi.fn());

        const event = fireBefore({ url: new URL('/autre', window.location.origin), method: 'get' });
        expect(event.preventDefault).toHaveBeenCalled();
        expect(api.pendingLeave.value).toBe(true);

        api.confirmLeave();
        expect(api.pendingLeave.value).toBe(false);
        expect(router.visit).toHaveBeenCalledTimes(1);
    });

    it('laisse passer préchargements, rechargements partiels et envois', () => {
        const { api } = setup();
        api.register('a', fakeForm(true), vi.fn());
        const url = new URL('/autre', window.location.origin);

        expect(fireBefore({ url, method: 'get', prefetch: true }).preventDefault).not.toHaveBeenCalled();
        expect(fireBefore({ url, method: 'get', only: ['items'] }).preventDefault).not.toHaveBeenCalled();
        expect(fireBefore({ url, method: 'post' }).preventDefault).not.toHaveBeenCalled();
        expect(api.pendingLeave.value).toBe(false);
    });

    it('ne bloque rien sans modification et retire ses écouteurs au démontage', () => {
        const { api, wrapper } = setup();
        api.register('a', fakeForm(false), vi.fn());
        const event = fireBefore({ url: new URL('/autre', window.location.origin), method: 'get' });
        expect(event.preventDefault).not.toHaveBeenCalled();

        wrapper.unmount();
        expect(beforeHandlers).toHaveLength(0);
    });
});
