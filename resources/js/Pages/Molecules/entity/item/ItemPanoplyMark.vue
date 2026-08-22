<script setup>
/**
 * Marque de panoplie sur une fiche équipement.
 *
 * @description
 * - `icon` : vignette seule (minimal compact), tooltip = nom + pièces (vue texte) + bonus.
 * - `named` : vignette + nom (minimal déployé, line), même tooltip.
 * - `full` : nom, liste des pièces, bonus avec paliers (chiffre seul).
 *
 * @example
 * <ItemPanoplyMark :item="item" density="named" />
 */
import { computed } from "vue";
import PanoplyThumb from "@/Pages/Molecules/entity/panoply/PanoplyThumb.vue";
import PanoplyBonusTiers from "@/Pages/Molecules/entity/panoply/PanoplyBonusTiers.vue";
import PanoplyEquipmentTextList from "@/Pages/Molecules/entity/panoply/PanoplyEquipmentTextList.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { itemPanopliesFrom } from "@/Utils/entity/itemPanoply";

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
    density: {
        type: String,
        default: "named",
        validator: (v) => ["icon", "named", "full"].includes(v),
    },
    tableMeta: {
        type: Object,
        default: () => ({}),
    },
});

const panoplies = computed(() => itemPanopliesFrom(props.item));

const showName = computed(() => props.density !== "icon");
const isFull = computed(() => props.density === "full");
const thumbSize = computed(() => (props.density === "full" ? "xs" : "badge"));

function setItems(panoply) {
    const raw = Array.isArray(panoply?.items) ? panoply.items : [];
    return raw.filter((row) => row && row.id != null);
}
</script>

<template>
    <div
        v-if="panoplies.length"
        class="min-w-0"
        :class="isFull ? 'flex w-full flex-col gap-3' : 'inline-flex max-w-full flex-wrap items-center gap-1.5'"
        @click.stop
    >
        <template v-for="panoply in panoplies" :key="panoply.id">
            <div
                v-if="isFull"
                class="w-full space-y-2"
            >
                <div class="flex min-w-0 items-center gap-2">
                    <PanoplyThumb
                        :items="setItems(panoply)"
                        :label="panoply.name || 'Panoplie'"
                        :size="thumbSize"
                    />
                    <h3 class="min-w-0 truncate text-sm font-semibold uppercase tracking-wide text-primary-300">
                        {{ panoply.name || "Panoplie" }}
                    </h3>
                </div>
                <div class="space-y-2 rounded-lg bg-base-200/50 p-3 text-primary-200">
                    <PanoplyEquipmentTextList
                        v-if="setItems(panoply).length"
                        :items="setItems(panoply)"
                        :table-meta="tableMeta"
                    />
                    <PanoplyBonusTiers
                        v-if="panoply.bonus"
                        :bonus="panoply.bonus"
                        label-mode="full"
                        layout="grid"
                    />
                </div>
            </div>
            <Tooltip
                v-else
                placement="top"
                :interactive="true"
                max-width="md"
                color="neutral"
                class="inline-flex min-w-0 max-w-full"
            >
                <span
                    class="inline-flex min-w-0 max-w-full items-center gap-1"
                    :title="panoply.name || 'Panoplie'"
                >
                    <PanoplyThumb
                        :items="setItems(panoply)"
                        :label="panoply.name || 'Panoplie'"
                        :size="thumbSize"
                    />
                    <span
                        v-if="showName"
                        class="min-w-0 truncate text-xs font-medium text-base-content/80"
                    >
                        {{ panoply.name || "Panoplie" }}
                    </span>
                </span>
                <template #content>
                    <div class="flex min-w-0 max-w-full flex-col gap-2 p-1 text-left">
                        <p class="m-0 text-xs font-semibold leading-tight text-base-content">
                            {{ panoply.name || "Panoplie" }}
                        </p>
                        <PanoplyEquipmentTextList
                            v-if="setItems(panoply).length"
                            :items="setItems(panoply)"
                            :table-meta="tableMeta"
                            layout="stack"
                        />
                        <PanoplyBonusTiers
                            v-if="panoply.bonus"
                            :bonus="panoply.bonus"
                            label-mode="icon-only"
                            layout="inline"
                        />
                    </div>
                </template>
            </Tooltip>
        </template>
    </div>
</template>
