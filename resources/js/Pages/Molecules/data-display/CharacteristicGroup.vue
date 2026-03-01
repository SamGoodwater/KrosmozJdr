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
import CharacteristicFormula from "@/Pages/Atoms/data-display/CharacteristicFormula.vue";
import CharacteristicBoolean from "@/Pages/Atoms/data-display/CharacteristicBoolean.vue";
import CharacteristicBadges from "@/Pages/Atoms/data-display/CharacteristicBadges.vue";

const props = defineProps({
    characteristics: { type: Array, default: () => [] },
    levelEffective: { type: [Number, String], default: null },
    title: { type: String, default: "" },
    /** Mode compact pour les atomes (icône + valeur, padding minimal) */
    compact: { type: Boolean, default: false },
});

const list = computed(() => Array.isArray(props.characteristics) ? props.characteristics : []);
</script>

<template>
    <div class="characteristic-group space-y-2">
        <h4 v-if="title" class="text-sm font-semibold opacity-90">
            {{ title }}
        </h4>
        <div class="flex flex-wrap gap-2">
            <template v-for="(item, i) in list" :key="item.def?.key ?? i">
                <CharacteristicFormula
                    v-if="item.type === 'formula'"
                    :def="item.def"
                    :value="item.value"
                    :formula-resolved="item.formulaResolved"
                    :formula-raw="item.formulaRaw"
                    :level-table="item.levelTable"
                    :unit="item.unit"
                    :compact="compact"
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
