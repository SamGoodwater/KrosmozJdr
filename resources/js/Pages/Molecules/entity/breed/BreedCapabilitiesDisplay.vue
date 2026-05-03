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
});

const partitioned = computed(() => partitionBreedCapabilities(props.capabilities));

const passiveList = computed(() => partitioned.value.passiveCapabilities);
const otherList = computed(() => partitioned.value.otherCapabilities);

const hasPassive = computed(() => passiveList.value.length > 0);
const hasOther = computed(() => otherList.value.length > 0);
const hasAny = computed(() => hasPassive.value || (props.showOtherSection && hasOther.value));
</script>

<template>
    <div v-if="hasAny" class="breed-capabilities-display space-y-4" :data-density="density">
        <div v-if="hasPassive" class="rounded-box border border-base-300 bg-base-100/40 p-4 space-y-3">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Passifs de classe</h3>
            <ul v-if="density === 'text'" class="flex flex-wrap gap-x-4 gap-y-2">
                <li v-for="c in passiveList" :key="`p-t-${c.id}`" class="min-w-0 max-w-full">
                    <CapabilityViewText :capability="c" />
                </li>
            </ul>
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
            <ul v-if="density === 'text'" class="flex flex-wrap gap-x-4 gap-y-2">
                <li v-for="c in otherList" :key="`o-t-${c.id}`" class="min-w-0 max-w-full">
                    <CapabilityViewText :capability="c" />
                </li>
            </ul>
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
