<script setup>
/**
 * Rendu structuré d’un sous-effet.
 *
 * Ligne 1 : badge d’action (gris), badge d’élément coloré s’il y en a un, flèche, formule.
 * Le critique reste tel quel et passe à la ligne suivante quand la largeur manque.
 * Ligne 2 : durée et portée.
 *
 * @see docs/features/effects/README.md
 *
 * @props {object} model - {@link buildUnifiedSubEffectModel}
 *
 * @example
 * <SpellSubEffectActionPresentation :model="model" />
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import SpellSummonMonsterInline from "@/Pages/Molecules/entity/spell/SpellSummonMonsterInline.vue";
import SpellSummonMonsterMinimal from "@/Pages/Molecules/entity/spell/SpellSummonMonsterMinimal.vue";
import SpellCharacteristicHoverLink from "@/Pages/Molecules/entity/spell/SpellCharacteristicHoverLink.vue";
import SpellFormulaPartsInline from "@/Pages/Molecules/entity/spell/SpellFormulaPartsInline.vue";
import ConditionInline from "@/Pages/Molecules/entity/condition/ConditionInline.vue";
import {
    resolveDef,
    getCharacteristicColorStyle,
    SPELL_EFFECT_CHIP_SOURCE_GROUPS,
} from "@/Composables/entity/useCharacteristicDisplay";
import {
    ELEMENT_PRIMARY_CSS_VARS,
    ELEMENT_PRIMARY_LABELS,
    getElementIcon,
} from "@/Utils/Entity/Elements";
import {
    subEffectScopeAbbrev,
    subEffectDurationSegment,
} from "@/Composables/entity/useSpellSubEffectPresentation";
import { formatDisplacementForDisplay } from "@/Utils/Entity/displacementFormat";
import {
    abbreviateDamageFormula,
    expandShortAbilityTokens,
    parseFormulaToParts,
    resolveCritFormula,
} from "@/Utils/Entity/spellFormulaDisplay";
import {
    SPELL_ACTIONS_CHARACTERISTIC_KEY,
    spellActionBadgeStyle,
} from "@/Utils/Entity/spellActionCharacteristics";

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

const layout = computed(() => m.value.layout ?? "large");
const isMinimalLayout = computed(() => layout.value === "minimal" || layout.value === "line");

const scopeTag = computed(() => subEffectScopeAbbrev(m.value.scope));
const durationSeg = computed(() =>
    subEffectDurationSegment(m.value.durationFormula, m.value.durationLabel),
);
const hasDuration = computed(() => durationSeg.value !== "");
const hasParamsLine = computed(() => hasDuration.value || scopeTag.value != null);

const textSize = computed(() => (layout.value === "line" ? "text-xs" : "text-sm"));
const badgeSizeClass = computed(() => (layout.value === "line" ? "badge-xs" : "badge-sm"));

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

const tempHpDef = computed(() =>
    resolveDef("pv_temporaires_spell", undefined, {
        sourceGroups: [...SPELL_EFFECT_CHIP_SOURCE_GROUPS],
    }),
);

const tempHpBlock = computed(() => {
    const d = tempHpDef.value;
    return {
        icon: d?._resolvedIcon ?? d?.icon ?? "lifeTemp.webp",
        color: d?._resolvedColor ?? d?.color,
        label: "PV temporaires",
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

const elementBadgeStyle = computed(() => {
    if (elementNum.value == null) {
        return undefined;
    }
    const cssVar = ELEMENT_PRIMARY_CSS_VARS[elementNum.value] ?? ELEMENT_PRIMARY_CSS_VARS[0];
    const tone = `color-mix(in srgb, ${cssVar} 58%, white)`;
    return {
        color: tone,
        borderColor: tone,
        backgroundColor: "transparent",
    };
});

const elementLabel = computed(() =>
    elementNum.value == null ? "" : (ELEMENT_PRIMARY_LABELS[elementNum.value] ?? ""),
);

const showElement = computed(
    () =>
        elementNum.value != null &&
        (action.value === "frapper" ||
            action.value === "soigner" ||
            action.value === "protéger" ||
            action.value === "donner-pv-temporaires"),
);

const rawValueFormula = computed(() => {
    const vf = m.value.valueFormula;
    if (typeof vf === "string" && vf.trim() !== "") {
        return vf.trim();
    }
    const vd = m.value.valueDisplay;
    return vd != null && String(vd).trim() !== "" ? String(vd).trim() : "";
});

const isDamageAction = computed(() => action.value === "frapper");

const damageAbbrev = computed(() => {
    if (!isDamageAction.value || !rawValueFormula.value) {
        return null;
    }
    return abbreviateDamageFormula(rawValueFormula.value, m.value.characteristic);
});

const plainFormulaParts = computed(() => {
    if (isDamageAction.value) {
        return null;
    }
    const expanded = expandShortAbilityTokens(rawValueFormula.value);
    return parseFormulaToParts(expanded);
});

const critRaw = computed(() =>
    resolveCritFormula({
        actionSlug: action.value,
        valueFormula: rawValueFormula.value,
        valueFormulaCrit: m.value.valueFormulaCrit,
    }),
);

const critAbbrev = computed(() => {
    if (!critRaw.value) return null;
    if (isDamageAction.value) {
        return abbreviateDamageFormula(critRaw.value, m.value.characteristic);
    }
    return {
        visible: parseFormulaToParts(expandShortAbilityTokens(critRaw.value)),
        full: parseFormulaToParts(expandShortAbilityTokens(critRaw.value)),
        hasBonus: false,
    };
});

const showCrit = computed(() => Boolean(critAbbrev.value?.visible?.length));

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

const isFrapperWithLifeSteal = computed(
    () =>
        action.value === "frapper" &&
        typeof m.value.lifeStealFormula === "string" &&
        m.value.lifeStealFormula.trim() !== "",
);

const lifeStealParts = computed(() => {
    if (!isFrapperWithLifeSteal.value) return [];
    return parseFormulaToParts(expandShortAbilityTokens(m.value.lifeStealFormula));
});

const conditionInlineProps = computed(() => ({
    condition: m.value.condition,
    nameFallback: m.value.conditionName ?? "",
}));

const actionTitle = computed(() => {
    switch (action.value) {
        case "appliquer-etat":
        case "s-appliquer-etat":
            return "Appliquer la condition";
        case "autre":
            return "Effet";
        case "booster":
            return "Ajout";
        case "déplacer":
            return "Déplacement";
        case "frapper":
            return "Attaque";
        case "soigner":
            return "Soin";
        case "invoquer":
            return "Invocation";
        case "protéger":
            return "Gain";
        case "donner-pv-temporaires":
            return "Gain";
        case "retirer":
            return "Retrait";
        case "voler-caracteristiques":
            return "Vol";
        default:
            return m.value.actionSlug || "Effet";
    }
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
    "donner-pv-temporaires",
    "retirer",
    "voler-caracteristiques",
]);

const isKnownAction = computed(() => KNOWN_ACTIONS.has(action.value));

const actionDef = computed(() =>
    resolveDef(SPELL_ACTIONS_CHARACTERISTIC_KEY, action.value, { sourceGroups: ["spell"] }),
);

const actionLabel = computed(() => {
    const subtitle = actionDef.value?._resolvedSubtitle;
    if (typeof subtitle === "string" && subtitle.trim() !== "") {
        return subtitle.trim();
    }
    return actionTitle.value;
});

const actionIcon = computed(() => {
    const icon = actionDef.value?._resolvedIcon ?? actionDef.value?.icon;
    return typeof icon === "string" && icon.trim() !== "" ? icon.trim() : "";
});

const actionBadgeStyle = computed(() =>
    spellActionBadgeStyle(actionDef.value?._resolvedColor ?? actionDef.value?.color),
);

const durationLabel = computed(() => {
    if (action.value === "frapper" && hasDuration.value) {
        return `Poison : ${durationSeg.value}`;
    }
    return hasDuration.value ? `Durée ${durationSeg.value}` : "";
});
</script>

<template>
    <div
        class="spell-sub-effect-row flex w-full min-w-0 flex-col gap-0.5"
        :class="textSize"
        data-cy="spell-sub-effect-row"
    >
        <!-- Ligne 1 : badges action + élément → formule | critique (peut passer dessous) -->
        <div class="flex w-full min-w-0 flex-wrap items-center gap-x-3 gap-y-1">
            <div class="mr-auto inline-flex min-w-0 max-w-full flex-wrap items-center gap-x-1.5 gap-y-0.5">
                <template v-if="m.critOnly">
                    <Icon source="fa-solid fa-bolt" alt="Critique" size="xs" class="shrink-0 text-warning" />
                </template>

                <span
                    class="badge inline-flex shrink-0 items-center gap-1 border-0 font-semibold"
                    :class="[
                        badgeSizeClass,
                        actionBadgeStyle ? 'border-0' : 'badge-neutral',
                    ]"
                    :style="actionBadgeStyle"
                    data-cy="spell-sub-effect-action"
                >
                    <Icon
                        v-if="actionIcon"
                        :source="actionIcon"
                        alt=""
                        size="xs"
                        class="shrink-0"
                    />
                    <span>{{ actionLabel }}</span>
                </span>

                <span
                    v-if="showElement"
                    class="badge badge-outline inline-flex shrink-0 items-center gap-1 border font-medium"
                    :class="badgeSizeClass"
                    :style="elementBadgeStyle"
                    data-cy="spell-sub-effect-element"
                >
                    <Icon
                        :source="getElementIcon(elementNum)"
                        :alt="elementLabel"
                        size="xs"
                        class="shrink-0"
                    />
                    <span>{{ elementLabel }}</span>
                </span>

                <span class="shrink-0 text-base-content/55" aria-hidden="true">→</span>

                <!-- appliquer-etat -->
                <template v-if="action === 'appliquer-etat' || action === 's-appliquer-etat'">
                    <ConditionInline
                        :condition="conditionInlineProps.condition"
                        :name-fallback="conditionInlineProps.nameFallback"
                        class="min-w-0"
                    />
                    <span v-if="action === 's-appliquer-etat'" class="text-base-content/80">à soi-même</span>
                </template>

                <!-- autre -->
                <template v-else-if="action === 'autre'">
                    <SpellFormulaPartsInline
                        v-if="plainFormulaParts?.length"
                        :parts="plainFormulaParts"
                    />
                    <span v-else class="tabular-nums font-medium">{{ m.textFallback || "—" }}</span>
                </template>

                <!-- booster / retirer / voler -->
                <template
                    v-else-if="
                        action === 'booster' ||
                        action === 'retirer' ||
                        action === 'voler-caracteristiques'
                    "
                >
                    <SpellFormulaPartsInline
                        v-if="plainFormulaParts?.length"
                        :parts="plainFormulaParts"
                    />
                    <span class="text-base-content/80">en</span>
                    <SpellCharacteristicHoverLink
                        v-if="charBlock"
                        :characteristic-key="m.characteristic || ''"
                        :label="charBlock.label"
                        :icon="charBlock.icon"
                        :color="charBlock.color"
                    />
                </template>

                <!-- déplacer -->
                <template v-else-if="action === 'déplacer'">
                    <span v-if="moveValue" class="tabular-nums font-medium">{{ moveValue }}</span>
                    <span v-if="movementKindLabel" class="text-base-content/80">{{ movementKindLabel }}</span>
                </template>

                <!-- frapper -->
                <template v-else-if="action === 'frapper'">
                    <SpellFormulaPartsInline
                        v-if="damageAbbrev"
                        :parts="damageAbbrev.visible"
                        :full-parts="damageAbbrev.full"
                    />
                    <template v-if="isFrapperWithLifeSteal">
                        <span class="text-base-content/70">—</span>
                        <span class="font-semibold">vol de vie</span>
                        <SpellFormulaPartsInline :parts="lifeStealParts" />
                    </template>
                </template>

                <!-- soigner -->
                <template v-else-if="action === 'soigner'">
                    <SpellFormulaPartsInline
                        v-if="plainFormulaParts?.length"
                        :parts="plainFormulaParts"
                    />
                </template>

                <!-- invoquer -->
                <template v-else-if="action === 'invoquer'">
                    <SpellSummonMonsterMinimal
                        v-if="m.summonMonster && !isMinimalLayout"
                        :monster-brief="m.summonMonster"
                        class="min-w-0"
                    />
                    <SpellSummonMonsterInline
                        v-else-if="m.summonMonster"
                        :monster-brief="m.summonMonster"
                        class="min-w-0"
                    />
                </template>

                <!-- protéger -->
                <template v-else-if="action === 'protéger'">
                    <SpellFormulaPartsInline
                        v-if="plainFormulaParts?.length"
                        :parts="plainFormulaParts"
                    />
                    <span class="text-base-content/80">de</span>
                    <span
                        v-if="shieldBlock"
                        class="inline-flex items-center gap-1 font-medium"
                        :style="
                            shieldBlock.color
                                ? getCharacteristicColorStyle(shieldBlock.color)
                                : undefined
                        "
                    >
                        <Icon
                            :source="shieldBlock.icon"
                            :alt="shieldBlock.label"
                            size="xs"
                            class="shrink-0"
                        />
                        <span>{{ shieldBlock.label }}</span>
                    </span>
                </template>

                <!-- donner-pv-temporaires -->
                <template v-else-if="action === 'donner-pv-temporaires'">
                    <SpellFormulaPartsInline
                        v-if="plainFormulaParts?.length"
                        :parts="plainFormulaParts"
                    />
                    <span
                        class="inline-flex items-center gap-1 font-medium"
                        :style="
                            tempHpBlock.color
                                ? getCharacteristicColorStyle(tempHpBlock.color)
                                : undefined
                        "
                    >
                        <Icon
                            :source="tempHpBlock.icon"
                            :alt="tempHpBlock.label"
                            size="xs"
                            class="shrink-0"
                        />
                        <span>{{ tempHpBlock.label }}</span>
                    </span>
                </template>

                <!-- inconnu -->
                <template v-else-if="!isKnownAction">
                    <SpellFormulaPartsInline
                        v-if="plainFormulaParts?.length"
                        :parts="plainFormulaParts"
                    />
                    <span v-else class="font-medium">{{ m.textFallback || "—" }}</span>
                </template>
            </div>

            <!-- Critique : à droite s'il tient, sinon ligne suivante (mr-auto sur la formule) -->
            <div
                v-if="showCrit"
                class="inline-flex max-w-full shrink-0 flex-wrap items-center gap-x-1 text-warning"
                data-cy="spell-sub-effect-crit"
            >
                <Icon source="fa-solid fa-bolt" alt="Critique" size="xs" class="shrink-0" />
                <span class="font-semibold">Critique&nbsp;:</span>
                <SpellFormulaPartsInline
                    :parts="critAbbrev.visible"
                    :full-parts="critAbbrev.full"
                />
            </div>
        </div>

        <!-- Ligne 2 : paramètres (durée, portée en jeu) -->
        <div
            v-if="hasParamsLine"
            class="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-base-content/65"
            data-cy="spell-sub-effect-params"
        >
            <span v-if="durationLabel">{{ durationLabel }}</span>
            <span v-if="scopeTag" class="font-mono uppercase tracking-tight">{{ scopeTag }}</span>
        </div>
    </div>
</template>
