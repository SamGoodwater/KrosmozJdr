<script setup>
/**
 * Capacités liées à une classe : passifs en premier, autres capacités ensuite.
 *
 * @props {object[]} capabilities - Liste brute (CapabilityResource)
 * @props {'text'|'compact'|'large'} density
 */
import { computed } from "vue";
import CapabilityViewText from "@/Pages/Molecules/entity/capability/CapabilityViewText.vue";
import CapabilityViewMinimal from "@/Pages/Molecules/entity/capability/CapabilityViewMinimal.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import { partitionBreedCapabilities } from "@/Utils/entity/breedCapabilitiesPartition";

const props = defineProps({
    capabilities: {
        type: Array,
        default: () => [],
    },
    density: {
        type: String,
        default: "large",
        validator: (v) => ["text", "compact", "large"].includes(v),
    },
    characteristicRuntime: {
        type: Object,
        default: null,
    },
    /** Afficher la section « autres capacités » (désactivable sur Minimal très compact) */
    showOtherSection: {
        type: Boolean,
        default: true,
    },
    lightweight: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["open-capability"]);

const partitioned = computed(() => partitionBreedCapabilities(props.capabilities));

const passiveList = computed(() => partitioned.value.passiveCapabilities);
const otherList = computed(() => partitioned.value.otherCapabilities);

const hasPassive = computed(() => passiveList.value.length > 0);
const hasOther = computed(() => otherList.value.length > 0);
const hasAny = computed(() => hasPassive.value || (props.showOtherSection && hasOther.value));

const ICON_LIMIT = 10;

const passivePreview = computed(() => passiveList.value.slice(0, ICON_LIMIT));
const otherPreview = computed(() => otherList.value.slice(0, ICON_LIMIT));

const hiddenPassiveCount = computed(() => Math.max(0, passiveList.value.length - passivePreview.value.length));
const hiddenOtherCount = computed(() => Math.max(0, otherList.value.length - otherPreview.value.length));

function entityName(entity) {
    return entity?.name || entity?._data?.name || "Capacité";
}

function entityImage(entity) {
    return entity?.image || entity?._data?.image || "";
}

function openCapability(entity) {
    emit("open-capability", entity);
}
</script>

<template>
    <div v-if="hasAny" class="breed-capabilities-display space-y-4" :data-density="density">
        <div v-if="hasPassive" class="rounded-box border border-base-300 bg-base-100/40 p-4 space-y-3">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Passifs de classe</h3>
            <ul v-if="density === 'text' && !lightweight" class="flex flex-wrap gap-x-4 gap-y-2">
                <li v-for="c in passiveList" :key="`p-t-${c.id}`" class="min-w-0 max-w-full">
                    <CapabilityViewText :capability="c" />
                </li>
            </ul>
            <div v-else-if="density === 'text' && lightweight" class="flex items-center gap-1.5 flex-wrap">
                <Tooltip
                    v-for="c in passivePreview"
                    :key="`p-i-${c.id}`"
                    :content="entityName(c)"
                    placement="top"
                >
                    <button
                        type="button"
                        class="btn btn-ghost btn-xs btn-square"
                        :aria-label="`Ouvrir ${entityName(c)}`"
                        @click.stop="openCapability(c)"
                    >
                        <Image
                            v-if="entityImage(c)"
                            :source="entityImage(c)"
                            :alt="entityName(c)"
                            fit="contain"
                            class="h-4 w-4"
                        />
                        <Icon v-else source="fa-solid fa-bolt" size="xs" />
                    </button>
                </Tooltip>
                <span v-if="hiddenPassiveCount > 0" class="text-[10px] text-base-content/60">+{{ hiddenPassiveCount }}</span>
            </div>
            <ul v-else class="flex flex-wrap gap-x-3 gap-y-3">
                <li v-for="c in passiveList" :key="`p-m-${c.id}`" class="min-w-0 max-w-full">
                    <CapabilityViewMinimal
                        :capability="c"
                        :display-mode="'extended'"
                        :characteristic-runtime="characteristicRuntime"
                        :show-actions="false"
                    />
                </li>
            </ul>
        </div>

        <div
            v-if="showOtherSection && hasOther"
            class="rounded-box border border-base-300 bg-base-100/40 p-4 space-y-3"
        >
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Autres capacités</h3>
            <ul v-if="density === 'text' && !lightweight" class="flex flex-wrap gap-x-4 gap-y-2">
                <li v-for="c in otherList" :key="`o-t-${c.id}`" class="min-w-0 max-w-full">
                    <CapabilityViewText :capability="c" />
                </li>
            </ul>
            <div v-else-if="density === 'text' && lightweight" class="flex items-center gap-1.5 flex-wrap">
                <Tooltip
                    v-for="c in otherPreview"
                    :key="`o-i-${c.id}`"
                    :content="entityName(c)"
                    placement="top"
                >
                    <button
                        type="button"
                        class="btn btn-ghost btn-xs btn-square"
                        :aria-label="`Ouvrir ${entityName(c)}`"
                        @click.stop="openCapability(c)"
                    >
                        <Image
                            v-if="entityImage(c)"
                            :source="entityImage(c)"
                            :alt="entityName(c)"
                            fit="contain"
                            class="h-4 w-4"
                        />
                        <Icon v-else source="fa-solid fa-bolt" size="xs" />
                    </button>
                </Tooltip>
                <span v-if="hiddenOtherCount > 0" class="text-[10px] text-base-content/60">+{{ hiddenOtherCount }}</span>
            </div>
            <ul v-else class="flex flex-wrap gap-x-3 gap-y-3">
                <li v-for="c in otherList" :key="`o-m-${c.id}`" class="min-w-0 max-w-full">
                    <CapabilityViewMinimal
                        :capability="c"
                        :display-mode="'extended'"
                        :characteristic-runtime="characteristicRuntime"
                        :show-actions="false"
                    />
                </li>
            </ul>
        </div>
    </div>
</template>
