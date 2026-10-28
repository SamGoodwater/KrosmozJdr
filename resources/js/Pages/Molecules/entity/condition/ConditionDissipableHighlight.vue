<script setup>
/**
 * Mise en avant de la dissipabilité d’un état (entité Condition).
 *
 * @prop {boolean} [dissipable] - Si absent, traité comme dissipable (défaut métier).
 * @prop {'inline'|'block'|'icon-only'|'caption-hover'} [variant='inline'] - Densité d’affichage.
 * @prop {boolean} [showLabel=true] - Libellé texte (masqué si variant = icon-only).
 *
 * `caption-hover` : icône seule ; au survol, libellé petit sous l’icône (ex. sous l’image Full).
 */
import { computed } from "vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import {
    formatConditionDispellable,
    getConditionDispellableIcon,
    resolveEntityDissipable,
} from "@/Composables/condition/conditionDisplay";

const props = defineProps({
    dissipable: { type: Boolean, default: undefined },
    variant: {
        type: String,
        default: "inline",
        validator: (v) => ["inline", "block", "icon-only", "caption-hover"].includes(v),
    },
    showLabel: { type: Boolean, default: true },
});

const effective = computed(() => resolveEntityDissipable(props.dissipable));
const icon = computed(() => getConditionDispellableIcon(effective.value));
const label = computed(() => formatConditionDispellable(effective.value) || "");

const isCaptionHover = computed(() => props.variant === "caption-hover");
</script>

<template>
    <span
        v-if="isCaptionHover"
        class="group/dissipable inline-flex max-w-[5.5rem] flex-col items-center gap-0.5"
        :title="label"
        data-cy="condition-dissipable-caption"
    >
        <Image
            v-if="icon"
            :source="icon"
            :alt="label"
            fit="contain"
            rounded="md"
            width="1.25rem"
            height="1.25rem"
            class="inline-flex max-h-5 max-w-5 shrink-0"
        />
        <span
            v-if="label"
            class="max-h-0 overflow-hidden text-center text-[10px] font-medium leading-tight text-primary-300/90 opacity-0 transition-all duration-150 ease-out group-hover/dissipable:max-h-8 group-hover/dissipable:opacity-100"
        >
            {{ label }}
        </span>
    </span>
    <span
        v-else
        class="inline-flex items-center gap-2"
        :class="{
            'rounded-md border border-base-300/60 bg-base-200/40 px-2 py-1': variant === 'block',
        }"
        :title="label"
    >
        <Image
            v-if="icon"
            :source="icon"
            :alt="label"
            fit="contain"
            rounded="md"
            width="1.75rem"
            height="1.75rem"
            class="inline-flex max-h-7 max-w-7 shrink-0"
        />
        <span
            v-if="showLabel && variant !== 'icon-only'"
            class="text-xs font-semibold uppercase tracking-wide text-primary-200/95"
        >
            {{ label }}
        </span>
    </span>
</template>
