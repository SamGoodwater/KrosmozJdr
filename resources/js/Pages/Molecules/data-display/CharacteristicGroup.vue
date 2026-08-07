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
 */
import { computed } from "vue";
import CharacteristicProperty from "@/Pages/Atoms/data-display/CharacteristicProperty.vue";
import CharacteristicBoolean from "@/Pages/Atoms/data-display/CharacteristicBoolean.vue";
import CharacteristicBadges from "@/Pages/Atoms/data-display/CharacteristicBadges.vue";
import {
    characteristicAtLevel,
    levelTableFromRuntime,
    mergeRuntimeIntoViewModel,
    viewModelFromFormulaGroupItem,
} from "@/Composables/entity/useCharacteristicViewModel";
import { shouldHideCharacteristicLine } from "@/Composables/entity/useCharacteristicDisplay";
import {
    CHARACTERISTIC_PROPERTY_BADGE,
    CHARACTERISTIC_PROPERTY_DENSITY,
    CHARACTERISTIC_PROPERTY_LAYOUT,
} from "@/Utils/Entity/Constants";

const props = defineProps({
    characteristics: { type: Array, default: () => [] },
    levelEffective: { type: [Number, String], default: null },
    title: { type: String, default: "" },
    /** Mode compact pour les atomes (icône + valeur, padding minimal) */
    compact: { type: Boolean, default: false },
    /** Payload runtime (ex. resolved-stats) — enrichit les tooltips / popovers par clé */
    runtime: { type: Object, default: null },
});

const list = computed(() => (Array.isArray(props.characteristics) ? props.characteristics : []));

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
    const table = levelTableFromRuntime(props.runtime, key);
    if (table.length > 0) {
        vm.levelTable = table;
    }
    vm.itemContributions = itemContributionsForKey(key);
    return vm;
}
</script>

<template>
    <div class="characteristic-group space-y-2">
        <h4 v-if="title" class="text-sm font-semibold text-base-content/90">
            {{ title }}
        </h4>
        <div class="flex flex-wrap gap-2">
            <template v-for="(item, i) in list" :key="item.def?.key ?? i">
                <CharacteristicProperty
                    v-if="item.type === 'formula' && !shouldHideCharacteristicLine(item.def, formulaViewModel(item).rawValue ?? item.value)"
                    :view-model="formulaViewModel(item)"
                    :density="compact ? CHARACTERISTIC_PROPERTY_DENSITY.iconOnly : CHARACTERISTIC_PROPERTY_DENSITY.full"
                    :layout="compact ? CHARACTERISTIC_PROPERTY_LAYOUT.inline : CHARACTERISTIC_PROPERTY_LAYOUT.card"
                    :badge="CHARACTERISTIC_PROPERTY_BADGE.none"
                    size="sm"
                    :prefer-decomposition-popover="true"
                />
                <CharacteristicBoolean
                    v-else-if="item.type === 'boolean'"
                    :def="item.def"
                    :value="item.value"
                    :compact="compact"
                />
                <CharacteristicBadges
                    v-else-if="item.type === 'badges'"
                    :def="item.def"
                    :items="item.items"
                    :value="item.value"
                    :compact="compact"
                />
            </template>
        </div>
    </div>
</template>
