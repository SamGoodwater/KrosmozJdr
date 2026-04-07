<script setup>
/**
 * Ligne de sous-effet (pivot effect_sub_effect) — action, valeurs, critique, durée.
 * Invocation : même vue texte que les monstres (`SpellSummonMonsterInline` / `EntityViewTextLink`) sur la ligne des badges.
 *
 * @props {Object} row - Objet sérialisé (SpellResource)
 */
import { computed } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import {
    resolveDef,
    getCharacteristicColorStyle,
} from '@/Composables/entity/useCharacteristicDisplay';
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

const params = computed(() =>
    props.row?.params && typeof props.row.params === 'object' ? props.row.params : {},
);

const actionLabel = computed(() => {
    const typeSlug = sub.value?.type_slug;
    const slug = sub.value?.slug;
    const ls =
        typeof params.value?.life_steal_formula === 'string' ? params.value.life_steal_formula.trim() : '';
    if ((slug === 'frapper' || typeSlug === 'frapper') && ls !== '') {
        return 'Vol de vie';
    }
    if (slug === 'frapper' || typeSlug === 'frapper') {
        return 'Dégâts';
    }
    if (typeSlug && ACTION_LABELS[typeSlug]) {
        return ACTION_LABELS[typeSlug];
    }
    if (slug && typeof slug === 'string') {
        return slug.replace(/_/g, ' ');
    }
    return 'Effet';
});

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
    const trimmed = key.trim();
    if (!trimmed) {
        return null;
    }
    return resolveDef(trimmed, undefined, {
        sourceGroups: ['spell', 'capability', 'creature'],
    });
});

/** Icône / couleur effectives (service `resolveDef`, variantes value_available / bool). */
const charDefDisplay = computed(() => {
    const d = charDef.value;
    if (!d) {
        return null;
    }
    const icon = d._resolvedIcon ?? d.icon ?? '';
    const color = d._resolvedColor ?? d.color;
    const label = d.short_name || d.name || '';
    if (!icon && !label) {
        return null;
    }
    return { icon, color, label };
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

const lifeStealFormulaText = computed(() => {
    const s = params.value?.life_steal_formula;
    return typeof s === 'string' && s.trim() !== '' ? s.trim() : '';
});
</script>

<template>
    <div class="overflow-visible py-2 border-b border-base-300/50 last:border-b-0">
        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 overflow-visible">
        <span
            v-if="row.crit_only"
            class="inline-flex items-center gap-1 rounded px-1.5 py-0.5 text-xs font-semibold uppercase tracking-wide bg-warning/20 text-warning"
        >
            <Icon source="fa-solid fa-bolt" alt="Critique" size="xs" />
            Critique
        </span>

        <span
            class="badge badge-sm badge-outline shrink-0 border-primary-400/45 text-primary-100 font-semibold normal-case capitalize"
        >
            {{ actionLabel }}
        </span>

        <template v-if="valueText || critFormula">
            <span
                v-if="valueText"
                class="badge badge-sm shrink-0 border-0 bg-primary-300/20 text-primary-100 tabular-nums font-medium"
            >
                {{ valueText }}
            </span>
            <span
                v-if="critFormula"
                class="badge badge-sm badge-outline shrink-0 border-warning/50 text-warning tabular-nums font-medium"
            >
                crit {{ critFormula }}
            </span>
        </template>

        <span
            v-if="lifeStealFormulaText"
            class="badge badge-sm badge-ghost shrink-0 text-xs tabular-nums text-primary-300"
            :title="'PV volés : ' + lifeStealFormulaText"
        >
            vol {{ lifeStealFormulaText }}
        </span>

        <!-- Vue texte monstre (icône + nom, survol → MonsterViewMinimal), alignée sur EntityViewTextLink -->
        <SpellSummonMonsterInline
            v-if="summonBrief"
            :monster-brief="summonBrief"
            class="min-w-0 max-w-full"
        />

        <span
            v-if="charDefDisplay"
            class="inline-flex items-center gap-1 text-sm"
            :style="charDefDisplay.color ? getCharacteristicColorStyle(charDefDisplay.color) : undefined"
        >
            <Icon
                v-if="charDefDisplay.icon"
                :source="charDefDisplay.icon"
                :alt="charDefDisplay.label"
                size="xs"
            />
            <span>{{ charDefDisplay.label }}</span>
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
    </div>
</template>
