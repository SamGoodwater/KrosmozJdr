<script setup>
/**
 * Mise en avant de la dissipabilité d’un état (entité Condition).
 *
 * @prop {boolean} [dissipable] - Si absent, traité comme dissipable (défaut métier).
 * @prop {'inline'|'block'|'icon-only'} [variant='inline'] - Densité d’affichage.
 * @prop {boolean} [showLabel=true] - Libellé texte (masqué si variant = icon-only).
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
        validator: (v) => ["inline", "block", "icon-only"].includes(v),
    },
    showLabel: { type: Boolean, default: true },
});

const effective = computed(() => resolveEntityDissipable(props.dissipable));
const icon = computed(() => getConditionDispellableIcon(effective.value));
const label = computed(() => formatConditionDispellable(effective.value) || "");
</script>

<template>
    <span
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
