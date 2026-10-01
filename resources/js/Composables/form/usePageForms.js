import { computed, onBeforeUnmount, ref, shallowReactive, watch } from 'vue';
import { router } from '@inertiajs/vue3';

/**
 * Regroupe les formulaires d’une page derrière un seul « Enregistrer » et une seule
 * « Annuler les modifications », avec une garde de sortie si des modifications sont en attente.
 *
 * @description
 * Chaque formulaire est un `useForm` Inertia (ou un objet exposant `isDirty`, `reset()` et
 * `clearErrors()`), enregistré avec sa fonction d’envoi. `saveAll()` n’envoie que les
 * formulaires modifiés, l’un après l’autre, et s’arrête à la première erreur. Inertia met
 * à jour les valeurs par défaut d’un formulaire après succès, donc `isDirty` repasse à faux.
 *
 * La garde de sortie bloque les navigations GET vers une autre URL (hors préchargement et
 * rechargements partiels) et la fermeture de l’onglet. `PageHeader` affiche la confirmation
 * via `pendingLeave`, `confirmLeave()` et `cancelLeave()`.
 *
 * @example
 * const profile = useForm({ name: user.name });
 * const pageForms = usePageForms();
 * pageForms.register('profile', profile, (callbacks) =>
 *     profile.patch(route('user.update', user.id), { preserveScroll: true, ...callbacks }));
 * // <PageHeader title="Mon compte" :forms="pageForms" />
 *
 * @param {{ guard?: boolean }} [options]
 * @returns {{
 *   register: (key: string, form: object, submit: (callbacks: object) => void) => () => void,
 *   isDirty: import('vue').ComputedRef<boolean>,
 *   processing: import('vue').Ref<boolean>,
 *   status: import('vue').Ref<'idle'|'saving'|'saved'|'error'>,
 *   saveAll: () => Promise<boolean>,
 *   discardAll: () => void,
 *   pendingLeave: import('vue').Ref<boolean>,
 *   confirmLeave: () => void,
 *   cancelLeave: () => void,
 * }}
 */
export function usePageForms({ guard = true } = {}) {
    /** @type {Array<{ key: string, form: object, submit: Function }>} */
    const entries = shallowReactive([]);
    const processing = ref(false);
    const status = ref('idle');
    const pendingLeave = ref(false);
    let pendingVisit = null;
    let bypassGuard = false;

    const isDirty = computed(() => entries.some((entry) => Boolean(entry.form?.isDirty)));

    function register(key, form, submit) {
        const existing = entries.findIndex((entry) => entry.key === key);
        if (existing !== -1) {
            entries.splice(existing, 1);
        }
        entries.push({ key, form, submit });
        return () => {
            const index = entries.findIndex((entry) => entry.key === key);
            if (index !== -1) {
                entries.splice(index, 1);
            }
        };
    }

    function submitEntry(entry) {
        return new Promise((resolve, reject) => {
            entry.submit({
                onSuccess: () => resolve(),
                onError: (errors) => reject(errors ?? new Error('validation')),
                onCancel: () => reject(new Error('cancelled')),
            });
        });
    }

    async function saveAll() {
        if (processing.value) {
            return false;
        }
        const dirtyEntries = entries.filter((entry) => entry.form?.isDirty);
        if (dirtyEntries.length === 0) {
            return true;
        }
        processing.value = true;
        status.value = 'saving';
        bypassGuard = true;
        try {
            for (const entry of dirtyEntries) {
                await submitEntry(entry);
            }
            status.value = 'saved';
            return true;
        } catch {
            status.value = 'error';
            return false;
        } finally {
            processing.value = false;
            bypassGuard = false;
        }
    }

    function discardAll() {
        for (const entry of entries) {
            entry.form?.reset?.();
            entry.form?.clearErrors?.();
        }
        status.value = 'idle';
    }

    watch(isDirty, (dirty) => {
        if (dirty && status.value === 'saved') {
            status.value = 'idle';
        }
    });

    function shouldGuardVisit(visit) {
        if (!guard || bypassGuard || !isDirty.value || !visit) {
            return false;
        }
        if (visit.prefetch || String(visit.method || 'get').toLowerCase() !== 'get') {
            return false;
        }
        if (Array.isArray(visit.only) && visit.only.length > 0) {
            return false;
        }
        const target = visit.url instanceof URL ? visit.url : new URL(String(visit.url), window.location.origin);
        return target.pathname !== window.location.pathname;
    }

    function confirmLeave() {
        const visit = pendingVisit;
        pendingLeave.value = false;
        pendingVisit = null;
        if (!visit) {
            return;
        }
        bypassGuard = true;
        router.visit(visit.url, {
            method: visit.method,
            data: visit.data,
            replace: visit.replace,
            preserveScroll: visit.preserveScroll,
            preserveState: visit.preserveState,
            onFinish: () => {
                bypassGuard = false;
            },
        });
    }

    function cancelLeave() {
        pendingLeave.value = false;
        pendingVisit = null;
    }

    const cleanups = [];
    if (guard && typeof window !== 'undefined') {
        cleanups.push(
            router.on('before', (event) => {
                const visit = event?.detail?.visit;
                if (!shouldGuardVisit(visit)) {
                    return;
                }
                event.preventDefault();
                pendingVisit = visit;
                pendingLeave.value = true;
            }),
        );

        const onBeforeUnload = (event) => {
            if (!isDirty.value || bypassGuard) {
                return;
            }
            event.preventDefault();
            event.returnValue = '';
        };
        window.addEventListener('beforeunload', onBeforeUnload);
        cleanups.push(() => window.removeEventListener('beforeunload', onBeforeUnload));
    }

    onBeforeUnmount(() => {
        for (const cleanup of cleanups) {
            if (typeof cleanup === 'function') {
                cleanup();
            }
        }
    });

    return {
        register,
        isDirty,
        processing,
        status,
        saveAll,
        discardAll,
        pendingLeave,
        confirmLeave,
        cancelLeave,
    };
}
