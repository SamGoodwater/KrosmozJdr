<script setup>
/**
 * CharacteristicGroup — Molécule regroupant des atomes de caractéristiques (Formula, Boolean, Badges).
 *
 * @description
 * Affiche une liste d'atomes avec un placement cohérent (grille/flex). Chaque item doit avoir
 * type ('formula' | 'boolean' | 'badges') et les props correspondantes (def, value, etc.).
 * Quand `runtime.levels` est fourni, `levelEffective` sélectionne la ligne de valeurs affichée.
 *
 * @props {Array<Object>} characteristics - Liste d'items { type, def, value?, formulaResolved?, formulaRaw?, levelTable?, items? }
 * @props {number|null} [levelEffective] - Level effectif pour les formules
 * @props {string} [title] - Titre optionnel du groupe (ex. "Stats de combat")
 * @props {'icon'|'labeled'|'spacious'|boolean} [density] - Densité d’affichage (boolean compact BC)
 */
import { computed } from "vue";
import CharacteristicProperty from "@/Pages/Atoms/data-display/CharacteristicProperty.vue";
import CharacteristicBoolean from "@/Pages/Atoms/data-display/CharacteristicBoolean.vue";
import CharacteristicBadges from "@/Pages/Atoms/data-display/CharacteristicBadges.vue";
import AbilityScoreStack from "@/Pages/Molecules/data-display/AbilityScoreStack.vue";
import {
    characteristicAtLevel,
    levelTableFromRuntime,
    mergeRuntimeIntoViewModel,
    viewModelFromFormulaGroupItem,
} from "@/Composables/entity/useCharacteristicViewModel";
import { shouldHideCharacteristicLine } from "@/Composables/entity/useCharacteristicDisplay";
import { formatCreatureSkillDisplay } from "@/Utils/Entity/buildCreatureCompetenceGroups";
import {
    CHARACTERISTIC_PROPERTY_BADGE,
    CHARACTERISTIC_PROPERTY_DENSITY,
    CHARACTERISTIC_PROPERTY_LAYOUT,
} from "@/Utils/Entity/Constants";
import { CHARACTERISTIC_CARD_DENSITY } from "@/Utils/Entity/creatureCharacteristicGroups.manifest";

const props = defineProps({
    characteristics: { type: Array, default: () => [] },
    levelEffective: { type: [Number, String], default: null },
    title: { type: String, default: "" },
    /** Kind de groupe (`abilityStack`, `db`, …) — piloté par le manifeste. */
    kind: { type: String, default: "" },
    /** @deprecated Préférer density */
    compact: { type: Boolean, default: false },
    density: {
        type: String,
        default: "",
        validator: (v) => v === "" || Object.values(CHARACTERISTIC_CARD_DENSITY).includes(v),
    },
    runtime: { type: Object, default: null },
});

const isAbilityStack = computed(() => props.kind === "abilityStack");

/** Densité icon (Minimal / Line) : tooltip au survol. Densités plus riches : popover de décomposition au clic. */
const useDecompositionPopover = computed(
    () => resolvedDensity.value !== CHARACTERISTIC_CARD_DENSITY.icon,
);

const list = computed(() => (Array.isArray(props.characteristics) ? props.characteristics : []));

const resolvedDensity = computed(() => {
    if (props.density && Object.values(CHARACTERISTIC_CARD_DENSITY).includes(props.density)) {
        return props.density;
    }
    return props.compact ? CHARACTERISTIC_CARD_DENSITY.icon : CHARACTERISTIC_CARD_DENSITY.labeled;
});

const titleClass = computed(() => {
    const d = resolvedDensity.value;
    if (d === CHARACTERISTIC_CARD_DENSITY.spacious) {
        return "text-base font-semibold text-base-content";
    }
    if (d === CHARACTERISTIC_CARD_DENSITY.icon) {
        return "text-[0.65rem] font-semibold uppercase tracking-wide text-base-content/80";
    }
    return "text-sm font-semibold text-base-content/90";
});

const wrapClass = computed(() =>
    resolvedDensity.value === CHARACTERISTIC_CARD_DENSITY.spacious
        ? "flex flex-wrap gap-3"
        : "flex flex-wrap gap-2",
);

const propertyDensity = computed(() => {
    const d = resolvedDensity.value;
    if (d === CHARACTERISTIC_CARD_DENSITY.icon) return CHARACTERISTIC_PROPERTY_DENSITY.iconOnly;
    if (d === CHARACTERISTIC_CARD_DENSITY.spacious) return CHARACTERISTIC_PROPERTY_DENSITY.full;
    return CHARACTERISTIC_PROPERTY_DENSITY.short;
});

const propertyLayout = computed(() =>
    resolvedDensity.value === CHARACTERISTIC_CARD_DENSITY.icon
        ? CHARACTERISTIC_PROPERTY_LAYOUT.inline
        : CHARACTERISTIC_PROPERTY_LAYOUT.card,
);

function itemContributionsForKey(key) {
    const lines = props.runtime?.items?.lines;
    if (!Array.isArray(lines) || !key) return [];
    const short = String(key).replace(/_creature$/, "");
    const out = [];
    for (const line of lines) {
        const bonuses = line?.bonuses && typeof line.bonuses === "object" ? line.bonuses : {};
        let amount = bonuses[short];
        if (amount == null && bonuses[key] != null) amount = bonuses[key];
        if (amount == null || Number(amount) === 0) continue;
        out.push({
            item_id: line.item_id,
            name: line.name,
            quantity: line.quantity,
            amount,
        });
    }
    return out;
}

function formulaViewModel(item) {
    let vm = viewModelFromFormulaGroupItem(item);
    const key = vm.key;
    const rc = characteristicAtLevel(props.runtime, props.levelEffective, key);
    vm = mergeRuntimeIntoViewModel(vm, rc);
    // Compétences : conserver « Nom +N (M|E) » après fusion runtime (qui sinon n’affiche que le total).
    if (item?.lockSkillDisplay && item.skillName) {
        const total = vm.total ?? vm.rawValue;
        const n = Number(total);
        const signed = Number.isFinite(n)
            ? formatCreatureSkillDisplay(n, item.skillTag || "")
            : String(item.value || vm.displayValue || "");
        vm.displayValue = Number.isFinite(n) ? `${item.skillName} ${signed}` : String(item.value || "");
        vm.shortName = item.skillName;
        vm.name = item.skillName;
    }
    const table = levelTableFromRuntime(props.runtime, key);
    if (table.length > 0) {
        vm.levelTable = table;
    }
    vm.itemContributions = itemContributionsForKey(key);
    return vm;
}
</script>

<template>
    <div
        class="characteristic-group"
        :class="resolvedDensity === CHARACTERISTIC_CARD_DENSITY.icon ? 'space-y-0.5' : 'space-y-2'"
    >
        <h4 v-if="title" :class="titleClass">
            {{ title }}
        </h4>
        <AbilityScoreStack
            v-if="isAbilityStack"
            :columns="list"
            :level-effective="levelEffective"
            :density="resolvedDensity"
            :runtime="runtime"
        />
        <div v-else :class="wrapClass">
            <template v-for="(item, i) in list" :key="item.def?.key ?? i">
                <CharacteristicProperty
                    v-if="item.type === 'formula' && !shouldHideCharacteristicLine(item.def, formulaViewModel(item).rawValue ?? item.value)"
                    :view-model="formulaViewModel(item)"
                    :density="propertyDensity"
                    :layout="propertyLayout"
                    :badge="CHARACTERISTIC_PROPERTY_BADGE.none"
                    size="sm"
                    :prefer-decomposition-popover="useDecompositionPopover"
                />
                <CharacteristicBoolean
                    v-else-if="item.type === 'boolean'"
                    :def="item.def"
                    :value="item.value"
                    :compact="resolvedDensity === CHARACTERISTIC_CARD_DENSITY.icon"
                />
                <CharacteristicBadges
                    v-else-if="item.type === 'badges'"
                    :def="item.def"
                    :items="item.items"
                    :value="item.value"
                    :compact="resolvedDensity === CHARACTERISTIC_CARD_DENSITY.icon"
                />
            </template>
        </div>
    </div>
</template>
