<script setup>
/**
 * Édition des orientations par voix élémentaire (PATCH partiel sur la classe).
 */
import { computed, reactive, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import EditActionDock from "@/Pages/Molecules/action/EditActionDock.vue";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import {
    BREED_ELEMENT_KEYS,
    BREED_ELEMENT_LABELS,
    normalizeElementOrientationMap,
} from "@/Utils/entity/breedOrientations";
import { warnDev } from "@/Utils/dev-logger";

/**
 * @param {Record<string, unknown>} map
 * @returns {Record<string, string>}
 */
function toSelectStringMap(map) {
    const n = normalizeElementOrientationMap(map);
    const o = {};
    for (const k of BREED_ELEMENT_KEYS) {
        o[k] = n[k] == null ? "" : String(n[k]);
    }
    return o;
}

const props = defineProps({
    breedId: {
        type: Number,
        required: true,
    },
    /** @type {Record<string, string|null>} */
    initialMap: {
        type: Object,
        default: () => ({}),
    },
    /** Clés d’orientation autorisées (API / config) */
    orientationKeys: {
        type: Array,
        default: () => [],
    },
});

const notificationStore = useNotificationStore();

const localOrientations = reactive(toSelectStringMap(props.initialMap));

watch(
    () => props.initialMap,
    (next) => {
        const m = toSelectStringMap(next);
        for (const k of BREED_ELEMENT_KEYS) {
            localOrientations[k] = m[k];
        }
    },
    { deep: true }
);

const processing = ref(false);

const orientationOptions = computed(() => {
    const keys = Array.isArray(props.orientationKeys) ? props.orientationKeys : [];
    return keys.map((k) => ({
        value: k,
        label: String(k).replace(/_/g, " "),
    }));
});

const hasChanges = computed(() => {
    const cur = JSON.stringify(toPayloadMap(localOrientations));
    const orig = JSON.stringify(normalizeElementOrientationMap(props.initialMap));
    return cur !== orig;
});

/**
 * @param {Record<string, string>} selectMap
 */
function toPayloadMap(selectMap) {
    const out = {};
    for (const k of BREED_ELEMENT_KEYS) {
        const v = selectMap[k];
        out[k] = v === "" || v == null ? null : String(v);
    }
    return out;
}

const save = () => {
    processing.value = true;
    router.patch(
        route("entities.breeds.update", { breed: props.breedId }),
        { element_orientations: toPayloadMap(localOrientations) },
        {
            preserveScroll: true,
            onSuccess: () => {
                notificationStore.success("Orientations enregistrées.", {
                    duration: 2500,
                    placement: "top-right",
                });
            },
            onError: (errors) => {
                notificationStore.error("Erreur lors de l’enregistrement des orientations.", {
                    duration: 4000,
                    placement: "top-center",
                });
                warnDev("[BreedElementOrientationsEditor]", errors);
            },
            onFinish: () => {
                processing.value = false;
            },
        }
    );
};
</script>

<template>
    <Container class="rounded-box border border-base-300 bg-base-200/30 p-4 space-y-3">
        <div>
            <h3 class="text-lg font-semibold">Voix élémentaires et orientations</h3>
            <p class="text-sm text-base-content/70 mt-1 max-w-3xl">
                Pour chaque voix (air, terre, feu, eau), choisissez une orientation. Les icônes proviennent du dossier
                breed_orientations.
            </p>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <label
                v-for="el in BREED_ELEMENT_KEYS"
                :key="el"
                class="form-control w-full"
            >
                <span class="label-text text-sm font-medium">{{ BREED_ELEMENT_LABELS[el] }}</span>
                <select
                    v-model="localOrientations[el]"
                    class="select select-bordered select-sm w-full"
                >
                    <option :value="null">— Non défini —</option>
                    <option
                        v-for="opt in orientationOptions"
                        :key="opt.value"
                        :value="opt.value"
                    >
                        {{ opt.label }}
                    </option>
                </select>
            </label>
        </div>

        <div class="flex justify-end border-t border-base-300 pt-2">
            <EditActionDock
                primary-label="Enregistrer les orientations"
                processing-label="Enregistrement…"
                :processing="processing"
                :disabled="!hasChanges"
                :show-secondary="false"
                :secondary-actions="[]"
                :fixed-on-desktop="false"
                @primary="save"
            />
        </div>
    </Container>
</template>
