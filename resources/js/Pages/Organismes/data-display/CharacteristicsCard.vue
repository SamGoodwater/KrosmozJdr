<script setup>
/**
 * CharacteristicsCard — Organisme : carte d'affichage des caractéristiques d'une entité.
 *
 * @description
 * Conteneur qui affiche un ou plusieurs CharacteristicGroup. Gère le level effectif (state)
 * et un sélecteur de level quand celui-ci est variable (1d4, [5-8]).
 * Densités : `icon` (minimal), `labeled` (modal), `spacious` (page).
 *
 * @props {Object} [entity] - Entité avec au minimum level
 * @props {Array<{title?: string, characteristics: Array}>} groups
 * @props {Array<number>} [levelOptions]
 * @props {'icon'|'labeled'|'spacious'} [density]
 * @props {boolean} [dense] - Alias BC : true → density icon
 */
import { computed, ref, watch } from "vue";
import CharacteristicGroup from "@/Pages/Molecules/data-display/CharacteristicGroup.vue";
import { useCharacteristicLevel } from "@/Utils/Entity/useCharacteristicLevel";
import { CHARACTERISTIC_CARD_DENSITY } from "@/Utils/Entity/creatureCharacteristicGroups.manifest";

const props = defineProps({
    entity: { type: Object, default: null },
    groups: { type: Array, default: () => [] },
    levelOptions: { type: Array, default: () => [] },
    /** @deprecated Préférer `density`. true → icon */
    dense: { type: Boolean, default: false },
    density: {
        type: String,
        default: "",
        validator: (v) => v === "" || Object.values(CHARACTERISTIC_CARD_DENSITY).includes(v),
    },
    runtime: { type: Object, default: null },
});

const emit = defineEmits(["update:levelEffective"]);

const resolvedDensity = computed(() => {
    if (props.density && Object.values(CHARACTERISTIC_CARD_DENSITY).includes(props.density)) {
        return props.density;
    }
    return props.dense ? CHARACTERISTIC_CARD_DENSITY.icon : CHARACTERISTIC_CARD_DENSITY.labeled;
});

const cardClass = computed(() => {
    const d = resolvedDensity.value;
    if (d === CHARACTERISTIC_CARD_DENSITY.icon) {
        return "characteristics-card rounded-box border border-base-300 bg-base-100 p-2 shadow-sm";
    }
    if (d === CHARACTERISTIC_CARD_DENSITY.spacious) {
        return "characteristics-card rounded-box border border-base-300 bg-base-100 p-5 shadow-sm";
    }
    return "characteristics-card rounded-box border border-base-300 bg-base-100 p-4 shadow-sm";
});

const spaceClass = computed(() => {
    const d = resolvedDensity.value;
    if (d === CHARACTERISTIC_CARD_DENSITY.icon) return "space-y-2";
    if (d === CHARACTERISTIC_CARD_DENSITY.spacious) return "space-y-5";
    return "space-y-3";
});

const levelFromEntity = computed(() => props.entity?.level ?? null);
const parsedLevel = computed(() => useCharacteristicLevel(levelFromEntity.value));

const options = computed(() => {
    if (Array.isArray(props.levelOptions) && props.levelOptions.length > 0) {
        return props.levelOptions;
    }
    return parsedLevel.value.options;
});

const hasLevelSelector = computed(() => options.value.length > 1);

const levelEffective = ref(null);

function initLevelEffective() {
    if (options.value.length > 0) {
        const defaultVal = parsedLevel.value.defaultLevel ?? options.value[0];
        if (levelEffective.value === null || !options.value.includes(Number(levelEffective.value))) {
            levelEffective.value = defaultVal;
        }
    } else {
        levelEffective.value = null;
    }
}

watch([options, () => props.entity?.level], initLevelEffective, { immediate: true });

watch(levelEffective, (v) => {
    emit("update:levelEffective", v);
});

const groupsList = computed(() => (Array.isArray(props.groups) ? props.groups : []));
</script>

<template>
    <div :class="cardClass">
        <div v-if="hasLevelSelector" class="mb-2 flex items-center gap-2">
            <label class="text-xs font-medium opacity-90">Niveau</label>
            <select
                v-model="levelEffective"
                class="select select-bordered select-sm max-w-32"
            >
                <option
                    v-for="opt in options"
                    :key="opt"
                    :value="opt"
                >
                    {{ opt }}
                </option>
            </select>
        </div>

        <div :class="spaceClass">
            <CharacteristicGroup
                v-for="(group, i) in groupsList"
                :key="i"
                :title="group.title"
                :characteristics="group.characteristics"
                :level-effective="levelEffective"
                :density="resolvedDensity"
                :runtime="runtime"
            />
        </div>
    </div>
</template>
