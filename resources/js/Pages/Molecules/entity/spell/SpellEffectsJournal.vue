<script setup>
/**
 * Progression des degrés d’un sort : effets du degré actif (+ onglets optionnels).
 * Préfère `spellDegrees` (canal natif) ; repli sur `effects_definitions` legacy.
 * Groupe les lignes par tour de déclenchement quand au moins une est différée.
 */
import { computed, ref, watch } from 'vue';
import SpellDegreeTabs from '@/Pages/Molecules/entity/spell/SpellDegreeTabs.vue';
import { groupSpellEffectRowsByTriggerTurn } from '@/Composables/entity/useSpellEffectRowSegments';
import { buildSpellDisplayDegrees } from '@/Composables/entity/useSpellDisplayDegrees';
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
        validator: (v) => ['large', 'compact', 'line', 'minimal'].includes(v),
    },
    /** Panneau sans bordure / padding (carte minimale). */
    flush: {
        type: Boolean,
        default: false,
    },
    /** Masque la barre d’onglets (contrôlée par le parent). */
    hideTabs: {
        type: Boolean,
        default: false,
    },
    /** Index du degré actif (contrôlé). */
    modelValue: {
        type: Number,
        default: undefined,
    },
});

const emit = defineEmits(['active-degree-change', 'update:modelValue']);

const internalIndex = ref(0);

const isControlled = computed(() => props.modelValue !== undefined && props.modelValue !== null);

const activeIndex = computed({
    get() {
        return isControlled.value ? Number(props.modelValue) || 0 : internalIndex.value;
    },
    set(v) {
        const idx = Number(v) || 0;
        if (isControlled.value) {
            emit('update:modelValue', idx);
        } else {
            internalIndex.value = idx;
        }
    },
});

const displayDegrees = computed(() =>
    buildSpellDisplayDegrees(props.spellDegrees, props.definitions),
);

const hasContent = computed(() => displayDegrees.value.length > 0);

const activeDegree = computed(() => displayDegrees.value[activeIndex.value] || null);

const effectGroups = computed(() => {
    const deg = activeDegree.value;
    const rows = Array.isArray(deg?.rows) ? [...deg.rows] : [];
    return groupSpellEffectRowsByTriggerTurn(rows);
});

const showTriggerGroupHeaders = computed(() =>
    effectGroups.value.some((g) => g.triggerTurn > 0),
);

watch(
    displayDegrees,
    (list) => {
        if (!list.length) {
            if (!isControlled.value) {
                internalIndex.value = 0;
            }
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
</script>

<template>
    <section v-if="hasContent" class="space-y-4" data-cy="spell-degrees-journal">
        <SpellDegreeTabs
            v-if="!hideTabs"
            v-model="activeIndex"
            :degrees="displayDegrees"
        />

        <div
            v-if="activeDegree"
            class="space-y-2 overflow-visible"
            :class="flush ? '' : 'rounded-box border border-base-300 bg-base-200/30 p-4 space-y-4'"
        >
            <div class="space-y-4">
                <div
                    v-for="(group, gi) in effectGroups"
                    :key="`tg-${group.triggerTurn}-${gi}`"
                    class="space-y-2"
                    data-cy="spell-effect-trigger-group"
                >
                    <h4
                        v-if="showTriggerGroupHeaders"
                        class="text-xs font-semibold uppercase tracking-wide text-base-content/60"
                        data-cy="spell-effect-trigger-label"
                    >
                        {{ group.label }}
                    </h4>
                    <template v-for="(seg, si) in group.segments" :key="`seg-${gi}-${si}`">
                        <div
                            v-if="seg.type === 'or'"
                            class="rounded-lg border border-secondary/50 bg-secondary/5 p-3 space-y-2"
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
                                    :degree-area="activeDegree.area"
                                    :hide-trigger-turn="true"
                                />
                            </template>
                        </div>
                        <div v-else class="space-y-2">
                            <SpellSubEffectTypeRouter
                                v-for="r in seg.rows"
                                :key="`seq-${r.order}`"
                                :row="r"
                                :layout="subEffectLayout"
                                :degree-area="activeDegree.area"
                                :hide-trigger-turn="true"
                            />
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </section>
</template>
