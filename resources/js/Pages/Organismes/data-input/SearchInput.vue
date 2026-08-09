<script setup>
/**
 * SearchInput — Recherche globale header (overlay modal, filtres, résultats groupés).
 *
 * @description
 * Au focus : assombrissement + flou de la page, champ élargi, filtres type DaisyUI (EntityLabel),
 * résultats groupés par type avec extrait (titre + subtitle). API `api.global-search`.
 * Overlay via `<dialog showModal>` (top layer) pour rester au-dessus des modals ouverts.
 *
 * @props {String} placeholder - Placeholder du champ (défaut: 'Rechercher')
 * @props {String} shortcut - Raccourci clavier pour focus (défaut: 'alt+k')
 * @emits update:modelValue
 *
 * @see useGlobalEntitySearch, EntityLabel, InputField
 */
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from "vue";
import { getCommonProps } from "@/Utils/atomic-design/uiHelper";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Kbd from "@/Pages/Atoms/data-display/Kbd.vue";
import EntityLabel from "@/Pages/Atoms/data-display/EntityLabel.vue";
import GlobalSearchHitRow from "@/Pages/Molecules/entity/shared/GlobalSearchHitRow.vue";
import EntityModal from "@/Pages/Organismes/entity/EntityModal.vue";
import {
    useGlobalEntitySearch,
    GLOBAL_SEARCH_TYPE_FILTERS,
    GLOBAL_SEARCH_STATE_FILTERS,
} from "@/Composables/entity/useGlobalEntitySearch";
import { useOpenEntityModal } from "@/Composables/entity/useOpenEntityModal";
import { globalSearchEntityLabelKey } from "@/Utils/entity/globalSearchEntityLabel";

/** @typedef {InstanceType<typeof InputField> & { focus?: () => void }} SearchInputFieldRef */

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
const searchDialogRef = ref(null);
/** @type {import('vue').Ref<SearchInputFieldRef|null>} */
const searchInputRef = ref(null);
/** @type {number} Dernier keystroke dans le champ (évite de voler le focus Tab vers les filtres). */
let lastSearchInputAt = 0;

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

const {
    modalOpen,
    modalEntity,
    modalEntityType,
    openHit,
    closeModal,
} = useOpenEntityModal();

const showPanel = computed(
    () => isFocused.value && isOpen.value && query.value.trim().length >= 2
);

const shortcutLabel = computed(() => {
    const parts = props.shortcut.split("+");
    if (parts.length !== 2) {
        return props.shortcut.toUpperCase();
    }
    return `${parts[0].trim().toUpperCase()} + ${parts[1].trim().toUpperCase()}`;
});

const showClearButton = computed(() => query.value.trim().length > 0);

/** @type {boolean} Clic backdrop : pointerdown et pointerup sur le dialog (pas sur le panneau). */
let backdropPointerDown = false;

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
    lastSearchInputAt = performance.now();
    emit("update:modelValue", value);
    setQuery(value);
};

const handleSelectResult = (result) => {
    openHit(result, { onBeforeOpen: () => blurSearch() });
};

const focusSearchInput = () => {
    searchInputRef.value?.focus?.();
};

const openSearch = () => {
    isFocused.value = true;
};

const blurSearch = () => {
    lastSearchInputAt = 0;
    isFocused.value = false;
    close();
};

const onSearchInputFocus = () => {
    lastSearchInputAt = performance.now();
};

/**
 * Évite que le focus saute sur les filtres après une mise à jour DOM (dialog / résultats).
 */
const restoreSearchInputFocusIfNeeded = () => {
    if (!isFocused.value || performance.now() - lastSearchInputAt > 750) {
        return;
    }

    const active = document.activeElement;
    if (active instanceof HTMLElement && active.closest("[data-global-search-filter]")) {
        focusSearchInput();
    }
};

const onDialogPointerDown = (event) => {
    backdropPointerDown = event.target === searchDialogRef.value;
};

const onDialogPointerUp = (event) => {
    if (backdropPointerDown && event.target === searchDialogRef.value) {
        blurSearch();
    }
    backdropPointerDown = false;
};

const clearSearch = () => {
    setQuery("");
    emit("update:modelValue", "");
    lastSearchInputAt = performance.now();
    focusSearchInput();
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
        openSearch();
    }
};

async function openSearchDialog() {
    await nextTick();
    const dialog = searchDialogRef.value;
    if (!dialog || dialog.open) {
        focusSearchInput();
        return;
    }

    dialog.showModal();
    await nextTick();
    focusSearchInput();
}

onMounted(() => {
    window.addEventListener("keydown", handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener("keydown", handleKeydown);
    document.body.classList.remove("overflow-hidden");
    searchDialogRef.value?.close?.();
});

watch(isFocused, (active) => {
    document.body.classList.toggle("overflow-hidden", active);
    if (active) {
        void openSearchDialog();
    }
}, { flush: "post" });

watch([loading, groupedResults], () => {
    if (!isFocused.value) {
        return;
    }
    nextTick(() => {
        restoreSearchInputFocusIfNeeded();
    });
});

</script>

<template>
    <div
        v-if="!isFocused"
        class="global-search-root global-search-root--compact relative"
    >
        <div class="global-search-compact-shell border-glass-sm rounded-box bd-blur-sm">
            <InputField
                :id="searchBarId"
                :model-value="query"
                :placeholder="props.placeholder"
                type="search"
                :input-style="{ variant: 'glass', size: 'sm', color: 'neutral' }"
                class="global-search-input global-search-input--compact w-full"
                @update:model-value="handleInput"
                @focus="openSearch"
            >
                <template #labelInEnd>
                    <Icon
                        source="fa-magnifying-glass"
                        alt=""
                        size="sm"
                        pack="solid"
                        class="opacity-40"
                    />
                </template>
                <template #overEnd>
                    <div class="global-search-input-end flex items-center gap-1.5">
                        <button
                            v-if="showClearButton"
                            type="button"
                            class="btn btn-ghost btn-xs btn-circle opacity-60 hover:opacity-100"
                            aria-label="Effacer la recherche"
                            @mousedown.prevent
                            @click="clearSearch"
                        >
                            <Icon source="fa-xmark" pack="solid" size="sm" alt="" />
                        </button>
                        <Kbd
                            size="xs"
                            class="global-search-shortcut pointer-events-none opacity-50"
                            aria-hidden="true"
                        >
                            {{ shortcutLabel }}
                        </Kbd>
                    </div>
                </template>
            </InputField>
        </div>
    </div>

    <Teleport to="body">
        <dialog
            v-if="isFocused"
            ref="searchDialogRef"
            class="global-search-dialog"
            aria-label="Recherche globale"
            @cancel.prevent="blurSearch"
            @pointerdown="onDialogPointerDown"
            @pointerup="onDialogPointerUp"
        >
            <div
                ref="panelRef"
                class="global-search-root global-search-root--active"
                @click.stop
            >
                <div class="global-search-panel global-search-panel--expanded flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <InputField
                            ref="searchInputRef"
                            :id="searchBarId"
                            :model-value="query"
                            :placeholder="props.placeholder"
                            class="global-search-input w-full"
                            autofocus
                            @update:model-value="handleInput"
                            @focus="onSearchInputFocus"
                        >
                            <template #overEnd>
                                <button
                                    v-if="showClearButton"
                                    type="button"
                                    class="btn btn-ghost btn-xs btn-circle opacity-70 hover:opacity-100"
                                    aria-label="Effacer la recherche"
                                    @mousedown.prevent
                                    @click="clearSearch"
                                >
                                    <Icon source="fa-xmark" pack="solid" size="sm" alt="" />
                                </button>
                            </template>
                        </InputField>
                    </div>

                    <div class="flex flex-col gap-3">
                        <div class="filter flex flex-wrap gap-1">
                            <button
                                v-for="opt in GLOBAL_SEARCH_TYPE_FILTERS"
                                :key="opt.value"
                                type="button"
                                data-global-search-filter
                                class="btn btn-xs h-auto min-h-8 border border-base-300/80 py-1 px-2 transition-all"
                                :class="
                                    isTypeActive(opt.value)
                                        ? 'opacity-100'
                                        : 'grayscale opacity-55 hover:opacity-75'
                                "
                                :aria-pressed="isTypeActive(opt.value)"
                                @mousedown.prevent
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
                                data-global-search-filter
                                class="btn btn-xs"
                                :class="
                                    isStateActive(opt.value)
                                        ? 'btn-primary'
                                        : 'btn-ghost border border-base-300/80 opacity-60'
                                "
                                :aria-pressed="isStateActive(opt.value)"
                                @mousedown.prevent
                                @click="toggleState(opt.value)"
                            >
                                {{ opt.label }}
                            </button>
                        </div>

                        <p class="text-[10px] leading-snug text-base-content/50">
                            Types et états : aucune sélection = tout inclure ; sélection mixte = union des choix.
                        </p>
                    </div>

                    <Transition name="global-search-results">
                        <div
                            v-if="showPanel"
                            class="global-search-results max-h-[min(60vh,28rem)] overflow-y-auto rounded-box border border-base-300 bg-base-100/95 shadow-2xl backdrop-blur-md"
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
                                            <GlobalSearchHitRow
                                                :hit="result"
                                                density="default"
                                                @select="handleSelectResult"
                                            />
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
        </dialog>
    </Teleport>

    <EntityModal
        v-if="modalEntity"
        :entity="modalEntity"
        :entity-type="modalEntityType"
        view="full"
        :open="modalOpen"
        :use-stored-format="false"
        @close="closeModal"
    />
</template>

<style scoped>
.global-search-dialog {
    position: fixed;
    inset: 0;
    margin: 0;
    height: 100%;
    max-height: none;
    width: 100%;
    max-width: none;
    border: 0;
    padding: 0;
    overflow: visible;
    background: transparent;
    color: inherit;
}

.global-search-dialog::backdrop {
    background-color: rgb(0 0 0 / 45%);
    backdrop-filter: blur(4px);
}

.global-search-root--compact {
    width: 22rem;
    max-width: calc(100vw - 10rem);
}

.global-search-compact-shell {
    --color: var(--color-base-content);
    position: relative;
    width: 100%;
    background: transparent;
    opacity: 0.72;
    transition: opacity 0.2s ease;
    border-radius: var(--radius-box);
    overflow: hidden;
}

.global-search-compact-shell::before {
    opacity: 0.28;
}

.global-search-compact-shell:hover,
.global-search-compact-shell:focus-within {
    opacity: 0.96;
}

.global-search-compact-shell:hover::before,
.global-search-compact-shell:focus-within::before {
    opacity: 0.52;
}

.global-search-root--active {
    position: fixed;
    left: 50%;
    top: 1rem;
    z-index: 1260;
    width: min(48rem, calc(100vw - 2rem));
    transform: translateX(-50%);
}

.global-search-input--compact :deep(.input),
.global-search-input--compact :deep(input) {
    min-height: 2.35rem;
    padding-right: 6.75rem;
    padding-left: 0.85rem;
    border-radius: var(--radius-field);
    background-color: transparent !important;
    border-color: transparent !important;
    box-shadow: none !important;
    color: color-mix(in oklch, var(--color-base-content) 75%, transparent);
}

.global-search-input--compact :deep(.input)::placeholder,
.global-search-input--compact :deep(input)::placeholder {
    color: color-mix(in oklch, var(--color-base-content) 38%, transparent);
}

.global-search-input--compact :deep(.input):hover,
.global-search-input--compact :deep(input):hover,
.global-search-input--compact :deep(.input):focus,
.global-search-input--compact :deep(input):focus {
    background-color: transparent !important;
    border-color: transparent !important;
    box-shadow: none !important;
}

.global-search-input-end {
    position: absolute;
    right: 0.65rem;
    top: 50%;
    transform: translateY(-50%);
}

.global-search-shortcut :deep(kbd) {
    font-size: 0.65rem;
    letter-spacing: 0.04em;
}

.global-search-panel--expanded .global-search-input :deep(.input),
.global-search-panel--expanded .global-search-input :deep(input) {
    font-size: 1.05rem;
    min-height: 3rem;
    padding-right: 2.5rem;
    border-radius: var(--radius-field);
}

.global-search-results-enter-active,
.global-search-results-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.global-search-results-enter-from,
.global-search-results-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}

.global-search-excerpt {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
    overflow: hidden;
}
</style>
