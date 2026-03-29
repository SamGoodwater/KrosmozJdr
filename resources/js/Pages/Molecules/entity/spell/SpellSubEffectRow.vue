<script setup>
/**
 * Ligne de sous-effet (pivot effect_sub_effect) — action, valeurs, critique, durée.
 *
 * @props {Object} row - Objet sérialisé (SpellResource)
 */
import { computed } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { getByCharacteristicKey } from '@/Composables/store/useCharacteristicsStore';
import { getCharacteristicColorStyle } from '@/Composables/entity/useCharacteristicDisplay';
import { getElementLabel, getElementIcon, getElementColor } from '@/Utils/Entity/Elements';
import SpellSummonMonsterInline from '@/Pages/Molecules/entity/spell/SpellSummonMonsterInline.vue';

const ACTION_LABELS = Object.freeze({
    damage: 'Dégâts',
    heal: 'Soin',
    heal_percent: 'Soin (%)',
    steal_pa: 'Vol de PA',
    steal_mp: 'Vol de PM',
    push: 'Repousse',
    pull: 'Attire',
    teleport: 'Téléportation',
    summon: 'Invocation',
    invocation: 'Invocation',
    state: 'État',
    armor: 'Armure',
    shield: 'Bouclier',
});

const props = defineProps({
    row: {
        type: Object,
        required: true,
    },
});

const sub = computed(() => props.row?.sub_effect ?? null);

const actionLabel = computed(() => {
    const typeSlug = sub.value?.type_slug;
    if (typeSlug && ACTION_LABELS[typeSlug]) {
        return ACTION_LABELS[typeSlug];
    }
    const slug = sub.value?.slug;
    if (slug && typeof slug === 'string') {
        return slug.replace(/_/g, ' ');
    }
    return 'Effet';
});

const params = computed(() =>
    props.row?.params && typeof props.row.params === 'object' ? props.row.params : {},
);

/** Résumé monstre (API) ou repli sur `monster_id` pour type invocation. */
const summonBrief = computed(() => {
    const sm = props.row?.summon_monster;
    if (sm?.id != null) {
        return sm;
    }
    const slug = sub.value?.type_slug;
    if (slug !== 'summon' && slug !== 'invocation') {
        return null;
    }
    const id = Number(params.value?.monster_id);
    if (!Number.isFinite(id)) {
        return null;
    }
    return { id, name: `Monstre #${id}`, image: null };
});

function formatPivotValue(row) {
    const p = row?.params && typeof row.params === 'object' ? row.params : {};
    const vf = p.value_formula;
    if (typeof vf === 'string' && vf.trim() !== '') {
        return vf.trim();
    }
    const dn = row?.dice_num;
    const ds = row?.dice_side;
    if (dn != null && ds != null) {
        return `${dn}d${ds}`;
    }
    const vmin = row?.value_min;
    const vmax = row?.value_max;
    if (vmin != null && vmax != null) {
        if (Number(vmin) === Number(vmax)) {
            return String(vmin);
        }
        return `${vmin}–${vmax}`;
    }
    if (vmin != null) {
        return String(vmin);
    }
    return '';
}

const valueText = computed(() => {
    if (summonBrief.value) {
        const vf = params.value?.value_formula;
        if (typeof vf === 'string' && vf.trim() !== '') {
            return vf.trim();
        }
        const dn = props.row?.dice_num;
        const ds = props.row?.dice_side;
        if (dn != null && ds != null) {
            return `${dn}d${ds}`;
        }
        return '';
    }
    return formatPivotValue(props.row);
});

const critFormula = computed(() => {
    const c = params.value?.value_formula_crit;
    return typeof c === 'string' && c.trim() !== '' ? c.trim() : '';
});

const charDef = computed(() => {
    const key = params.value?.characteristic;
    if (!key || typeof key !== 'string') {
        return null;
    }
    return getByCharacteristicKey('spell', key) ?? getByCharacteristicKey('creature', key);
});

const elementParam = computed(() => {
    const el = params.value?.element;
    if (el === null || el === undefined || el === '') {
        return null;
    }
    const n = Number(el);
    return Number.isFinite(n) ? n : null;
});

const elementStyle = computed(() => {
    if (elementParam.value == null) {
        return undefined;
    }
    const hex = getElementColor(elementParam.value);
    return hex ? getCharacteristicColorStyle(hex) : undefined;
});

const durationText = computed(() => {
    const d = props.row?.duration_formula;
    return typeof d === 'string' && d.trim() !== '' ? d.trim() : '';
});
</script>

<template>
    <div class="py-2 border-b border-base-300/50 last:border-b-0 space-y-2">
    <div
        class="flex flex-wrap items-baseline gap-x-2 gap-y-1"
    >
        <span
            v-if="row.crit_only"
            class="inline-flex items-center gap-1 rounded px-1.5 py-0.5 text-xs font-semibold uppercase tracking-wide bg-warning/20 text-warning"
        >
            <Icon source="fa-solid fa-bolt" alt="Critique" size="xs" />
            Critique
        </span>

        <span class="font-semibold text-primary-100 capitalize">{{ actionLabel }}</span>

        <template v-if="valueText || critFormula">
            <span v-if="valueText" class="text-primary-200 tabular-nums">{{ valueText }}</span>
            <span v-if="critFormula" class="text-primary-300 tabular-nums">({{ critFormula }})</span>
        </template>

        <span
            v-if="charDef"
            class="inline-flex items-center gap-1 text-sm"
            :style="charDef.color ? getCharacteristicColorStyle(charDef.color) : undefined"
        >
            <Icon
                v-if="charDef.icon"
                :source="charDef.icon"
                :alt="charDef.short_name || charDef.name || ''"
                size="xs"
            />
            <span>{{ charDef.short_name || charDef.name }}</span>
        </span>

        <span
            v-if="elementParam != null"
            class="inline-flex items-center gap-1 text-sm"
            :style="elementStyle"
        >
            <Icon :source="getElementIcon(elementParam)" :alt="getElementLabel(elementParam)" size="xs" />
            <span>{{ getElementLabel(elementParam) }}</span>
        </span>

        <span
            v-if="durationText"
            class="inline-flex items-center gap-1 text-sm text-primary-300 ml-auto"
        >
            <Icon source="fa-solid fa-clock" alt="Durée" size="xs" />
            <span class="tabular-nums">{{ durationText }}</span>
        </span>
    </div>
    <div v-if="summonBrief" class="pl-2 ml-1 border-l-2 border-primary/25 text-sm">
        <SpellSummonMonsterInline :monster-brief="summonBrief" />
    </div>
    </div>
</template>
