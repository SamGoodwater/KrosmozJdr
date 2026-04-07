<script setup>
/**
 * Route vers le présentateur adapté au `type_slug` du sous-effet.
 *
 * @props {Object} row
 */
import { computed } from 'vue';
import SpellSubEffectRow from '@/Pages/Molecules/entity/spell/SpellSubEffectRow.vue';
import SpellSubEffectDamagePresenter from '@/Pages/Molecules/entity/spell/presenters/SpellSubEffectDamagePresenter.vue';
import SpellSubEffectHealPresenter from '@/Pages/Molecules/entity/spell/presenters/SpellSubEffectHealPresenter.vue';

const props = defineProps({
    row: {
        type: Object,
        required: true,
    },
    layout: {
        type: String,
        default: "large",
        validator: (v) => ["large", "compact", "line", "minimal"].includes(v),
    },
    degreeArea: {
        type: [String, null],
        default: null,
    },
});

const presenters = {
    damage: SpellSubEffectDamagePresenter,
    heal: SpellSubEffectHealPresenter,
    heal_percent: SpellSubEffectHealPresenter,
};

const resolved = computed(() => {
    const slug = props.row?.sub_effect?.type_slug;
    return (slug && presenters[slug]) || SpellSubEffectRow;
});
</script>

<template>
    <component :is="resolved" :row="row" :layout="layout" :degree-area="degreeArea" />
</template>
