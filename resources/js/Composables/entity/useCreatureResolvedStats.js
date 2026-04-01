/**
 * useCreatureResolvedStats — charge le payload runtime (formules résolues, placeholders).
 *
 * @description
 * Appelle `GET /entities/creatures/{id}/resolved-stats?entity=…` pour alimenter
 * `CharacteristicGroup` / `useCharacteristicViewModel` / `EntityPropertyDisplay`.
 *
 * @param {import('vue').Ref|function|import('vue').ComputedRef} creatureIdSource — id créature ou getter
 * @param {import('vue').Ref|function|string} [entityQuery] — défaut `monster`
 * @returns {{ runtime: import('vue').ShallowRef<object|null>, loading: import('vue').Ref<boolean>, error: import('vue').Ref<Error|null>, refresh: function }}
 */
import { computed, toValue } from "vue";
import { useCharacteristicRuntimeFetch } from "@/Composables/entity/useCharacteristicRuntimeFetch";

export function useCreatureResolvedStats(creatureIdSource, entityQuery = "monster") {
    const url = computed(() => {
        const id = toValue(creatureIdSource);
        if (id === null || id === undefined || id === "") {
            return null;
        }
        const entity = encodeURIComponent(String(toValue(entityQuery) || "monster"));
        const base = route("entities.creatures.resolvedStats", { creature: id });
        return `${base}?entity=${entity}`;
    });

    return useCharacteristicRuntimeFetch(url);
}
