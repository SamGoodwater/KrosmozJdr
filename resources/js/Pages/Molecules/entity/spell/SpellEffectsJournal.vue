<script setup>
/**
 * Bloc « effets » d’un sort : définitions, onglets par degré, zones, sous-effets (dont branches OU).
 *
 * @props {Array<Object>} definitions - `effects_definitions` (SpellResource)
 */
import { computed, ref, watch } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { getAreaIcon, getAreaShape, getAreaShapeLabel } from '@/Utils/Entity/Areas';
import { segmentSpellEffectRows } from '@/Composables/entity/useSpellEffectRowSegments';
import SpellSubEffectTypeRouter from '@/Pages/Molecules/entity/spell/SpellSubEffectTypeRouter.vue';

const TARGET_LABELS = Object.freeze({
    direct: 'Cible directe',
    trap: 'Piège',
    glyph: 'Glyphe',
});

const props = defineProps({
    definitions: {
        type: Array,
        default: () => [],
    },
});

const activeTabByEffect = ref({});

function tabKey(effectId) {
    return String(effectId ?? '0');
}

function degreeTitle(deg) {
    const lvl = deg?.required_creature_level;
    if (lvl != null && String(lvl).trim() !== '') {
        return `Niveau créature ${lvl}`;
    }
    const d = deg?.degree;
    if (d != null) {
        return `Degré ${d}`;
    }
    return 'Degré';
}

function segmentsForDegree(deg) {
    const rows = Array.isArray(deg?.rows) ? [...deg.rows] : [];
    return segmentSpellEffectRows(rows);
}

watch(
    () => props.definitions,
    (defs) => {
        const next = { ...activeTabByEffect.value };
        for (const def of defs || []) {
            const k = tabKey(def.id);
            const degrees = def.degrees || [];
            if (!degrees.length) {
                next[k] = 0;
                continue;
            }
            if (next[k] == null || next[k] >= degrees.length) {
                next[k] = 0;
            }
        }
        activeTabByEffect.value = next;
    },
    { immediate: true, deep: true },
);

const hasDefinitions = computed(() => Array.isArray(props.definitions) && props.definitions.length > 0);
</script>

<template>
    <section v-if="hasDefinitions" class="space-y-8">
        <div v-for="def in definitions" :key="def.id ?? def.name" class="space-y-3">
            <div class="space-y-1">
                <h3 v-if="def.name" class="text-lg font-semibold text-primary-100">
                    {{ def.name }}
                </h3>
                <p v-if="def.description" class="text-sm text-primary-300 whitespace-pre-wrap break-words">
                    {{ def.description }}
                </p>
                <div v-if="def.target_type" class="flex flex-wrap gap-2 pt-1">
                    <span
                        class="badge badge-outline badge-sm border-primary-400/40 text-primary-200"
                    >
                        {{ TARGET_LABELS[def.target_type] || def.target_type }}
                    </span>
                </div>
            </div>

            <div
                v-if="!def.degrees || def.degrees.length === 0"
                class="text-sm text-primary-400 italic"
            >
                Aucun degré défini pour cet effet.
            </div>

            <template v-else>
                <div role="tablist" class="tabs tabs-boxed flex-wrap gap-1 bg-base-200/60 p-1">
                    <button
                        v-for="(deg, idx) in def.degrees"
                        :key="deg.id ?? idx"
                        type="button"
                        role="tab"
                        class="tab tab-sm"
                        :class="{ 'tab-active': activeTabByEffect[tabKey(def.id)] === idx }"
                        @click="activeTabByEffect = { ...activeTabByEffect, [tabKey(def.id)]: idx }"
                    >
                        {{ degreeTitle(deg) }}
                    </button>
                </div>

                <div
                    v-for="(deg, idx) in def.degrees"
                    v-show="activeTabByEffect[tabKey(def.id)] === idx"
                    :key="`panel-${def.id}-${deg.id ?? idx}`"
                    class="rounded-box border border-base-300 bg-base-200/30 p-4 space-y-4"
                >
                    <div
                        v-if="deg.area"
                        class="flex flex-wrap items-center gap-2 text-sm text-primary-200"
                    >
                        <Icon
                            :source="getAreaIcon(deg.area)"
                            :alt="getAreaShapeLabel(getAreaShape(deg.area))"
                            size="sm"
                        />
                        <span class="font-medium">{{ getAreaShapeLabel(getAreaShape(deg.area)) }}</span>
                        <span class="text-primary-400 font-mono text-xs break-all">{{ deg.area }}</span>
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
                                    <SpellSubEffectTypeRouter :row="r" />
                                </template>
                            </div>
                            <div v-else class="space-y-0">
                                <SpellSubEffectTypeRouter v-for="r in seg.rows" :key="`seq-${r.order}`" :row="r" />
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </section>
</template>
