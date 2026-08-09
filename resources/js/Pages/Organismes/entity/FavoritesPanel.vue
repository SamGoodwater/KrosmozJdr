<script setup>
/**
 * FavoritesPanel — Liste des favoris (Minimal) + recherche (hits texte agrandis).
 *
 * @description
 * Clic → modal full. Survol d’un hit recherche → aperçu Minimal.
 * Textes utilisateur sans le mot « entité ».
 */
import { computed, onMounted, ref, watch, markRaw } from "vue";
import { router } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import EntityModal from "@/Pages/Organismes/entity/EntityModal.vue";
import GlobalSearchHitRow from "@/Pages/Molecules/entity/shared/GlobalSearchHitRow.vue";
import {
    FAVORITES_ACCESS_AUTH_REQUIRED_MESSAGE,
    FAVORITES_AUTH_REQUIRED_MESSAGE,
    fetchHydratedFavorites,
    isEntityFavorite,
    toggleEntityFavorite,
    useFavoriteEntityVersion,
} from "@/Composables/entity/useFavoriteEntityIds";
import {
    GLOBAL_SEARCH_TYPE_ORDER,
    useGlobalEntitySearch,
} from "@/Composables/entity/useGlobalEntitySearch";
import { useOpenEntityModal } from "@/Composables/entity/useOpenEntityModal";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useUxFeedback } from "@/Composables/utils/useUxFeedback";
import {
    canOpenEntityModal,
    entityPropNameForType,
    fetchEntityModels,
} from "@/Utils/entity/fetchEntityModel";
import { resolveEntityViewComponentSync } from "@/Utils/entity/resolveEntityViewComponent";
import { normalizeEntityType } from "@/Entities/entity-registry";

const props = defineProps({
    surface: {
        type: String,
        default: "modal",
        validator: (v) => ["modal", "page"].includes(v),
    },
    showOpenPageButton: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["close", "open-page"]);

const { isAuthenticated } = usePermissions();
const { notifySuccess, notifyInfo, notifyError } = useUxFeedback();
const favoriteVersion = useFavoriteEntityVersion();

const {
    modalOpen,
    modalEntity,
    modalEntityType,
    openHit,
    openEntity,
    closeModal,
} = useOpenEntityModal();

const loadingFavorites = ref(false);
const favoriteItems = ref([]);
/** @type {import('vue').Ref<Array<{ entityType: string, model: object, Minimal: object, prop: string }>>} */
const favoriteCards = ref([]);
const loadError = ref(null);

const {
    query,
    setQuery,
    groupedResults,
    loading: searchLoading,
    hasResults,
    clearResults,
} = useGlobalEntitySearch({ minQueryLength: 2, debounce: 250, pageSize: 40 });

const TYPE_LABELS = Object.freeze({
    breeds: "Classes",
    specializations: "Spécialisations",
    monsters: "Monstres",
    spells: "Sorts",
    capabilities: "Capacités",
    "creature-traits": "Traits de créature",
    conditions: "États",
    items: "Équipements",
    consumables: "Consommables",
    resources: "Ressources",
    panoplies: "Panoplies",
    campaigns: "Campagnes",
    scenarios: "Scénarios",
    npcs: "PNJ",
    shops: "Boutiques",
    pages: "Pages",
    sections: "Sections",
    "resource-types": "Types de ressource",
    "item-types": "Types d'équipement",
    "consumable-types": "Types de consommable",
    "spell-types": "Types de sort",
    "monster-races": "Races",
});

const favoriteGroups = computed(() => {
    favoriteVersion.value;
    /** @type {Map<string, typeof favoriteCards.value>} */
    const map = new Map();
    for (const card of favoriteCards.value) {
        const type = card.entityType || "unknown";
        if (!map.has(type)) map.set(type, []);
        map.get(type).push(card);
    }
    // Hits sans modèle (pages…) — affichage fallback
    for (const item of favoriteItems.value) {
        const type = normalizeEntityType(item.entityType);
        if (canOpenEntityModal(type)) continue;
        if (!map.has(type)) map.set(type, []);
        map.get(type).push({ entityType: type, hit: item, Minimal: null, model: null, prop: "" });
    }
    return GLOBAL_SEARCH_TYPE_ORDER.filter((t) => map.has(t))
        .concat([...map.keys()].filter((t) => !GLOBAL_SEARCH_TYPE_ORDER.includes(t)))
        .map((type) => ({
            entityType: type,
            label: TYPE_LABELS[type] || type,
            items: map.get(type) || [],
        }))
        .filter((g) => g.items.length > 0);
});

const showSearchResults = computed(() => String(query.value || "").trim().length >= 2);

function onSearchInput(value) {
    setQuery(value);
}

async function buildFavoriteCards(items) {
    /** @type {Map<string, string[]>} */
    const byType = new Map();
    for (const item of items) {
        const type = normalizeEntityType(item.entityType);
        if (!canOpenEntityModal(type)) continue;
        if (!byType.has(type)) byType.set(type, []);
        byType.get(type).push(String(item.id));
    }

    const cards = [];
    await Promise.all(
        [...byType.entries()].map(async ([type, ids]) => {
            const Minimal = resolveEntityViewComponentSync(type, "minimal");
            if (!Minimal) return;
            const models = await fetchEntityModels(type, ids);
            const prop = entityPropNameForType(type);
            for (const model of models) {
                if (!model?.id) continue;
                cards.push({
                    entityType: type,
                    model,
                    Minimal: markRaw(Minimal),
                    prop,
                });
            }
        }),
    );
    favoriteCards.value = cards;
}

async function reloadFavorites() {
    if (!isAuthenticated.value) {
        favoriteItems.value = [];
        favoriteCards.value = [];
        return;
    }
    loadingFavorites.value = true;
    loadError.value = null;
    try {
        const data = await fetchHydratedFavorites();
        favoriteItems.value = data.items || [];
        await buildFavoriteCards(favoriteItems.value);
    } catch {
        loadError.value = "Impossible de charger vos favoris.";
        favoriteItems.value = [];
        favoriteCards.value = [];
    } finally {
        loadingFavorites.value = false;
    }
}

async function onToggleFavorite(row) {
    if (!isAuthenticated.value) {
        notifyInfo(FAVORITES_AUTH_REQUIRED_MESSAGE);
        return;
    }
    const type = row.entityType;
    const id = row.id;
    const result = await toggleEntityFavorite(type, id, { authenticated: true });
    if (!result.ok) {
        if (result.reason === "auth") notifyInfo(FAVORITES_AUTH_REQUIRED_MESSAGE);
        else notifyError("Impossible de mettre à jour vos favoris.");
        return;
    }
    notifySuccess(result.favorited ? "Ajouté aux favoris" : "Retiré des favoris");
    if (!result.favorited) {
        favoriteItems.value = favoriteItems.value.filter(
            (it) => !(String(it.entityType) === String(type) && String(it.id) === String(id)),
        );
        favoriteCards.value = favoriteCards.value.filter(
            (c) => !(String(c.entityType) === String(type) && String(c.model?.id) === String(id)),
        );
    } else {
        const exists = favoriteItems.value.some(
            (it) => String(it.entityType) === String(type) && String(it.id) === String(id),
        );
        if (!exists) {
            favoriteItems.value = [{ ...row }, ...favoriteItems.value];
        }
        await buildFavoriteCards(favoriteItems.value);
    }
}

function onSelectHit(row) {
    openHit(row);
}

function onQuickViewFromMinimal(entity, entityType) {
    openEntity(entityType, entity);
}

function handleMinimalAction(actionKey, entity, entityType) {
    if (actionKey === "quick-view") {
        onQuickViewFromMinimal(entity, entityType);
    }
}

function openAsPage() {
    emit("open-page");
    emit("close");
    router.visit(route("favorites.index"));
}

function goLogin() {
    emit("close");
    router.visit(route("login"));
}

function isFav(row) {
    favoriteVersion.value;
    return isEntityFavorite(row.entityType, row.id);
}

onMounted(() => {
    reloadFavorites();
});

watch(isAuthenticated, (ok) => {
    if (ok) reloadFavorites();
    else {
        favoriteItems.value = [];
        favoriteCards.value = [];
        clearResults();
    }
});
</script>

<template>
    <div class="favorites-panel flex min-h-0 flex-col gap-4" data-cy="favorites-panel">
        <header class="flex flex-wrap items-start justify-between gap-2">
            <div class="min-w-0">
                <component
                    :is="surface === 'page' ? 'h1' : 'h2'"
                    class="flex items-center gap-2 text-xl font-bold text-primary-100 sm:text-2xl"
                >
                    <Icon source="fa-heart" pack="solid" size="sm" class="text-primary" alt="" />
                    Mes favoris
                </component>
                <p class="mt-1 text-sm text-base-content/70">
                    Retrouvez vos fiches préférées, classées par type.
                </p>
                <Btn
                    v-if="isAuthenticated && showOpenPageButton && surface === 'modal'"
                    class="mt-1 !px-0"
                    size="xs"
                    variant="link"
                    color="neutral"
                    @click="openAsPage"
                >
                    Voir la page favoris
                </Btn>
            </div>
        </header>

        <div
            v-if="!isAuthenticated"
            class="rounded-box border border-primary/30 bg-primary/10 p-4 text-sm text-primary-100"
            role="status"
        >
            <p>{{ FAVORITES_ACCESS_AUTH_REQUIRED_MESSAGE }}</p>
            <Btn class="mt-3" size="sm" color="primary" variant="glass" @click="goLogin">
                Se connecter
            </Btn>
        </div>

        <template v-else>
            <div class="rounded-box border-glass-sm bg-base-200/40 p-2 bd-blur-sm">
                <InputField
                    :model-value="query"
                    type="search"
                    placeholder="Rechercher une fiche…"
                    :input-style="{ variant: 'glass', size: 'sm', color: 'neutral' }"
                    @update:model-value="onSearchInput"
                >
                    <template #labelInEnd>
                        <Icon source="fa-magnifying-glass" pack="solid" size="sm" class="opacity-40" alt="" />
                    </template>
                </InputField>
                <p class="mt-1.5 px-1 text-[11px] text-base-content/55">
                    Les favoris apparaissent en premier. Survolez un résultat pour l’aperçu ; cliquez pour ouvrir la fiche.
                </p>
            </div>

            <div v-if="showSearchResults" class="min-h-0 flex-1 space-y-3 overflow-y-auto">
                <p v-if="searchLoading" class="text-sm text-base-content/60">Recherche…</p>
                <p v-else-if="!hasResults" class="text-sm text-base-content/60">Aucun résultat.</p>
                <section
                    v-for="group in groupedResults"
                    :key="group.entityType"
                    class="space-y-1"
                >
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300/90">
                        {{ TYPE_LABELS[group.entityType] || group.group || group.entityType }}
                    </h3>
                    <ul class="space-y-0.5">
                        <li
                            v-for="row in group.items"
                            :key="`${row.entityType}:${row.id}`"
                        >
                            <GlobalSearchHitRow
                                :hit="row"
                                density="lg"
                                :show-favorite-toggle="true"
                                :is-favorite="isFav(row)"
                                @select="onSelectHit"
                                @toggle-favorite="onToggleFavorite"
                            />
                        </li>
                    </ul>
                </section>
            </div>

            <div v-else class="min-h-0 flex-1 space-y-4 overflow-y-auto">
                <p v-if="loadingFavorites" class="text-sm text-base-content/60">Chargement…</p>
                <p v-else-if="loadError" class="text-sm text-error">{{ loadError }}</p>
                <p
                    v-else-if="favoriteGroups.length === 0"
                    class="rounded-box border border-base-300/70 bg-base-100/40 p-6 text-center text-sm text-base-content/65"
                >
                    Aucun favori pour le moment. Utilisez la recherche ci-dessus pour en ajouter.
                </p>
                <section
                    v-for="group in favoriteGroups"
                    :key="group.entityType"
                    class="space-y-2"
                >
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300/90">
                        {{ group.label }}
                        <span class="ml-1 font-normal text-base-content/50">({{ group.items.length }})</span>
                    </h3>
                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                        <template v-for="(card, idx) in group.items" :key="`${group.entityType}:${card.model?.id || card.hit?.id || idx}`">
                            <div
                                v-if="card.Minimal && card.model"
                                class="min-w-0"
                            >
                                <component
                                    :is="card.Minimal"
                                    v-bind="{
                                        [card.prop]: card.model,
                                        displayMode: 'compact',
                                        showActions: true,
                                    }"
                                    @quick-view="(entity) => onQuickViewFromMinimal(entity || card.model, card.entityType)"
                                    @action="(key, entity) => handleMinimalAction(key, entity || card.model, card.entityType)"
                                />
                            </div>
                            <GlobalSearchHitRow
                                v-else-if="card.hit"
                                :hit="card.hit"
                                density="lg"
                                :show-favorite-toggle="true"
                                :is-favorite="true"
                                @select="onSelectHit"
                                @toggle-favorite="onToggleFavorite"
                            />
                        </template>
                    </div>
                </section>
            </div>
        </template>

        <EntityModal
            v-if="modalEntity"
            :entity="modalEntity"
            :entity-type="modalEntityType"
            view="full"
            :open="modalOpen"
            :use-stored-format="false"
            @close="closeModal"
        />
    </div>
</template>
