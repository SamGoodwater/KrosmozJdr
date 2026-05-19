<script setup>
/**
 * SearchInput — Recherche globale header (overlay modal, filtres, résultats groupés).
 *
 * @description
 * Au focus : assombrissement + flou de la page, champ élargi, filtres type DaisyUI (EntityLabel),
 * résultats groupés par type avec extrait (titre + subtitle). API `api.global-search`.
 *
 * @props {String} placeholder - Placeholder du champ (défaut: 'Rechercher')
 * @props {String} shortcut - Raccourci clavier pour focus (défaut: 'alt+k')
 * @emits update:modelValue
 *
 * @see useGlobalEntitySearch, EntityLabel, InputField
 */
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import { getCommonProps } from "@/Utils/atomic-design/uiHelper";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Kbd from "@/Pages/Atoms/data-display/Kbd.vue";
import EntityLabel from "@/Pages/Atoms/data-display/EntityLabel.vue";
import {
    useGlobalEntitySearch,
    GLOBAL_SEARCH_TYPE_FILTERS,
    GLOBAL_SEARCH_STATE_FILTERS,
} from "@/Composables/entity/useGlobalEntitySearch";
import { globalSearchEntityLabelKey } from "@/Utils/entity/globalSearchEntityLabel";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    ...getCommonProps(),
    placeholder: {
        type: String,
        default: "Rechercher",
    },
    shortcut: {
        type: String,
        default: "alt+k",
    },
});

const emit = defineEmits(["update:modelValue"]);

const searchBarId = ref(`searchBar-${Date.now()}-${Math.random().toString(36).substring(2, 11)}`);
const isFocused = ref(false);
const panelRef = ref(null);

const selectedTypes = ref([]);
const selectedStates = ref([]);

const {
    query,
    groupedResults,
    loading,
    loadingMore,
    error,
    isOpen,
    hasResults,
    hasMore,
    setQuery,
    loadMore,
    close,
} = useGlobalEntitySearch({ selectedTypes, selectedStates });

const showPanel = computed(
    () => isFocused.value && isOpen.value && query.value.trim().length >= 2
);

const isTypeActive = (value) =>
    selectedTypes.value.length === 0 || selectedTypes.value.includes(value);

const isStateActive = (value) =>
    selectedStates.value.length === 0 || selectedStates.value.includes(value);

/**
 * @param {string} value
 */
function toggleType(value) {
    const set = new Set(selectedTypes.value);
    if (set.has(value)) {
        set.delete(value);
    } else {
        set.add(value);
    }
    selectedTypes.value = [...set];
}

/**
 * @param {string} value
 */
function toggleState(value) {
    const set = new Set(selectedStates.value);
    if (set.has(value)) {
        set.delete(value);
    } else {
        set.add(value);
    }
    selectedStates.value = [...set];
}

const handleInput = (value) => {
    emit("update:modelValue", value);
    setQuery(value);
};

const handleSelectResult = (result) => {
    if (result?.href) {
        router.visit(result.href);
    }
    blurSearch();
};

const focusSearch = () => {
    isFocused.value = true;
    document.getElementById(searchBarId.value)?.focus();
};

const blurSearch = () => {
    isFocused.value = false;
    close();
};

const onBackdropPointerDown = (event) => {
    if (panelRef.value?.contains(event.target)) {
        return;
    }
    blurSearch();
};

const handleKeydown = (event) => {
    if (event.key === "Escape" && isFocused.value) {
        event.preventDefault();
        blurSearch();
        return;
    }

    const [modifier, key] = props.shortcut.split("+");
    if (
        event[`${modifier}Key`] &&
        event.key.toLowerCase() === key.toLowerCase()
    ) {
        event.preventDefault();
        focusSearch();
    }
};

onMounted(() => {
    window.addEventListener("keydown", handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener("keydown", handleKeydown);
    document.body.classList.remove("overflow-hidden");
});

watch(isFocused, (active) => {
    document.body.classList.toggle("overflow-hidden", active);
});
</script>

<template>
    <Teleport to="body">
        <Transition name="global-search-backdrop">
            <button
                v-if="isFocused"
                type="button"
                class="global-search-backdrop fixed inset-0 z-[90] cursor-default border-0 bg-black/45 backdrop-blur-sm"
                aria-label="Fermer la recherche"
                @pointerdown="onBackdropPointerDown"
            />
        </Transition>
    </Teleport>

    <div
        ref="panelRef"
        class="global-search-root relative w-full"
        :class="{ 'global-search-root--active': isFocused }"
    >
        <div
            class="global-search-panel flex flex-col gap-2"
            :class="{ 'global-search-panel--expanded': isFocused }"
        >
            <div class="flex items-center gap-2">
                <InputField
                    :id="searchBarId"
                    :model-value="query"
                    :placeholder="props.placeholder"
                    class="global-search-input w-full"
                    @update:model-value="handleInput"
                    @focus="isFocused = true"
                >
                    <template #labelInEnd>
                        <Icon
                            source="fa-magnifying-glass"
                            alt="Rechercher"
                            size="md"
                            pack="solid"
                            class="opacity-70"
                        />
                    </template>
                </InputField>
                <Kbd v-if="!isFocused" size="sm" class="shrink-0">{{ props.shortcut }}</Kbd>
            </div>

            <Transition name="global-search-filters">
                <div v-if="isFocused" class="flex flex-col gap-3">
                    <div class="filter flex flex-wrap gap-1">
                        <button
                            v-for="opt in GLOBAL_SEARCH_TYPE_FILTERS"
                            :key="opt.value"
                            type="button"
                            class="btn btn-xs h-auto min-h-8 border border-base-300/80 py-1 px-2 transition-all"
                            :class="
                                isTypeActive(opt.value)
                                    ? 'opacity-100'
                                    : 'grayscale opacity-55 hover:opacity-75'
                            "
                            :aria-pressed="isTypeActive(opt.value)"
                            @click="toggleType(opt.value)"
                        >
                            <EntityLabel
                                :entity="globalSearchEntityLabelKey(opt.value)"
                                variant="icon-inline"
                                size="xs"
                                :label="opt.label"
                            />
                        </button>
                    </div>

                    <div class="filter flex flex-wrap gap-1">
                        <button
                            v-for="opt in GLOBAL_SEARCH_STATE_FILTERS"
                            :key="opt.value"
                            type="button"
                            class="btn btn-xs"
                            :class="
                                isStateActive(opt.value)
                                    ? 'btn-primary'
                                    : 'btn-ghost border border-base-300/80 opacity-60'
                            "
                            :aria-pressed="isStateActive(opt.value)"
                            @click="toggleState(opt.value)"
                        >
                            {{ opt.label }}
                        </button>
                    </div>

                    <p class="text-[10px] leading-snug text-base-content/50">
                        Types et états : aucune sélection = tout inclure ; sélection mixte = union des choix.
                    </p>
                </div>
            </Transition>

            <Transition name="global-search-results">
                <div
                    v-if="showPanel"
                    class="global-search-results max-h-[min(60vh,28rem)] overflow-y-auto rounded-xl border border-base-300 bg-base-100/95 shadow-2xl backdrop-blur-md"
                >
                    <div v-if="loading && !hasResults" class="px-4 py-3 text-sm text-base-content/60">
                        Chargement…
                    </div>
                    <p v-else-if="error" class="px-4 py-3 text-sm text-error">
                        {{ error }}
                    </p>

                    <template v-else-if="hasResults">
                        <section
                            v-for="group in groupedResults"
                            :key="group.entityType"
                            class="border-b border-base-200 last:border-b-0"
                        >
                            <header
                                class="sticky top-0 z-[1] flex items-center gap-2 border-b border-base-200/80 bg-base-200/80 px-3 py-2 backdrop-blur-sm"
                            >
                                <EntityLabel
                                    :entity="group.labelKey"
                                    variant="icon-inline"
                                    size="xs"
                                    :label="group.group"
                                />
                                <span class="text-[11px] text-base-content/50">
                                    {{ group.items.length }}
                                </span>
                            </header>
                            <ul>
                                <li
                                    v-for="result in group.items"
                                    :key="`${result.entityType}-${result.id}`"
                                >
                                    <button
                                        type="button"
                                        class="global-search-hit w-full px-3 py-2.5 text-left transition-colors hover:bg-base-200/70"
                                        @click="handleSelectResult(result)"
                                    >
                                        <span class="block text-sm font-semibold leading-snug text-base-content">
                                            {{ result.title }}
                                        </span>
                                        <span
                                            v-if="result.subtitle"
                                            class="global-search-excerpt mt-1 block text-xs leading-relaxed text-base-content/75"
                                        >
                                            {{ result.subtitle }}
                                        </span>
                                    </button>
                                </li>
                            </ul>
                        </section>

                        <div
                            v-if="hasMore"
                            class="border-t border-base-200 p-2 text-center"
                        >
                            <button
                                type="button"
                                class="btn btn-ghost btn-sm w-full"
                                :disabled="loadingMore"
                                @click="loadMore"
                            >
                                {{ loadingMore ? "Chargement…" : "Afficher plus de résultats" }}
                            </button>
                        </div>
                    </template>

                    <div
                        v-else-if="!loading"
                        class="px-4 py-3 text-sm text-base-content/60"
                    >
                        Aucun résultat.
                    </div>
                </div>
            </Transition>
        </div>
    </div>
</template>

<style scoped>
.global-search-root--active {
    position: fixed;
    left: 50%;
    top: 1rem;
    z-index: 100;
    width: min(48rem, calc(100vw - 2rem));
    transform: translateX(-50%);
}

.global-search-panel--expanded .global-search-input :deep(.input),
.global-search-panel--expanded .global-search-input :deep(input) {
    font-size: 1.05rem;
    min-height: 3rem;
}

.global-search-backdrop-enter-active,
.global-search-backdrop-leave-active,
.global-search-filters-enter-active,
.global-search-filters-leave-active,
.global-search-results-enter-active,
.global-search-results-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.global-search-backdrop-enter-from,
.global-search-backdrop-leave-to,
.global-search-filters-enter-from,
.global-search-filters-leave-to,
.global-search-results-enter-from,
.global-search-results-leave-to {
    opacity: 0;
}

.global-search-filters-enter-from,
.global-search-filters-leave-to,
.global-search-results-enter-from,
.global-search-results-leave-to {
    transform: translateY(-4px);
}

.global-search-excerpt {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
    overflow: hidden;
}
</style>
