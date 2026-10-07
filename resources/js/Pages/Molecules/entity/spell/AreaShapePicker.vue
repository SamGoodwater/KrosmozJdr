<script setup>
/**
 * Sélecteur de zone par icône de forme + paramètres numériques + aperçu.
 *
 * @example
 * <AreaShapePicker v-model="area" />
 */
import { computed, ref, watch } from 'vue';
import {
    AREA_SHAPE_ICONS,
    AREA_SHAPE_LABELS,
    AREA_SHAPES,
} from '@/Utils/Entity/Areas.js';
import {
    AREA_SHAPE_HELP,
    buildAreaNotation,
    isKnownShapeNotation,
    parseAreaShapeParams,
} from '@/Utils/Entity/areaShapePicker.js';
import SpellZonePreview from '@/Pages/Molecules/entity/spell/SpellZonePreview.vue';

const props = defineProps({
    modelValue: { type: [String, null], default: '' },
    disabled: { type: Boolean, default: false },
    /** Affiche aussi le champ texte brut (notation avancée). */
    showRaw: { type: Boolean, default: true },
});

const emit = defineEmits(['update:modelValue']);

const params = ref(parseAreaShapeParams(props.modelValue));
const rawMode = ref(!isKnownShapeNotation(props.modelValue) && Boolean(props.modelValue));

watch(
    () => props.modelValue,
    (v) => {
        if (!isKnownShapeNotation(v) && v) {
            rawMode.value = true;
            return;
        }
        params.value = parseAreaShapeParams(v);
    },
);

const help = computed(() => AREA_SHAPE_HELP[params.value.shape] || '');

function selectShape(shape) {
    if (props.disabled) return;
    rawMode.value = false;
    const next = { ...params.value, shape };
    if (shape === 'line' && !next.length) next.length = 3;
    if ((shape === 'cross' || shape === 'circle') && next.a == null) {
        next.a = 0;
        next.b = 1;
    }
    if (shape === 'rect') {
        if (!next.width) next.width = 3;
        if (!next.height) next.height = 3;
    }
    params.value = next;
    emit('update:modelValue', buildAreaNotation(next));
}

function updateParam(key, value) {
    const next = { ...params.value, [key]: value === '' ? 0 : Number(value) };
    if ((next.shape === 'cross' || next.shape === 'circle') && next.b < next.a) {
        next.b = next.a;
    }
    params.value = next;
    emit('update:modelValue', buildAreaNotation(next));
}

function onRawInput(value) {
    emit('update:modelValue', value);
}
</script>

<template>
    <div class="area-shape-picker space-y-2" data-cy="area-shape-picker">
        <div class="flex flex-wrap gap-1.5">
            <button
                v-for="shape in AREA_SHAPES"
                :key="shape"
                type="button"
                class="btn btn-sm gap-1.5 border border-base-300"
                :class="
                    !rawMode && params.shape === shape
                        ? 'btn-neutral'
                        : 'btn-ghost bg-base-100'
                "
                :disabled="disabled"
                :title="AREA_SHAPE_LABELS[shape]"
                :aria-pressed="!rawMode && params.shape === shape"
                @click="selectShape(shape)"
            >
                <img
                    :src="`/storage/images/${AREA_SHAPE_ICONS[shape]}`"
                    :alt="AREA_SHAPE_LABELS[shape]"
                    class="h-5 w-5 object-contain"
                />
                <span class="text-xs">{{ AREA_SHAPE_LABELS[shape] }}</span>
            </button>
        </div>

        <p class="text-xs text-base-content/70 leading-snug">{{ help }}</p>

        <div class="flex flex-wrap items-start gap-3">
            <div v-if="!rawMode && params.shape !== 'point'" class="flex flex-wrap gap-2 min-w-0">
                <label v-if="params.shape === 'line'" class="form-control w-24">
                    <span class="label-text text-xs text-base-content/70">Longueur L</span>
                    <input
                        type="number"
                        min="1"
                        class="input input-bordered input-sm"
                        :value="params.length ?? 1"
                        :disabled="disabled"
                        @input="updateParam('length', $event.target.value)"
                    />
                </label>
                <template v-if="params.shape === 'cross' || params.shape === 'circle'">
                    <label class="form-control w-24">
                        <span class="label-text text-xs text-base-content/70">Rayon min a</span>
                        <input
                            type="number"
                            min="0"
                            class="input input-bordered input-sm"
                            :value="params.a ?? 0"
                            :disabled="disabled"
                            @input="updateParam('a', $event.target.value)"
                        />
                    </label>
                    <label class="form-control w-24">
                        <span class="label-text text-xs text-base-content/70">Rayon max b</span>
                        <input
                            type="number"
                            min="0"
                            class="input input-bordered input-sm"
                            :value="params.b ?? 0"
                            :disabled="disabled"
                            @input="updateParam('b', $event.target.value)"
                        />
                    </label>
                </template>
                <template v-if="params.shape === 'rect'">
                    <label class="form-control w-24">
                        <span class="label-text text-xs text-base-content/70">Largeur W</span>
                        <input
                            type="number"
                            min="1"
                            class="input input-bordered input-sm"
                            :value="params.width ?? 1"
                            :disabled="disabled"
                            @input="updateParam('width', $event.target.value)"
                        />
                    </label>
                    <label class="form-control w-24">
                        <span class="label-text text-xs text-base-content/70">Hauteur H</span>
                        <input
                            type="number"
                            min="1"
                            class="input input-bordered input-sm"
                            :value="params.height ?? 1"
                            :disabled="disabled"
                            @input="updateParam('height', $event.target.value)"
                        />
                    </label>
                </template>
            </div>

            <SpellZonePreview
                :area="modelValue || 'point'"
                :max-viewport-px="120"
                :cell-size-min-px="10"
            />
        </div>

        <div v-if="showRaw" class="space-y-1">
            <button
                type="button"
                class="btn btn-ghost btn-xs text-base-content/70"
                :disabled="disabled"
                @click="rawMode = !rawMode"
            >
                {{ rawMode ? 'Revenir aux formes' : 'Notation avancée' }}
            </button>
            <input
                v-if="rawMode"
                type="text"
                class="input input-bordered input-sm w-full font-mono text-xs"
                :value="modelValue"
                :disabled="disabled"
                placeholder="point, circle-0-2, shape-80…"
                @input="onRawInput($event.target.value)"
            />
        </div>
    </div>
</template>
