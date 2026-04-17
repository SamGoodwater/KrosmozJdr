<script setup>
/**
 * CharacteristicGroup — Molécule regroupant des atomes de caractéristiques (Formula, Boolean, Badges).
 *
 * @description
 * Affiche une liste d'atomes avec un placement cohérent (grille/flex). Chaque item doit avoir
 * type ('formula' | 'boolean' | 'badges') et les props correspondantes (def, value, etc.).
 *
 * @props {Array<Object>} characteristics - Liste d'items { type, def, value?, formulaResolved?, formulaRaw?, levelTable?, items? }
 * @props {number|null} [levelEffective] - Level effectif pour les formules (optionnel)
 * @props {string} [title] - Titre optionnel du groupe (ex. "Stats de combat")
 */
import { computed } from "vue";
import CharacteristicProperty from "@/Pages/Atoms/data-display/CharacteristicProperty.vue";
import CharacteristicBoolean from "@/Pages/Atoms/data-display/CharacteristicBoolean.vue";
import CharacteristicBadges from "@/Pages/Atoms/data-display/CharacteristicBadges.vue";
import {
    mergeRuntimeIntoViewModel,
    viewModelFromFormulaGroupItem,
} from "@/Composables/entity/useCharacteristicViewModel";
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
    /** Payload runtime (ex. resolved-stats) — enrichit les tooltips par clé caractéristique */
    runtime: { type: Object, default: null },
});

const list = computed(() => (Array.isArray(props.characteristics) ? props.characteristics : []));

const computedMap = computed(() => {
    const r = props.runtime;
    if (!r || typeof r !== "object") return {};
    return r.computed && typeof r.computed === "object" ? r.computed : {};
});

function formulaViewModel(item) {
    let vm = viewModelFromFormulaGroupItem(item);
    const rc = computedMap.value[vm.key];
    vm = mergeRuntimeIntoViewModel(vm, rc);
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
                    v-if="item.type === 'formula'"
                    :view-model="formulaViewModel(item)"
                    :density="compact ? CHARACTERISTIC_PROPERTY_DENSITY.iconOnly : CHARACTERISTIC_PROPERTY_DENSITY.full"
                    :layout="compact ? CHARACTERISTIC_PROPERTY_LAYOUT.inline : CHARACTERISTIC_PROPERTY_LAYOUT.card"
                    :badge="CHARACTERISTIC_PROPERTY_BADGE.none"
                    size="sm"
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
