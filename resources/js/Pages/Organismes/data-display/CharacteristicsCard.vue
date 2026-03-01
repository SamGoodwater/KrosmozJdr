<script setup>
/**
 * CharacteristicsCard — Organisme : carte d'affichage des caractéristiques d'une entité.
 *
 * @description
 * Conteneur qui affiche un ou plusieurs CharacteristicGroup. Gère le level effectif (state)
 * et un sélecteur de level quand celui-ci est variable (1d4, [5-8]). Fournit entity et
 * levelEffective aux groupes.
 *
 * @props {Object} [entity] - Entité (resource, item, monster, spell…) avec au minimum level
 * @props {Array<{title?: string, characteristics: Array}>} groups - Groupes de caractéristiques
 * @props {Array<number>} [levelOptions] - Options de level pour le sélecteur (si vide, déduit de entity.level via useCharacteristicLevel)
 */
import { computed, ref, watch } from "vue";
import CharacteristicGroup from "@/Pages/Molecules/data-display/CharacteristicGroup.vue";
import { useCharacteristicLevel } from "@/Utils/Entity/useCharacteristicLevel";

const props = defineProps({
    entity: { type: Object, default: null },
    groups: { type: Array, default: () => [] },
    levelOptions: { type: Array, default: () => [] },
    /** Réduit padding et espacements (ex. dans une cellule de tableau) */
    dense: { type: Boolean, default: false },
});

const emit = defineEmits(["update:levelEffective"]);

const cardClass = computed(() =>
    props.dense
        ? "characteristics-card rounded-lg border border-base-300 bg-base-100 p-2 shadow-sm"
        : "characteristics-card rounded-xl border border-base-300 bg-base-100 p-4 shadow-sm",
);
const spaceClass = computed(() => (props.dense ? "space-y-2" : "space-y-4"));

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

const groupsList = computed(() => Array.isArray(props.groups) ? props.groups : []);
</script>

<template>
    <div :class="cardClass">
        <!-- Sélecteur de level (si level variable) -->
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

        <!-- Groupes de caractéristiques -->
        <div :class="spaceClass">
            <CharacteristicGroup
                v-for="(group, i) in groupsList"
                :key="i"
                :title="group.title"
                :characteristics="group.characteristics"
                :level-effective="levelEffective"
                :compact="dense"
            />
        </div>
    </div>
</template>
