<script setup>
/**
 * Rendu structuré d’un sous-effet (modèle unifié définition ou chip).
 *
 * @see docs/30-UI/SPELL_SUB_EFFECTS_DISPLAY.md
 *
 * @props {object} model - {@link buildUnifiedSubEffectModel}
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import AreaDisplay from "@/Pages/Molecules/entity/spell/AreaDisplay.vue";
import SpellSummonMonsterInline from "@/Pages/Molecules/entity/spell/SpellSummonMonsterInline.vue";
import ConditionInline from "@/Pages/Molecules/entity/condition/ConditionInline.vue";
import {
    resolveDef,
    getCharacteristicColorStyle,
    SPELL_EFFECT_CHIP_SOURCE_GROUPS,
} from "@/Composables/entity/useCharacteristicDisplay";
import { ELEMENT_PRIMARY_LABELS, getElementIcon, getElementColor } from "@/Utils/Entity/Elements";
import {
    subEffectScopeAbbrev,
    subEffectCritShowsWord,
    subEffectDurationSegment,
} from "@/Composables/entity/useSpellSubEffectPresentation";
import { formatDisplacementForDisplay } from "@/Utils/Entity/displacementFormat";

const props = defineProps({
    model: {
        type: Object,
        required: true,
    },
});

const m = computed(() => props.model ?? {});

const action = computed(() => {
    const s = m.value.actionSlug ?? "";
    if (s === "damage") {
        return "frapper";
    }
    if (s === "heal" || s === "heal_percent") {
        return "soigner";
    }
    return s;
});

const scopeTag = computed(() => subEffectScopeAbbrev(m.value.scope));
const critWord = computed(() => subEffectCritShowsWord(m.value.layout));
const durationSeg = computed(() =>
    subEffectDurationSegment(m.value.durationFormula, m.value.durationLabel),
);
const hasDuration = computed(() => durationSeg.value !== "");
const hasArea = computed(() => m.value.area != null && String(m.value.area).trim() !== "");

const textSize = computed(() =>
    m.value.layout === "minimal" || m.value.layout === "line" ? "text-xs" : "text-sm",
);

const charDef = computed(() => {
    const key = m.value.characteristic;
    if (!key || typeof key !== "string" || key.trim() === "") {
        return null;
    }
    return resolveDef(key.trim(), undefined, {
        sourceGroups: [...SPELL_EFFECT_CHIP_SOURCE_GROUPS],
    });
});

const charBlock = computed(() => {
    const d = charDef.value;
    if (!d) {
        return null;
    }
    const icon = d._resolvedIcon ?? d.icon ?? "";
    const color = d._resolvedColor ?? d.color;
    const label = d.short_name || d.name || "";
    if (!icon && !label) {
        return null;
    }
    return { icon, color, label };
});

const shieldDef = computed(() =>
    resolveDef("shield", undefined, {
        sourceGroups: [...SPELL_EFFECT_CHIP_SOURCE_GROUPS],
    }),
);

const shieldBlock = computed(() => {
    const d = shieldDef.value;
    if (!d) {
        return null;
    }
    return {
        icon: d._resolvedIcon ?? d.icon ?? "fa-solid fa-shield-halved",
        color: d._resolvedColor ?? d.color,
        label: "pts de bouclier",
    };
});

const elementNum = computed(() => {
    const el = m.value.element;
    if (el === null || el === undefined || el === "") {
        return null;
    }
    const n = Number(el);
    return Number.isFinite(n) ? n : null;
});

const elementStyle = computed(() => {
    if (elementNum.value == null) {
        return undefined;
    }
    const hex = getElementColor(elementNum.value);
    return hex ? getCharacteristicColorStyle(hex) : undefined;
});

const elementLabel = computed(() =>
    elementNum.value == null ? "" : (ELEMENT_PRIMARY_LABELS[elementNum.value] ?? ""),
);

const valueBadge = computed(() => {
    const v = m.value.valueDisplay;
    return v != null && String(v).trim() !== "" ? String(v).trim() : "";
});

const critBadge = computed(() => {
    const c = m.value.valueFormulaCrit;
    return typeof c === "string" && c.trim() !== "" ? c.trim() : "";
});

const showCritInline = computed(() => critBadge.value !== "");

const conditionInlineProps = computed(() => ({
    condition: m.value.condition,
    nameFallback: m.value.conditionName ?? "",
}));

const isFrapperWithLifeSteal = computed(
    () =>
        action.value === "frapper" &&
        typeof m.value.lifeStealFormula === "string" &&
        m.value.lifeStealFormula.trim() !== "",
);

const moveValue = computed(() => {
    const d = m.value.moveCellsDisplay;
    if (d == null || String(d).trim() === "") {
        return "";
    }
    return formatDisplacementForDisplay(String(d).trim());
});

const movementKindLabel = computed(() => {
    const kind = m.value.movementKind;
    if (kind === "jump") return "saut";
    if (kind === "teleport") return "téléportation";
    if (kind === "push") return "repousse";
    if (kind === "pull") return "attirance";
    if (m.value.teleport) return "téléportation";
    return "";
});

const KNOWN_ACTIONS = new Set([
    "appliquer-etat",
    "s-appliquer-etat",
    "autre",
    "booster",
    "déplacer",
    "frapper",
    "soigner",
    "invoquer",
    "protéger",
    "retirer",
    "voler-caracteristiques",
]);

const isKnownAction = computed(() => KNOWN_ACTIONS.has(action.value));
</script>

<template>
    <span
        class="inline-flex min-w-0 max-w-full flex-wrap items-center gap-x-1 gap-y-0.5 leading-snug text-left text-base-content"
        :class="textSize"
    >
        <!-- Portée (hors général) -->
        <span
            v-if="scopeTag"
            class="badge badge-ghost badge-xs shrink-0 font-mono uppercase tracking-tight"
            >{{ scopeTag }}</span
        >

        <!-- Sous-effet réservé au critique : préfixe commun -->
        <template v-if="m.critOnly">
            <Icon source="fa-solid fa-bolt" alt="Critique" size="xs" class="shrink-0 text-warning" />
            <span v-if="critWord" class="shrink-0 font-semibold text-warning">Critique&nbsp;:</span>
        </template>

        <!-- appliquer-etat -->
        <template v-if="action === 'appliquer-etat'">
            <span class="font-bold">Appliquer la condition</span>
            <span aria-hidden="true">:</span>
            <ConditionInline
                :condition="conditionInlineProps.condition"
                :name-fallback="conditionInlineProps.nameFallback"
                class="min-w-0"
            />
            <template v-if="hasDuration">
                <span class="text-base-content/70">—</span>
                <span class="text-base-content/80">Durée {{ durationSeg }}</span>
            </template>
            <template v-if="hasArea">
                <span class="shrink-0 text-base-content/40" aria-hidden="true">|</span>
                <AreaDisplay :area="m.area" icon-only icon-size="sm" class="shrink-0" />
            </template>
        </template>

        <!-- s-appliquer-etat -->
        <template v-if="action === 's-appliquer-etat'">
            <span class="font-bold">Appliquer la condition</span>
            <span aria-hidden="true">:</span>
            <ConditionInline
                :condition="conditionInlineProps.condition"
                :name-fallback="conditionInlineProps.nameFallback"
                class="min-w-0"
            />
            <span class="text-base-content/80">à soit-même</span>
            <template v-if="hasDuration">
                <span class="text-base-content/70">—</span>
                <span class="text-base-content/80">Durée {{ durationSeg }}</span>
            </template>
        </template>

        <!-- autre -->
        <template v-if="action === 'autre'">
            <span class="tabular-nums font-medium">{{ m.textFallback || valueBadge || "—" }}</span>
            <template v-if="showCritInline">
                <Icon source="fa-solid fa-bolt" alt="Critique" size="xs" class="shrink-0 text-warning" />
                <span
                    class="badge badge-sm shrink-0 border-warning/50 tabular-nums font-medium text-warning badge-outline"
                    >{{ critBadge }}</span
                >
            </template>
            <template v-if="hasDuration">
                <span class="text-base-content/70">—</span>
                <span class="text-base-content/80">Durée {{ durationSeg }}</span>
            </template>
            <template v-if="hasArea">
                <span class="shrink-0 text-base-content/40" aria-hidden="true">|</span>
                <AreaDisplay :area="m.area" icon-only icon-size="sm" class="shrink-0" />
            </template>
        </template>

        <!-- booster -->
        <template v-if="action === 'booster'">
            <span class="font-bold">Ajout</span>
            <span class="text-base-content/80">de</span>
            <span
                v-if="valueBadge"
                class="badge badge-sm shrink-0 border-0 bg-primary-300/20 font-medium text-primary-100 tabular-nums"
                >{{ valueBadge }}</span
            >
            <template v-if="showCritInline">
                <Icon source="fa-solid fa-bolt" alt="Critique" size="xs" class="shrink-0 text-warning" />
                <span
                    class="badge badge-sm shrink-0 border-warning/50 tabular-nums font-medium text-warning badge-outline"
                    >{{ critBadge }}</span
                >
            </template>
            <span class="text-base-content/80">en</span>
            <span
                v-if="charBlock"
                class="inline-flex items-center gap-1 font-medium"
                :style="charBlock.color ? getCharacteristicColorStyle(charBlock.color) : undefined"
            >
                <Icon
                    v-if="charBlock.icon"
                    :source="charBlock.icon"
                    :alt="charBlock.label"
                    size="xs"
                    class="shrink-0"
                />
                <span>{{ charBlock.label }}</span>
            </span>
            <template v-if="hasDuration">
                <span class="text-base-content/70">—</span>
                <span class="text-base-content/80">Durée {{ durationSeg }}</span>
            </template>
            <template v-if="hasArea">
                <span class="shrink-0 text-base-content/40" aria-hidden="true">|</span>
                <AreaDisplay :area="m.area" icon-only icon-size="sm" class="shrink-0" />
            </template>
        </template>

        <!-- déplacer -->
        <template v-if="action === 'déplacer'">
            <span class="font-bold">Déplacement</span>
            <span class="text-base-content/80">de</span>
            <span
                v-if="moveValue"
                class="badge badge-sm shrink-0 border-0 bg-primary-300/20 font-medium text-primary-100 tabular-nums"
                >{{ moveValue }}</span
            >
            <span v-if="movementKindLabel" class="text-base-content/80">{{ movementKindLabel }}</span>
            <template v-if="hasDuration">
                <span class="text-base-content/70">—</span>
                <span class="text-base-content/80">Durée {{ durationSeg }}</span>
            </template>
            <template v-if="hasArea">
                <span class="shrink-0 text-base-content/40" aria-hidden="true">|</span>
                <AreaDisplay :area="m.area" icon-only icon-size="sm" class="shrink-0" />
            </template>
        </template>

        <!-- frapper -->
        <template v-if="action === 'frapper'">
            <span class="font-bold">Attaque</span>
            <span aria-hidden="true">:</span>
            <span
                v-if="valueBadge"
                class="badge badge-sm shrink-0 border-0 bg-primary-300/20 font-medium text-primary-100 tabular-nums"
                >{{ valueBadge }}</span
            >
            <template v-if="showCritInline">
                <Icon source="fa-solid fa-bolt" alt="Critique" size="xs" class="shrink-0 text-warning" />
                <span
                    class="badge badge-sm shrink-0 border-warning/50 tabular-nums font-medium text-warning badge-outline"
                    >{{ critBadge }}</span
                >
            </template>
            <template v-if="elementNum != null">
                <span class="text-base-content/60">(</span>
                <span
                    class="inline-flex items-center gap-1 font-medium"
                    :style="elementStyle"
                >
                    <Icon
                        :source="getElementIcon(elementNum)"
                        :alt="elementLabel"
                        size="xs"
                        class="shrink-0"
                    />
                    <span>{{ elementLabel }}</span>
                </span>
                <span class="text-base-content/60">)</span>
            </template>
            <template v-if="isFrapperWithLifeSteal">
                <span class="text-base-content/70">—</span>
                <span class="font-bold">Ajout de vol de vie</span>
                <span
                    class="badge badge-sm shrink-0 border-0 bg-secondary/20 font-medium text-secondary tabular-nums"
                    >{{ m.lifeStealFormula }}</span
                >
            </template>
            <template v-if="hasDuration">
                <span class="text-base-content/70">—</span>
                <span class="font-semibold text-base-content/90">Poison&nbsp;:</span>
                <span class="text-base-content/80">{{ durationSeg }}</span>
            </template>
            <template v-if="hasArea">
                <span class="shrink-0 text-base-content/40" aria-hidden="true">|</span>
                <AreaDisplay :area="m.area" icon-only icon-size="sm" class="shrink-0" />
            </template>
        </template>

        <!-- soigner -->
        <template v-if="action === 'soigner'">
            <span class="font-bold">Soin</span>
            <span aria-hidden="true">:</span>
            <span
                v-if="valueBadge"
                class="badge badge-sm shrink-0 border-0 bg-primary-300/20 font-medium text-primary-100 tabular-nums"
                >{{ valueBadge }}</span
            >
            <template v-if="showCritInline">
                <Icon source="fa-solid fa-bolt" alt="Critique" size="xs" class="shrink-0 text-warning" />
                <span
                    class="badge badge-sm shrink-0 border-warning/50 tabular-nums font-medium text-warning badge-outline"
                    >{{ critBadge }}</span
                >
            </template>
            <template v-if="elementNum != null">
                <span class="text-base-content/60">(</span>
                <span
                    class="inline-flex items-center gap-1 font-medium"
                    :style="elementStyle"
                >
                    <Icon
                        :source="getElementIcon(elementNum)"
                        :alt="elementLabel"
                        size="xs"
                        class="shrink-0"
                    />
                    <span>{{ elementLabel }}</span>
                </span>
                <span class="text-base-content/60">)</span>
            </template>
            <template v-if="hasDuration">
                <span class="text-base-content/70">—</span>
                <span class="font-semibold text-base-content/90">Durée&nbsp;:</span>
                <span class="text-base-content/80">{{ durationSeg }}</span>
            </template>
            <template v-if="hasArea">
                <span class="shrink-0 text-base-content/40" aria-hidden="true">|</span>
                <AreaDisplay :area="m.area" icon-only icon-size="sm" class="shrink-0" />
            </template>
        </template>

        <!-- invoquer -->
        <template v-if="action === 'invoquer'">
            <span class="font-bold">Invocation</span>
            <span class="text-base-content/80">de</span>
            <SpellSummonMonsterInline
                v-if="m.summonMonster"
                :monster-brief="m.summonMonster"
                class="min-w-0"
            />
        </template>

        <!-- protéger -->
        <template v-if="action === 'protéger'">
            <span class="font-bold">Gain de</span>
            <span
                v-if="valueBadge"
                class="badge badge-sm shrink-0 border-0 bg-primary-300/20 font-medium text-primary-100 tabular-nums"
                >{{ valueBadge }}</span
            >
            <template v-if="showCritInline">
                <Icon source="fa-solid fa-bolt" alt="Critique" size="xs" class="shrink-0 text-warning" />
                <span
                    class="badge badge-sm shrink-0 border-warning/50 tabular-nums font-medium text-warning badge-outline"
                    >{{ critBadge }}</span
                >
            </template>
            <span class="text-base-content/80">de</span>
            <span
                v-if="shieldBlock"
                class="inline-flex items-center gap-1 font-medium"
                :style="shieldBlock.color ? getCharacteristicColorStyle(shieldBlock.color) : undefined"
            >
                <Icon
                    :source="shieldBlock.icon"
                    :alt="shieldBlock.label"
                    size="xs"
                    class="shrink-0"
                />
                <span>{{ shieldBlock.label }}</span>
            </span>
            <template v-if="hasDuration">
                <span class="text-base-content/70">—</span>
                <span class="text-base-content/80">Durée {{ durationSeg }}</span>
            </template>
            <template v-if="hasArea">
                <span class="shrink-0 text-base-content/40" aria-hidden="true">|</span>
                <AreaDisplay :area="m.area" icon-only icon-size="sm" class="shrink-0" />
            </template>
        </template>

        <!-- retirer -->
        <template v-if="action === 'retirer'">
            <span class="font-bold">Retrait</span>
            <span class="text-base-content/80">de</span>
            <span
                v-if="valueBadge"
                class="badge badge-sm shrink-0 border-0 bg-primary-300/20 font-medium text-primary-100 tabular-nums"
                >{{ valueBadge }}</span
            >
            <template v-if="showCritInline">
                <Icon source="fa-solid fa-bolt" alt="Critique" size="xs" class="shrink-0 text-warning" />
                <span
                    class="badge badge-sm shrink-0 border-warning/50 tabular-nums font-medium text-warning badge-outline"
                    >{{ critBadge }}</span
                >
            </template>
            <span class="text-base-content/80">en</span>
            <span
                v-if="charBlock"
                class="inline-flex items-center gap-1 font-medium"
                :style="charBlock.color ? getCharacteristicColorStyle(charBlock.color) : undefined"
            >
                <Icon
                    v-if="charBlock.icon"
                    :source="charBlock.icon"
                    :alt="charBlock.label"
                    size="xs"
                    class="shrink-0"
                />
                <span>{{ charBlock.label }}</span>
            </span>
            <template v-if="hasDuration">
                <span class="text-base-content/70">—</span>
                <span class="text-base-content/80">Durée {{ durationSeg }}</span>
            </template>
            <template v-if="hasArea">
                <span class="shrink-0 text-base-content/40" aria-hidden="true">|</span>
                <AreaDisplay :area="m.area" icon-only icon-size="sm" class="shrink-0" />
            </template>
        </template>

        <!-- voler-caracteristiques -->
        <template v-if="action === 'voler-caracteristiques'">
            <span class="font-bold">Vol</span>
            <span class="text-base-content/80">de</span>
            <span
                v-if="valueBadge"
                class="badge badge-sm shrink-0 border-0 bg-primary-300/20 font-medium text-primary-100 tabular-nums"
                >{{ valueBadge }}</span
            >
            <template v-if="showCritInline">
                <Icon source="fa-solid fa-bolt" alt="Critique" size="xs" class="shrink-0 text-warning" />
                <span
                    class="badge badge-sm shrink-0 border-warning/50 tabular-nums font-medium text-warning badge-outline"
                    >{{ critBadge }}</span
                >
            </template>
            <span class="text-base-content/80">en</span>
            <span
                v-if="charBlock"
                class="inline-flex items-center gap-1 font-medium"
                :style="charBlock.color ? getCharacteristicColorStyle(charBlock.color) : undefined"
            >
                <Icon
                    v-if="charBlock.icon"
                    :source="charBlock.icon"
                    :alt="charBlock.label"
                    size="xs"
                    class="shrink-0"
                />
                <span>{{ charBlock.label }}</span>
            </span>
            <template v-if="hasDuration">
                <span class="text-base-content/70">—</span>
                <span class="text-base-content/80">Durée {{ durationSeg }}</span>
            </template>
            <template v-if="hasArea">
                <span class="shrink-0 text-base-content/40" aria-hidden="true">|</span>
                <AreaDisplay :area="m.area" icon-only icon-size="sm" class="shrink-0" />
            </template>
        </template>

        <!-- inconnu / repli -->
        <template v-if="!isKnownAction">
            <span class="font-medium">{{ m.textFallback || valueBadge || "—" }}</span>
            <template v-if="hasDuration">
                <span class="text-base-content/70">—</span>
                <span class="text-base-content/80">Durée {{ durationSeg }}</span>
            </template>
            <template v-if="hasArea">
                <span class="shrink-0 text-base-content/40" aria-hidden="true">|</span>
                <AreaDisplay :area="m.area" icon-only icon-size="sm" class="shrink-0" />
            </template>
        </template>
    </span>
</template>
