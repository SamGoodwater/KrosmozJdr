<script setup>
/**
 * Progression des degrés d’un sort : onglets niveau + effets + propriétés actives.
 * Préfère `spellDegrees` (canal natif) ; repli sur `effects_definitions` legacy.
 */
import { computed, ref, watch } from 'vue';
import AreaDisplay from '@/Pages/Molecules/entity/spell/AreaDisplay.vue';
import { getAreaHumanReadable } from '@/Utils/Entity/Areas';
import { segmentSpellEffectRows } from '@/Composables/entity/useSpellEffectRowSegments';
import SpellSubEffectTypeRouter from '@/Pages/Molecules/entity/spell/SpellSubEffectTypeRouter.vue';

const props = defineProps({
    /** Payload natif `{ degrees, default_degree_id }`. */
    spellDegrees: {
        type: Object,
        default: null,
    },
    /** Legacy `effects_definitions`. */
    definitions: {
        type: Array,
        default: () => [],
    },
    subEffectLayout: {
        type: String,
        default: 'large',
        validator: (v) => ['large', 'compact'].includes(v),
    },
});

const emit = defineEmits(['active-degree-change']);

const activeIndex = ref(0);

const nativeDegrees = computed(() => {
    const list = props.spellDegrees?.degrees;
    return Array.isArray(list) ? list : [];
});

const useNative = computed(() => nativeDegrees.value.length > 0);

/** Degrés unifiés pour l’UI (natif ou legacy aplati). */
const displayDegrees = computed(() => {
    if (useNative.value) {
        return nativeDegrees.value.map((d) => ({
            id: d.id,
            required_level: d.required_level,
            position: d.position,
            area: d.area ?? d.properties?.area,
            properties: d.properties || {},
            rows: d.rows || [],
        }));
    }

    // Legacy : aplatit la première définition (ou fusionne tous les degrés du 1er effet).
    const defs = Array.isArray(props.definitions) ? props.definitions : [];
    if (!defs.length) return [];
    const def = defs[0];
    return (def.degrees || []).map((deg) => ({
        id: deg.id,
        required_level: deg.required_creature_level,
        position: deg.degree,
        area: deg.area,
        properties: { area: deg.area },
        rows: deg.rows || [],
    }));
});

function degreeTitle(deg) {
    const lvl = Number(deg?.required_level);
    if (lvl === 13) return 'Palier I (perso 13)';
    if (lvl === 16) return 'Palier II (perso 16)';
    if (lvl === 20) return 'Palier III (perso 20)';
    if (!Number.isNaN(lvl) && lvl > 0) return `Niveau ${lvl}`;
    return `Degré ${deg?.position ?? '?'}`;
}

function segmentsForDegree(deg) {
    return segmentSpellEffectRows(Array.isArray(deg?.rows) ? [...deg.rows] : []);
}

watch(
    displayDegrees,
    (list) => {
        if (!list.length) {
            activeIndex.value = 0;
            emit('active-degree-change', null);
            return;
        }
        if (activeIndex.value >= list.length) {
            activeIndex.value = 0;
        }
        emit('active-degree-change', list[activeIndex.value] || null);
    },
    { immediate: true, deep: true },
);

watch(activeIndex, (idx) => {
    emit('active-degree-change', displayDegrees.value[idx] || null);
});

const hasContent = computed(() => displayDegrees.value.length > 0);
</script>

<template>
    <section v-if="hasContent" class="space-y-4" data-cy="spell-degrees-journal">
        <div role="tablist" class="tabs tabs-boxed flex-wrap gap-1 bg-base-200/60 p-1">
            <button
                v-for="(deg, idx) in displayDegrees"
                :key="deg.id ?? idx"
                type="button"
                role="tab"
                class="tab tab-sm"
                :class="{ 'tab-active': activeIndex === idx }"
                @click="activeIndex = idx"
            >
                {{ degreeTitle(deg) }}
            </button>
        </div>

        <div
            v-for="(deg, idx) in displayDegrees"
            v-show="activeIndex === idx"
            :key="`panel-${deg.id ?? idx}`"
            class="rounded-box border border-base-300 bg-base-200/30 p-4 space-y-4 overflow-visible"
        >
            <div
                v-if="deg.area"
                class="flex flex-wrap items-center gap-2 text-sm text-primary-200"
            >
                <AreaDisplay :area="deg.area" icon-size="sm" />
                <span class="text-primary-300">{{ getAreaHumanReadable(deg.area) }}</span>
            </div>

            <div class="space-y-0">
                <template v-for="(seg, si) in segmentsForDegree(deg)" :key="`seg-${si}`">
                    <div
                        v-if="seg.type === 'or'"
                        class="rounded-lg border border-secondary/50 bg-secondary/5 p-3 space-y-1"
                    >
                        <template v-for="(r, ri) in seg.rows" :key="`or-${ri}-${r.order}`">
                            <p
                                class="text-sm font-bold text-secondary"
                                :class="{ 'pt-2': ri > 0 }"
                            >
                                Soit
                            </p>
                            <SpellSubEffectTypeRouter
                                :row="r"
                                :layout="subEffectLayout"
                                :degree-area="deg.area"
                            />
                        </template>
                    </div>
                    <div v-else class="space-y-0">
                        <SpellSubEffectTypeRouter
                            v-for="r in seg.rows"
                            :key="`seq-${r.order}`"
                            :row="r"
                            :layout="subEffectLayout"
                            :degree-area="deg.area"
                        />
                    </div>
                </template>
            </div>
        </div>
    </section>
</template>
