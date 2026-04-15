<script setup>
/**
 * Catalogue public de chartes : liste via API norms-catalog, détail chargé à l’ouverture d’un panneau.
 */
import { computed, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue';
import NormsViewer from '@/Pages/Organismes/data-display/NormsViewer.vue';
import axios from 'axios';

const props = defineProps({
    section: { type: Object, required: true },
    data: { type: Object, default: () => ({}) },
    settings: { type: Object, default: () => ({}) },
});

const catalogLoading = ref(false);
const catalogError = ref(null);
const catalogItems = ref([]);

const group = computed(() => props.settings?.group || 'spell');
const entity = computed(() => props.settings?.entity ?? '*');

/** @type {import('vue').ComputedRef<string[]>} */
const keysFilter = computed(() => {
    const raw = props.settings?.characteristic_keys;
    if (Array.isArray(raw) && raw.length > 0) {
        return raw.map((k) => String(k).trim()).filter(Boolean);
    }
    return [];
});

const normsByKey = reactive({});
const loadingDetail = reactive({});
const errorDetail = reactive({});
const searchTerm = ref('');
const hasScrolled = ref(false);
const isSearchHovered = ref(false);
const isSearchFocused = ref(false);
const searchInputRef = ref(null);
const SEARCH_STORAGE_KEY = 'norms_catalog_search_term';

const catalogUrl = computed(() => {
    const g = encodeURIComponent(group.value);
    const e = entity.value;
    if (!e || e === '*') {
        return `/api/characteristics/norms-catalog/${g}`;
    }
    return `/api/characteristics/norms-catalog/${g}/${encodeURIComponent(e)}`;
});

async function fetchCatalog() {
    catalogLoading.value = true;
    catalogError.value = null;
    try {
        const params = {};
        if (keysFilter.value.length > 0) {
            params.keys = keysFilter.value.join(',');
        }
        const { data } = await axios.get(catalogUrl.value, { params });
        catalogItems.value = Array.isArray(data.items) ? data.items : [];
    } catch (e) {
        catalogError.value = e.response?.data?.error || 'Impossible de charger le catalogue de chartes.';
        catalogItems.value = [];
    } finally {
        catalogLoading.value = false;
    }
}

/**
 * @param {Event} event
 * @param {string} key
 */
async function onPanelToggle(event, key) {
    const el = event.target;
    if (!(el instanceof HTMLDetailsElement) || !el.open) {
        return;
    }
    if (normsByKey[key] || loadingDetail[key]) {
        return;
    }
    loadingDetail[key] = true;
    errorDetail[key] = null;
    try {
        const url = `/api/characteristics/${encodeURIComponent(key)}/norms/${encodeURIComponent(entity.value)}`;
        const { data } = await axios.get(url);
        normsByKey[key] = data;
    } catch (e) {
        errorDetail[key] = e.response?.data?.error || 'Erreur lors du chargement des normes.';
    } finally {
        loadingDetail[key] = false;
    }
}

const filteredItems = computed(() => {
    const term = searchTerm.value.trim().toLowerCase();
    if (!term) {
        return catalogItems.value;
    }
    return catalogItems.value.filter((item) => {
        const key = String(item?.key || '').toLowerCase();
        const name = String(item?.name || '').toLowerCase();
        return key.includes(term) || name.includes(term);
    });
});

const shouldCompactSearch = computed(() => hasScrolled.value && !isSearchHovered.value && !isSearchFocused.value);

function handleScroll() {
    hasScrolled.value = window.scrollY > 180;
}

function clearSearch() {
    searchTerm.value = '';
    if (searchInputRef.value) {
        searchInputRef.value.focus();
    }
}

function handleGlobalSearchShortcut(event) {
    if (event.key !== '/') {
        return;
    }

    const target = event.target;
    const tagName = target?.tagName?.toLowerCase?.() || '';
    const isTypingContext = tagName === 'input'
        || tagName === 'textarea'
        || target?.isContentEditable;

    if (isTypingContext) {
        return;
    }

    event.preventDefault();
    if (searchInputRef.value) {
        searchInputRef.value.focus();
    }
}

onMounted(fetchCatalog);
onMounted(() => {
    window.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('keydown', handleGlobalSearchShortcut);
    handleScroll();

    try {
        const stored = sessionStorage.getItem(SEARCH_STORAGE_KEY);
        if (typeof stored === 'string' && stored.trim() !== '') {
            searchTerm.value = stored;
        }
    } catch {
        // no-op: storage indisponible
    }
});
onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('keydown', handleGlobalSearchShortcut);
});
watch([group, entity, keysFilter], fetchCatalog);
watch(searchTerm, (value) => {
    try {
        if (!value) {
            sessionStorage.removeItem(SEARCH_STORAGE_KEY);
            return;
        }
        sessionStorage.setItem(SEARCH_STORAGE_KEY, value);
    } catch {
        // no-op: storage indisponible
    }
});
</script>

<template>
    <div class="section-characteristic-norms-catalog space-y-3">
        <div class="sticky top-2 z-20 flex justify-end pointer-events-none">
            <div
                class="pointer-events-auto transition-all duration-300"
                :class="shouldCompactSearch ? 'w-10' : 'w-full max-w-md'"
                @mouseenter="isSearchHovered = true"
                @mouseleave="isSearchHovered = false"
            >
                <label
                    class="input input-sm input-bordered flex items-center gap-2 bg-base-100/90 backdrop-blur border-base-300"
                    :class="shouldCompactSearch ? 'justify-center px-0' : ''"
                >
                    <i class="fa-solid fa-magnifying-glass text-base-content/60" />
                    <input
                        ref="searchInputRef"
                        v-model="searchTerm"
                        type="text"
                        placeholder="Rechercher une caractéristique..."
                        class="grow text-sm"
                        :class="shouldCompactSearch ? 'hidden' : ''"
                        @focus="isSearchFocused = true"
                        @blur="isSearchFocused = false"
                    />
                    <button
                        v-if="searchTerm && !shouldCompactSearch"
                        type="button"
                        class="btn btn-ghost btn-xs px-1 min-h-0 h-5"
                        title="Effacer la recherche"
                        @click="clearSearch"
                    >
                        <i class="fa-solid fa-xmark" />
                    </button>
                </label>
            </div>
        </div>

        <div v-if="catalogLoading" class="flex items-center justify-center py-10">
            <span class="loading loading-spinner loading-lg text-primary" />
        </div>

        <div v-else-if="catalogError" class="alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation" />
            <span>{{ catalogError }}</span>
        </div>

        <div v-else-if="!catalogItems.length" class="alert alert-info">
            <i class="fa-solid fa-info-circle" />
            <span>Aucune charte disponible pour ce groupe (ou filtre trop restrictif).</span>
        </div>

        <div v-else class="space-y-2">
            <div class="text-xs text-base-content/60 px-1">
                {{ filteredItems.length }} résultat(s)
            </div>
            <details
                v-for="item in filteredItems"
                :key="item.key"
                class="group border border-base-300 rounded-lg bg-base-200/30 open:shadow-sm"
                @toggle="onPanelToggle($event, item.key)"
            >
                <summary
                    class="cursor-pointer list-none px-3 py-2 text-sm font-medium marker:content-none flex flex-wrap items-baseline gap-2 [&::-webkit-details-marker]:hidden"
                >
                    <i class="fa-solid fa-chevron-right text-xs text-base-content/50 transition-transform group-open:rotate-90" />
                    <span class="font-mono text-xs text-base-content/60">{{ item.key }}</span>
                    <span>{{ item.name }}</span>
                </summary>
                <div class="border-t border-base-300 bg-base-100/50">
                    <div v-if="loadingDetail[item.key]" class="flex justify-center py-8">
                        <span class="loading loading-spinner loading-md" />
                    </div>
                    <div v-else-if="errorDetail[item.key]" class="alert alert-warning m-4">
                        {{ errorDetail[item.key] }}
                    </div>
                    <div v-else-if="normsByKey[item.key]?.norms" class="p-4">
                        <NormsViewer
                            :grid="normsByKey[item.key].norms.grid"
                            :conditions="normsByKey[item.key].norms.conditions || []"
                            :description="normsByKey[item.key].norms.description || ''"
                            :min-limit="normsByKey[item.key].norms.limits?.min ?? null"
                            :max-limit="normsByKey[item.key].norms.limits?.max ?? null"
                            :characteristic-name="normsByKey[item.key].characteristic?.name || ''"
                            :characteristic-color="normsByKey[item.key].characteristic?.color || '#6366f1'"
                            :available-characteristics="normsByKey[item.key].available_characteristics || {}"
                        />
                    </div>
                </div>
            </details>
        </div>
    </div>
</template>
