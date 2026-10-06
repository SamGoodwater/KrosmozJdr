<script setup>
/**
 * Champ de zone avec aperçu, validation et rappel de la notation Krosmoz.
 *
 * @example
 * <AreaNotationField v-model="form.area" label="Zone" />
 */
import { computed } from 'vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import AreaDisplay from '@/Pages/Molecules/entity/spell/AreaDisplay.vue';
import { AREA_NOTATION_HELP, isValidAreaNotation } from '@/Utils/Entity/areaNotation.js';

const props = defineProps({
    modelValue: { type: [String, null], default: '' },
    label: { type: String, default: 'Zone' },
    helper: { type: String, default: '' },
    name: { type: String, default: 'area' },
});

const emit = defineEmits(['update:modelValue']);

const validation = computed(() => {
    const raw = props.modelValue;
    if (raw == null || String(raw).trim() === '') return undefined;
    return isValidAreaNotation(raw)
        ? undefined
        : { state: 'error', message: `Notation invalide. ${AREA_NOTATION_HELP}` };
});
</script>

<template>
    <div class="space-y-1.5">
        <div class="flex items-start gap-2">
            <div class="flex min-h-12 shrink-0 items-center justify-center pt-6">
                <AreaDisplay :area="modelValue || ''" icon-size="lg" icon-only />
            </div>
            <InputField
                :model-value="modelValue"
                :label="label"
                :name="name"
                :helper="helper"
                :validation="validation"
                class="min-w-0 flex-1"
                placeholder="point, circle-1-2, line-1x3…"
                @update:model-value="emit('update:modelValue', $event)"
            />
        </div>
        <p class="text-xs leading-relaxed text-base-content/70">
            <strong>Notation</strong> <code class="text-[0.7rem]">forme[-paramètres]</code> :
            <code class="text-[0.7rem]">point</code> ;
            <code class="text-[0.7rem]">line-1xL</code> ;
            <code class="text-[0.7rem]">cross-a-b</code> /
            <code class="text-[0.7rem]">circle-a-b</code> (a≤b) ;
            <code class="text-[0.7rem]">rect-WxH</code> ;
            <code class="text-[0.7rem]">shape-ID</code> ou
            <code class="text-[0.7rem]">shape-ID-p1-p2</code>.
        </p>
    </div>
</template>
