<script setup>
/**
 * Bandeau compact : 4 voix élémentaires + icône d’orientation par voix.
 *
 * @props {Record<string, string|null|undefined>} orientationMap - air|earth|fire|water => orientation_key
 */
import { computed } from "vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import {
    BREED_ELEMENT_KEYS,
    BREED_ELEMENT_LABELS,
    BREED_ELEMENT_VOICE_ICON_PATH,
    breedOrientationIconUrl,
} from "@/Utils/entity/breedOrientations";

const props = defineProps({
    orientationMap: {
        type: Object,
        default: () => ({}),
    },
    /** Taille visuelle des pictos */
    size: {
        type: String,
        default: "sm",
        validator: (v) => ["xs", "sm", "md"].includes(v),
    },
});

const sizeClass = {
    xs: "h-5 w-5",
    sm: "h-6 w-6",
    md: "h-8 w-8",
};

const slotClass = computed(() => sizeClass[props.size] || sizeClass.sm);

const orientationSlots = computed(() => {
    const m = props.orientationMap || {};
    return BREED_ELEMENT_KEYS.map((el) => ({
        element: el,
        label: BREED_ELEMENT_LABELS[el] || el,
        voiceIcon: BREED_ELEMENT_VOICE_ICON_PATH[el],
        orientationKey: m[el] != null && String(m[el]).trim() !== "" ? String(m[el]).trim() : null,
        orientationUrl: breedOrientationIconUrl(m[el]),
    }));
});
</script>

<template>
    <div class="flex flex-wrap items-center gap-1.5" data-cy="breed-element-orientations">
        <Tooltip
            v-for="s in orientationSlots"
            :key="s.element"
            :content="
                s.orientationKey
                    ? `${s.label} — orientation : ${s.orientationKey}`
                    : `${s.label} — orientation non définie`
            "
            placement="top"
        >
            <span
                class="inline-flex items-center gap-0.5 rounded-md border border-base-300/60 bg-base-200/40 px-1 py-0.5"
            >
                <Image
                    :source="s.voiceIcon"
                    :alt="s.label"
                    fit="contain"
                    :class="[slotClass, 'shrink-0']"
                />
                <Image
                    v-if="s.orientationUrl"
                    :source="s.orientationUrl"
                    :alt="s.orientationKey || ''"
                    fit="contain"
                    rounded="sm"
                    :class="[slotClass, 'shrink-0']"
                />
                <span
                    v-else
                    :class="slotClass"
                    class="inline-flex items-center justify-center rounded-sm bg-base-300/50 text-[10px] text-base-content/50"
                >
                    —
                </span>
            </span>
        </Tooltip>
    </div>
</template>
