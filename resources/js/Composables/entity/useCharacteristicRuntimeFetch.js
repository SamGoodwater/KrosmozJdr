/**
 * useCharacteristicRuntimeFetch — charge un payload `{ computed: { [key]: … } }` depuis une URL.
 *
 * @description
 * Utilisable pour tout endpoint qui renvoie le même schéma que `resolved-stats` créature.
 * `useCreatureResolvedStats` s’appuie sur ce composable.
 *
 * @param {import('vue').Ref<string|null|undefined>|import('vue').ComputedRef<string|null|undefined>|function} urlSource — URL complète, ou null pour ne rien charger
 * @returns {{ runtime: import('vue').ShallowRef<object|null>, loading: import('vue').Ref<boolean>, error: import('vue').Ref<Error|null>, refresh: function }}
 */
import { ref, shallowRef, watch, toValue } from "vue";

export function useCharacteristicRuntimeFetch(urlSource) {
    const runtime = shallowRef(null);
    const loading = ref(false);
    const error = ref(null);

    async function refresh() {
        const url = toValue(urlSource);
        if (!url || typeof url !== "string") {
            runtime.value = null;
            return;
        }
        loading.value = true;
        error.value = null;
        try {
            const res = await fetch(url, {
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                credentials: "same-origin",
            });
            if (!res.ok) {
                runtime.value = null;
                return;
            }
            runtime.value = await res.json();
        } catch (e) {
            error.value = e instanceof Error ? e : new Error(String(e));
            runtime.value = null;
        } finally {
            loading.value = false;
        }
    }

    watch(() => toValue(urlSource), refresh, { immediate: true });

    return { runtime, loading, error, refresh };
}
