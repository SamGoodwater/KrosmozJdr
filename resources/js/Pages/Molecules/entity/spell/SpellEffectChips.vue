<script setup>
/**
 * SpellEffectChips — Affichage des effets d'un sort avec filtre par degré.
 *
 * @description
 * Affiche les chips d'effets. Par défaut, seul le degré 1 est visible.
 * Un bouton permet d'afficher les degrés supérieurs (D2, D3…).
 *
 * @example
 * <SpellEffectChips :items="items" />
 */
import { ref, computed } from "vue";
import CharacteristicInlineGroup from "@/Pages/Molecules/data-display/CharacteristicInlineGroup.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";

const props = defineProps({
    /** Chips avec { icon, color, value, tooltip, degree } */
    items: {
        type: Array,
        default: () => [],
    },
});

const showAllDegrees = ref(false);

/** Degrés présents dans les items (ex: [1, 2, 3]) */
const degreesPresent = computed(() => {
    const degrees = new Set();
    (props.items || []).forEach((it) => {
        const d = it?.degree;
        if (d != null && Number.isFinite(Number(d))) {
            degrees.add(Number(d));
        }
    });
    return Array.from(degrees).sort((a, b) => a - b);
});

/** Y a-t-il des degrés > 1 à afficher ? */
const hasHigherDegrees = computed(() =>
    degreesPresent.value.some((d) => d > 1),
);

/** Libellé du bouton : "D2, D3" ou "D2" */
const higherDegreesLabel = computed(() => {
    const higher = degreesPresent.value.filter((d) => d > 1);
    if (higher.length === 0) return "";
    return higher.map((d) => `D${d}`).join(", ");
});

/** Items filtrés selon showAllDegrees (par défaut : degré 1 ou sans degré) */
const visibleItems = computed(() => {
    const list = props.items || [];
    if (showAllDegrees.value) return list;
    return list.filter((it) => {
        const d = it?.degree;
        if (d == null) return true;
        return Number(d) <= 1;
    });
});

const toggleDegrees = () => {
    showAllDegrees.value = !showAllDegrees.value;
};
</script>

<template>
    <span class="inline-flex flex-wrap items-center gap-x-2 gap-y-0.5">
        <CharacteristicInlineGroup :items="visibleItems" />
        <Btn
            v-if="hasHigherDegrees"
            size="xs"
            variant="ghost"
            class="shrink-0 text-xs opacity-70 hover:opacity-100"
            :title="showAllDegrees ? 'Masquer les degrés supérieurs' : `Afficher ${higherDegreesLabel}`"
            @click="toggleDegrees"
        >
            {{ showAllDegrees ? "− " + higherDegreesLabel : "+ " + higherDegreesLabel }}
        </Btn>
    </span>
</template>
