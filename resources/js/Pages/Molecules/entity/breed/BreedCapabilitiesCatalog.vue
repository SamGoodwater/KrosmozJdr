<script setup>
/**
 * Catalogue secondaire des capacités de classe (hors passif), groupées par niveau.
 * Bascule Icônes (défaut) / Cartes / Texte — localStorage dédié.
 *
 * @props {object[]} capabilities
 * @props {'full'|'compact'} density
 */
import { computed, markRaw, ref, watch } from "vue";
import CapabilityViewMinimal from "@/Pages/Molecules/entity/capability/CapabilityViewMinimal.vue";
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import OverlayTrigger from "@/Pages/Molecules/overlay/OverlayTrigger.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import BreedCatalogByLevel from "@/Pages/Molecules/entity/breed/BreedCatalogByLevel.vue";
import { partitionBreedCapabilities } from "@/Utils/entity/breedCapabilitiesPartition";
import { Capability } from "@/Models/Entity/Capability";
import { fetchEntityModelById } from "@/Composables/entity/useEntityTableFetch";

const CAP_LAYOUT_STORAGE_KEY = "breed_capabilities_catalog_layout";

/** @type {import('vue').Raw<typeof CapabilityViewMinimal>} */
const capabilityMinimalComponent = markRaw(CapabilityViewMinimal);

/**
 * @returns {'icons'|'cards'|'text'}
 */
function readStoredLayout() {
    try {
        const raw = localStorage.getItem(CAP_LAYOUT_STORAGE_KEY);
        if (raw === "text" || raw === "cards" || raw === "icons") return raw;
    } catch {
        /* private mode / SSR */
    }
    return "icons";
}

const props = defineProps({
    capabilities: {
        type: Array,
        default: () => [],
    },
    density: {
        type: String,
        default: "full",
        validator: (v) => ["full", "compact"].includes(v),
    },
    characteristicRuntime: {
        type: Object,
        default: null,
    },
});

const otherList = computed(() => {
    const { otherCapabilities } = partitionBreedCapabilities(props.capabilities);
    return otherCapabilities;
});

const hasAny = computed(() => otherList.value.length > 0);

/**
 * @returns {Array<{ level: number, levelLabel: string, capabilities: Capability[] }>}
 */
const byLevel = computed(() => {
    /** @type {Map<number, Capability[]>} */
    const map = new Map();
    for (const cap of otherList.value) {
        const level = Number(cap.level) || 0;
        if (level < 1) continue;
        if (!map.has(level)) map.set(level, []);
        map.get(level).push(cap);
    }
    return Array.from(map.keys())
        .sort((a, b) => a - b)
        .map((level) => ({
            level,
            levelLabel: `N${level}`,
            capabilities: map.get(level) || [],
        }));
});

const layout = ref(readStoredLayout());
const isIcons = computed(() => layout.value === "icons");
const isCards = computed(() => layout.value === "cards");
const isText = computed(() => layout.value === "text");

watch(layout, (value) => {
    try {
        localStorage.setItem(CAP_LAYOUT_STORAGE_KEY, value);
    } catch {
        /* ignore */
    }
});

/**
 * @param {'icons'|'cards'|'text'} next
 */
function setLayout(next) {
    if (next !== "icons" && next !== "cards" && next !== "text") return;
    layout.value = next;
}

/**
 * @param {object} raw
 * @returns {Capability}
 */
const asCap = (raw) => (raw instanceof Capability ? raw : new Capability(raw));

const entityName = (entity) => entity?.name || entity?._data?.name || "Capacité";
const entityImage = (entity) => entity?.image || entity?._data?.image || "";

/**
 * @param {object} raw
 */
function capIconOverlay(raw) {
    const cap = asCap(raw);
    const id = cap?.id;
    return {
        key: `breed-cap-icon:${id ?? entityName(cap)}`,
        loader: async () => {
            let entity = cap;
            if (id) {
                try {
                    const full = await fetchEntityModelById("capabilities", id);
                    if (full) entity = full;
                } catch {
                    /* payload allégé */
                }
            }
            return {
                component: capabilityMinimalComponent,
                props: {
                    capability: entity,
                    showActions: true,
                    displayMode: "extended",
                    ...(props.characteristicRuntime != null
                        ? { characteristicRuntime: props.characteristicRuntime }
                        : {}),
                },
            };
        },
    };
}

const iconBtnClass = computed(() =>
    props.density === "compact"
        ? "btn btn-ghost btn-xs btn-square"
        : "btn btn-ghost btn-sm btn-square border border-base-300/40 bg-base-200/20 hover:bg-base-200/50",
);
const iconThumbClass = computed(() => (props.density === "compact" ? "h-5 w-5" : "h-7 w-7"));
</script>

<template>
    <div
        v-if="hasAny"
        class="breed-capabilities-catalog space-y-3"
        :data-density="density"
        :data-cap-layout="layout"
    >
        <div class="flex flex-wrap items-start justify-between gap-2">
            <div>
                <h3
                    class="text-xs font-semibold uppercase tracking-wide"
                    :class="density === 'compact' ? 'text-base-content/50' : 'text-base-content/50'"
                >
                    Catalogue des capacités
                </h3>
                <p v-if="density === 'full'" class="text-xs text-base-content/55 mt-1">
                    Capacité de classe hors passif, par niveau d’apprentissage.
                </p>
            </div>
            <div class="join" role="group" aria-label="Mode d’affichage des capacités">
                <Tooltip content="Icônes" placement="top">
                    <button
                        type="button"
                        class="btn btn-xs join-item"
                        :class="isIcons ? 'btn-primary' : 'btn-ghost'"
                        aria-label="Affichage en icônes"
                        :aria-pressed="isIcons"
                        @click="setLayout('icons')"
                    >
                        <Icon source="fa-solid fa-grip" size="xs" />
                    </button>
                </Tooltip>
                <Tooltip content="Cartes minimales" placement="top">
                    <button
                        type="button"
                        class="btn btn-xs join-item"
                        :class="isCards ? 'btn-primary' : 'btn-ghost'"
                        aria-label="Affichage en cartes minimales"
                        :aria-pressed="isCards"
                        @click="setLayout('cards')"
                    >
                        <Icon source="fa-solid fa-table-cells-large" size="xs" />
                    </button>
                </Tooltip>
                <Tooltip content="Liste texte" placement="top">
                    <button
                        type="button"
                        class="btn btn-xs join-item"
                        :class="isText ? 'btn-primary' : 'btn-ghost'"
                        aria-label="Affichage texte"
                        :aria-pressed="isText"
                        @click="setLayout('text')"
                    >
                        <Icon source="fa-solid fa-align-left" size="xs" />
                    </button>
                </Tooltip>
            </div>
        </div>

        <div
            class="rounded-box border space-y-3"
            :class="
                density === 'compact'
                    ? 'border-base-300/80 bg-base-200/30 p-2'
                    : 'border-base-300/60 bg-base-100/30 p-4'
            "
        >
            <div v-if="isIcons" class="flex flex-wrap items-start gap-x-4 gap-y-3">
                <BreedCatalogByLevel
                    v-for="block in byLevel"
                    :key="`cap-icons-n${block.level}`"
                    :level-label="block.levelLabel"
                >
                    <OverlayTrigger
                        v-for="c in block.capabilities"
                        :key="`cap-i-${c.id}`"
                        :content="capIconOverlay(c)"
                        trigger="click"
                        placement="bottom-start"
                        max-width="auto"
                        :interactive="true"
                        :close-on-outside="true"
                        :close-on-escape="true"
                        :chromeless="true"
                        panel-class="w-[min(92vw,36rem)] max-w-[36rem]"
                        :focus-trap="false"
                    >
                        <button
                            type="button"
                            :class="iconBtnClass"
                            :aria-label="entityName(c)"
                            :title="entityName(c)"
                        >
                            <EntityThumb
                                size="table"
                                :class="iconThumbClass"
                                :src="entityImage(c) || ''"
                                :label="entityName(c)"
                            />
                        </button>
                    </OverlayTrigger>
                </BreedCatalogByLevel>
            </div>

            <div
                v-else-if="isCards"
                class="flex flex-wrap items-start gap-x-4 gap-y-3"
            >
                <div
                    v-for="block in byLevel"
                    :key="`cap-cards-n${block.level}`"
                    class="space-y-2 shrink-0 max-w-full"
                >
                    <div class="text-xs font-semibold uppercase tracking-wide text-base-content/55">
                        {{ block.levelLabel }}
                    </div>
                    <ul class="flex list-none flex-wrap gap-2 p-0 m-0 items-start">
                        <li
                            v-for="c in block.capabilities"
                            :key="`cap-c-${c.id}`"
                            class="min-w-0 max-w-[22rem]"
                        >
                            <CapabilityViewMinimal
                                :capability="c"
                                display-mode="hover"
                                :characteristic-runtime="characteristicRuntime"
                                :show-actions="true"
                            />
                        </li>
                    </ul>
                </div>
            </div>

            <div v-else class="flex flex-wrap items-start gap-x-4 gap-y-2">
                <div
                    v-for="block in byLevel"
                    :key="`cap-text-n${block.level}`"
                    class="flex flex-col gap-1 shrink-0 max-w-full"
                >
                    <span class="text-xs font-semibold uppercase tracking-wide text-base-content/55">
                        {{ block.levelLabel }}
                    </span>
                    <ul class="flex list-none flex-wrap gap-x-3 gap-y-1.5 p-0 m-0">
                        <li
                            v-for="c in block.capabilities"
                            :key="`cap-t-${c.id}`"
                            class="min-w-0 max-w-full"
                        >
                            <EntityViewTextLink
                                :entity="asCap(c)"
                                entity-prop="capability"
                                :minimal-component="capabilityMinimalComponent"
                                fallback-icon="fa-solid fa-star"
                                ui-color="neutral"
                                :show-actions-on-hover="true"
                                hydrate-type="capabilities"
                                hover-width-class="w-[min(92vw,36rem)] max-w-[36rem]"
                                :characteristic-runtime="characteristicRuntime"
                            />
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
