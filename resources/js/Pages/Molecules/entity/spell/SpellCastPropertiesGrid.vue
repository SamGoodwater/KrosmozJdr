<script setup>
/**
 * Grille dense des propriétés de lancement (sort de base ou degré).
 *
 * @example
 * <SpellCastPropertiesGrid v-model="properties" :readonly="false" />
 */
import { computed } from 'vue';
import SpellDegreePropertyField from '@/Pages/Molecules/entity/spell/SpellDegreePropertyField.vue';
import AreaShapePicker from '@/Pages/Molecules/entity/spell/AreaShapePicker.vue';
import { formatPoRange, parsePoRange } from '@/Utils/Entity/poRange.js';
import { getByDbColumn } from '@/Composables/store/useCharacteristicsStore';
import { getAreaHumanReadable } from '@/Utils/Entity/Areas';

const props = defineProps({
    /** Objet propriétés mutable (pa, po_min, …). */
    modelValue: { type: Object, required: true },
    readonly: { type: Boolean, default: false },
    /** Badge source affiché en mode lecture (ex. « hérité du sort »). */
    sourceLabel: { type: String, default: '' },
    /** Sources par clé (degree|previous|spell). */
    propertySources: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['update:modelValue', 'dirty']);

const LOCAL_HELPERS = Object.freeze({
    pa: 'Coût en points d’action pour lancer le sort.',
    po_min: 'Portée en cases : un nombre (X) ou un intervalle (X-Y).',
    po_editable: 'Si actif, la portée peut être modifiée par les bonus de PO.',
    sight_line: 'Le sort exige une ligne de vue libre jusqu’à la cible.',
    global_cooldown: 'Tours d’attente avant de pouvoir relancer ce sort (relance globale).',
    cast_per_turn: 'Nombre maximal de lancers de ce sort par tour.',
    cast_per_target: 'Nombre maximal de lancers sur une même cible par tour (0 = illimité).',
    number_between_two_cast: 'Délai minimum (tours) entre deux lancers successifs.',
    casting_time: 'Temps d’incantation libre (ex. Instantané, 1 action).',
    area: 'Forme de la zone d’impact. Choisissez une icône puis ajustez les nombres.',
});

function helperFor(key) {
    const meta = getByDbColumn('spell', key);
    const fromMeta =
        meta?.helper ||
        meta?.description ||
        (Array.isArray(meta?.descriptions) ? meta.descriptions[0] : null);
    return fromMeta || LOCAL_HELPERS[key] || '';
}

function patch(key, value) {
    if (props.readonly) return;
    emit('update:modelValue', { ...props.modelValue, [key]: value });
    emit('dirty');
}

function updatePoRange(value) {
    const parsed = parsePoRange(value);
    emit('update:modelValue', {
        ...props.modelValue,
        po_min: parsed.po_min,
        po_max: parsed.po_max,
    });
    emit('dirty');
}

const poDisplay = computed(() =>
    formatPoRange(props.modelValue?.po_min, props.modelValue?.po_max),
);

function sourceBadge(key) {
    const src = props.propertySources?.[key];
    if (!src || src === 'degree' || src === 'none') return '';
    if (src === 'previous') return 'degré précédent';
    if (src === 'spell') return 'sort de base';
    return src;
}
</script>

<template>
    <div class="spell-cast-properties-grid space-y-2" data-cy="spell-cast-properties-grid">
        <p v-if="sourceLabel" class="text-xs text-base-content/70">
            <span class="badge badge-ghost badge-sm">{{ sourceLabel }}</span>
        </p>

        <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
            <SpellDegreePropertyField
                characteristic-key="pa"
                label="PA"
                icon="fa-solid fa-bolt"
                :helper="helperFor('pa')"
            >
                <input
                    v-if="!readonly"
                    :value="modelValue.pa ?? ''"
                    type="text"
                    class="input input-bordered input-sm w-full"
                    @input="patch('pa', $event.target.value)"
                />
                <p v-else class="text-sm font-medium tabular-nums">
                    {{ modelValue.pa ?? '—' }}
                    <span
                        v-if="sourceBadge('pa')"
                        class="ml-1 text-[0.65rem] font-normal text-base-content/70"
                    >
                        ({{ sourceBadge('pa') }})
                    </span>
                </p>
            </SpellDegreePropertyField>

            <SpellDegreePropertyField
                characteristic-key="po_min"
                label="Portée"
                icon="fa-solid fa-bullseye"
                :helper="helperFor('po_min')"
            >
                <input
                    v-if="!readonly"
                    :value="poDisplay"
                    type="text"
                    class="input input-bordered input-sm w-full"
                    placeholder="4 ou 2-8"
                    data-cy="spell-cast-po-range"
                    @input="updatePoRange($event.target.value)"
                />
                <p v-else class="text-sm font-medium tabular-nums">
                    {{ poDisplay || '—' }}
                </p>
            </SpellDegreePropertyField>

            <SpellDegreePropertyField
                characteristic-key="po_editable"
                label="Portée modifiable"
                icon="fa-solid fa-up-right-and-down-left-from-center"
                :helper="helperFor('po_editable')"
            >
                <label class="flex cursor-pointer items-center gap-2 text-xs">
                    <input
                        type="checkbox"
                        class="checkbox checkbox-sm"
                        :checked="Boolean(modelValue.po_editable)"
                        :disabled="readonly"
                        @change="patch('po_editable', $event.target.checked)"
                    />
                    <span>{{ modelValue.po_editable ? 'Oui' : 'Non' }}</span>
                </label>
            </SpellDegreePropertyField>

            <SpellDegreePropertyField
                characteristic-key="sight_line"
                label="Ligne de vue"
                icon="fa-solid fa-eye"
                :helper="helperFor('sight_line')"
            >
                <label class="flex cursor-pointer items-center gap-2 text-xs">
                    <input
                        type="checkbox"
                        class="checkbox checkbox-sm"
                        :checked="Boolean(modelValue.sight_line)"
                        :disabled="readonly"
                        @change="patch('sight_line', $event.target.checked)"
                    />
                    <span>{{ modelValue.sight_line ? 'Requise' : 'Non requise' }}</span>
                </label>
            </SpellDegreePropertyField>

            <SpellDegreePropertyField
                characteristic-key="global_cooldown"
                label="Temps de relance"
                icon="fa-solid fa-rotate"
                :helper="helperFor('global_cooldown')"
            >
                <input
                    v-if="!readonly"
                    :value="modelValue.global_cooldown ?? 0"
                    type="number"
                    min="0"
                    max="255"
                    class="input input-bordered input-sm w-full"
                    @input="patch('global_cooldown', Number($event.target.value))"
                />
                <p v-else class="text-sm font-medium tabular-nums">
                    {{ modelValue.global_cooldown ?? '—' }}
                </p>
            </SpellDegreePropertyField>

            <SpellDegreePropertyField
                characteristic-key="cast_per_turn"
                label="Lancers / tour"
                icon="fa-solid fa-repeat"
                :helper="helperFor('cast_per_turn')"
            >
                <input
                    v-if="!readonly"
                    :value="modelValue.cast_per_turn ?? ''"
                    type="text"
                    class="input input-bordered input-sm w-full"
                    @input="patch('cast_per_turn', $event.target.value)"
                />
                <p v-else class="text-sm font-medium tabular-nums">
                    {{ modelValue.cast_per_turn ?? '—' }}
                </p>
            </SpellDegreePropertyField>

            <SpellDegreePropertyField
                characteristic-key="cast_per_target"
                label="Lancers / cible"
                icon="fa-solid fa-crosshairs"
                :helper="helperFor('cast_per_target')"
            >
                <input
                    v-if="!readonly"
                    :value="modelValue.cast_per_target ?? ''"
                    type="text"
                    class="input input-bordered input-sm w-full"
                    @input="patch('cast_per_target', $event.target.value)"
                />
                <p v-else class="text-sm font-medium tabular-nums">
                    {{ modelValue.cast_per_target ?? '—' }}
                </p>
            </SpellDegreePropertyField>

            <SpellDegreePropertyField
                characteristic-key="number_between_two_cast"
                label="Délai entre lancers"
                icon="fa-solid fa-hourglass-half"
                :helper="helperFor('number_between_two_cast')"
            >
                <input
                    v-if="!readonly"
                    :value="modelValue.number_between_two_cast ?? ''"
                    type="text"
                    class="input input-bordered input-sm w-full"
                    @input="patch('number_between_two_cast', $event.target.value)"
                />
                <p v-else class="text-sm font-medium tabular-nums">
                    {{ modelValue.number_between_two_cast ?? '—' }}
                </p>
            </SpellDegreePropertyField>

            <SpellDegreePropertyField
                characteristic-key="casting_time"
                label="Temps d’incantation"
                icon="fa-solid fa-clock"
                :helper="helperFor('casting_time')"
            >
                <input
                    v-if="!readonly"
                    :value="modelValue.casting_time ?? ''"
                    type="text"
                    class="input input-bordered input-sm w-full"
                    placeholder="Instantané…"
                    @input="patch('casting_time', $event.target.value)"
                />
                <p v-else class="text-sm">{{ modelValue.casting_time || '—' }}</p>
            </SpellDegreePropertyField>
        </div>

        <SpellDegreePropertyField
            characteristic-key="area"
            label="Zone"
            icon="fa-solid fa-draw-polygon"
            :helper="helperFor('area')"
        >
            <AreaShapePicker
                v-if="!readonly"
                :model-value="modelValue.area || ''"
                @update:model-value="patch('area', $event)"
            />
            <p v-else class="text-sm">
                {{ getAreaHumanReadable(modelValue.area) || modelValue.area || '—' }}
            </p>
        </SpellDegreePropertyField>
    </div>
</template>
