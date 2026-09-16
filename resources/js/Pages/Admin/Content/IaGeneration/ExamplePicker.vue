<script setup>
/**
 * Sélecteur d’étalons few-shot pour un type d’entité (admin IA métier).
 *
 * Recherche via `api.tables.{type}` (`useEntitySearch`). Résultats par défaut :
 * état `playable` uniquement. La valeur émise est `official_id` ou le nom,
 * jamais l’id SQL.
 *
 * @example
 * <ExamplePicker entity="item" v-model="form.entities.item.example_ids" />
 * <ExamplePicker entity="panoply" ref-mode="name" v-model="form.entities.item.few_shot_panoplies" />
 */
import { computed, onMounted } from "vue";
import InputCore from "@/Pages/Atoms/data-input/InputCore.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import { useEntitySearch } from "@/Composables/entity/useEntitySearch";
import { normalizeEntityType } from "@/Entities/entity-registry";
import {
    IA_EXAMPLE_STATE_FILTER,
    entityDisplayName,
    portableEntityRef,
} from "@/Utils/entity/portableEntityRef";
import { resolveEntityImageUrl, resolveEntityThumbLabel } from "@/Utils/entity/entityThumb";

const props = defineProps({
    entity: { type: String, required: true },
    modelValue: { type: Array, default: () => [] },
    title: { type: String, default: "Fiches exemples (playable)" },
    helper: {
        type: String,
        default:
            "Recherche parmi les fiches jouables du type. On stocke l’identifiant officiel ou le nom (pas l’id SQL). Clique une ligne pour ajouter ou retirer.",
    },
    searchPlaceholder: { type: String, default: "Rechercher un étalon…" },
    searchAriaLabel: { type: String, default: "Rechercher un étalon" },
    refMode: { type: String, default: "portable" },
});

const emit = defineEmits(["update:modelValue"]);

const tableType = computed(() => normalizeEntityType(props.entity) || props.entity);

const { query, results, loading, error, search } = useEntitySearch({
    entityType: tableType.value,
    initialFilters: { ...IA_EXAMPLE_STATE_FILTER },
    initialSort: "id",
    initialOrder: "asc",
    limit: 20,
    debounce: 250,
});

onMounted(() => {
    search();
});

const selectedRefs = computed(() =>
    (Array.isArray(props.modelValue) ? props.modelValue : [])
        .map((ref) => String(ref).trim())
        .filter(Boolean),
);

const resultsByRef = computed(() => {
    const map = new Map();
    for (const entity of results.value || []) {
        const ref = entityRef(entity);
        if (ref) {
            map.set(String(ref), entity);
        }
    }
    return map;
});

/**
 * Ref persistée : official_id/nom (`portable`) ou nom seul (`name`, panoplies).
 *
 * @param {object} entity
 * @returns {string|null}
 */
function entityRef(entity) {
    if (props.refMode === "name") {
        const name = entityDisplayName(entity);
        return name !== "" ? name : portableEntityRef(entity);
    }
    return portableEntityRef(entity);
}

/**
 * @param {object} entity
 * @returns {boolean}
 */
function isSelected(entity) {
    const ref = entityRef(entity);
    return ref != null && selectedRefs.value.includes(String(ref));
}

/**
 * @param {string} ref
 * @returns {object|null}
 */
function entityForRef(ref) {
    return resultsByRef.value.get(String(ref)) || null;
}

/**
 * @param {string} ref
 * @returns {string}
 */
function chipLabel(ref) {
    const entity = entityForRef(ref);
    if (entity) {
        return entityDisplayName(entity) || String(ref);
    }
    return String(ref);
}

/**
 * @param {object} entity
 */
function toggleEntity(entity) {
    const ref = entityRef(entity);
    if (ref == null) {
        return;
    }
    if (selectedRefs.value.includes(String(ref))) {
        removeRef(ref);
        return;
    }
    emit("update:modelValue", [...selectedRefs.value, ref]);
}

/**
 * @param {string} ref
 */
function removeRef(ref) {
    emit(
        "update:modelValue",
        selectedRefs.value.filter((item) => item !== String(ref)),
    );
}
</script>

<template>
    <div class="space-y-3">
        <div>
            <p class="text-sm font-medium text-base-content">{{ title }}</p>
            <p class="mt-1 text-xs text-base-content/60">{{ helper }}</p>
        </div>

        <div
            v-if="selectedRefs.length"
            class="flex flex-wrap gap-2"
            data-testid="ia-example-selected"
        >
            <span
                v-for="ref in selectedRefs"
                :key="ref"
                class="inline-flex max-w-full items-center gap-1 rounded-box border border-base-300 bg-base-200/70 py-1 pl-2 pr-1 text-sm"
            >
                <EntityThumb
                    v-if="entityForRef(ref)"
                    size="compact"
                    :src="resolveEntityImageUrl(entityForRef(ref))"
                    :label="resolveEntityThumbLabel(chipLabel(ref))"
                />
                <span class="min-w-0 truncate" :title="ref">{{ chipLabel(ref) }}</span>
                <Btn
                    type="button"
                    variant="ghost"
                    size="xs"
                    circle
                    :aria-label="`Retirer ${chipLabel(ref)}`"
                    @click="removeRef(ref)"
                >
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </Btn>
            </span>
        </div>
        <p v-else class="text-sm text-base-content/60">Aucun étalon pour l’instant.</p>

        <div class="space-y-2">
            <InputCore
                type="search"
                variant="glass"
                color="primary"
                size="sm"
                class="w-full"
                :placeholder="searchPlaceholder"
                :aria-label="searchAriaLabel"
                :model-value="query"
                @update:model-value="(value) => (query = value)"
            />
            <p class="text-xs text-base-content/60">
                <Badge size="xs" variant="outline">Jouable</Badge>
                Filtre par défaut — tu peux chercher par nom dans ce vivier.
            </p>
        </div>

        <div
            class="max-h-72 overflow-y-auto divide-y divide-base-200 rounded-box border border-base-300 bg-base-100/60"
            data-testid="ia-example-results"
        >
            <button
                v-for="row in results"
                :key="String(row.id)"
                type="button"
                class="flex w-full items-center justify-between gap-2.5 px-3 py-2 text-left text-sm transition-colors hover:bg-base-200/80"
                :class="isSelected(row) ? 'bg-primary/10' : ''"
                :disabled="!entityRef(row)"
                @click="toggleEntity(row)"
            >
                <EntityThumb
                    size="compact"
                    :src="resolveEntityImageUrl(row)"
                    :label="resolveEntityThumbLabel(entityDisplayName(row) || `#${row.id}`)"
                />
                <div class="flex min-w-0 flex-1 flex-col items-start gap-0.5">
                    <span class="w-full truncate font-medium">
                        {{ entityDisplayName(row) || entityRef(row) || `#${row.id}` }}
                    </span>
                    <span v-if="row.official_id" class="w-full truncate text-xs text-base-content/70">
                        {{ row.official_id }}
                    </span>
                </div>
                <Badge v-if="row.state" size="xs" variant="outline">{{ row.state }}</Badge>
            </button>

            <div
                v-if="loading"
                class="px-3 py-3 text-center text-sm text-base-content/60"
            >
                Chargement…
            </div>
            <div
                v-else-if="error"
                class="px-3 py-3 text-center text-sm text-error"
            >
                Impossible de charger les fiches.
            </div>
            <div
                v-else-if="results.length === 0"
                class="px-3 py-3 text-center text-sm text-base-content/60"
            >
                Aucun résultat jouable.
            </div>
        </div>
    </div>
</template>
