<script setup>
/**
 * Vignette de panoplie : images des équipements, sinon initiales du nom.
 *
 * @example
 * <PanoplyThumb :items="panoply.items" label="Panoplie du Bouftou" size="line" />
 */
import { computed } from "vue";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import { resolveEntityImageUrl } from "@/Utils/entity/entityThumb";

const props = defineProps({
    items: { type: Array, default: () => [] },
    label: { type: String, default: "Panoplie" },
    size: {
        type: String,
        default: "line",
        validator: (v) => ["xs", "compact", "line", "fill"].includes(v),
    },
});

const imageUrls = computed(() => {
    const urls = [];
    const seen = new Set();
    for (const item of Array.isArray(props.items) ? props.items : []) {
        const url = resolveEntityImageUrl(item);
        if (!url || seen.has(url)) {
            continue;
        }
        seen.add(url);
        urls.push(url);
        if (urls.length >= 4) {
            break;
        }
    }
    return urls;
});

const gridClass = computed(() => {
    const n = imageUrls.value.length;
    if (n <= 1) {
        return "grid-cols-1 grid-rows-1";
    }
    if (n === 2) {
        return "grid-cols-2 grid-rows-1";
    }
    return "grid-cols-2 grid-rows-2";
});
</script>

<template>
    <div
        v-if="imageUrls.length === 0"
        class="panoply-thumb panoply-thumb--empty"
        :class="[`panoply-thumb--${size}`]"
    >
        <EntityThumb
            size="line"
            :label="label"
            class="panoply-thumb__initials"
        />
    </div>
    <div
        v-else-if="imageUrls.length === 1"
        class="panoply-thumb"
        :class="[`panoply-thumb--${size}`]"
    >
        <Image
            :src="imageUrls[0]"
            :alt="label"
            fit="contain"
            rounded="box"
            class="h-full w-full"
        />
    </div>
    <div
        v-else
        class="panoply-thumb grid gap-px overflow-hidden rounded-box bg-base-300"
        :class="[`panoply-thumb--${size}`, gridClass]"
    >
        <Image
            v-for="(url, index) in imageUrls"
            :key="`${url}-${index}`"
            :src="url"
            :alt="label"
            fit="contain"
            class="h-full w-full bg-base-200"
        />
    </div>
</template>

<style scoped>
.panoply-thumb--xs {
    width: 2.5rem;
    height: 2.5rem;
}
.panoply-thumb--compact {
    width: 3.5rem;
    height: 3.5rem;
}
.panoply-thumb--line {
    width: 5rem;
    min-height: 5rem;
    height: 100%;
    align-self: stretch;
}
.panoply-thumb--fill {
    width: 100%;
    height: 100%;
}
.panoply-thumb {
    flex-shrink: 0;
}
.panoply-thumb--empty :deep(.entity-thumb) {
    width: 100%;
    height: 100%;
    min-height: 100%;
    align-self: stretch;
}
</style>
