<script setup>
/**
 * Variantes de sorts pour un niveau de classe — cartes SpellViewMinimal en hover (contractées).
 * Niveau 1 : sous-titres « Sort 1 (élémentaire) », « Sort 2 », « Sort 3 », groupes côte à côte.
 *
 * @props {Object} breed - Breed / payload avec spell_slots
 * @props {number} characterLevel - Niveau de personnage (1, 2, 4…)
 */
import { computed, markRaw } from "vue";
import SpellViewMinimal from "@/Pages/Molecules/entity/spell/SpellViewMinimal.vue";
import { Spell } from "@/Models/Entity/Spell";
import {
    breedLevel1SpellChoiceLabel,
    splitBreedSpellSlotGroups,
} from "@/Utils/entity/breedSpellSlots";

const props = defineProps({
    breed: { type: Object, required: true },
    characterLevel: { type: Number, required: true },
    characteristicRuntime: { type: Object, default: null },
});

/** @type {import('vue').Raw<typeof SpellViewMinimal>} */
const spellMinimalComponent = markRaw(SpellViewMinimal);

const rawBreed = computed(() => props.breed?._data ?? props.breed);

const groups = computed(() => {
    const level = Number(props.characterLevel);
    if (!Number.isFinite(level) || level < 1) return [];
    return splitBreedSpellSlotGroups(rawBreed.value)
        .variantGroups.filter((g) => Number(g.character_level) === level)
        .sort((a, b) => Number(a.slot_index) - Number(b.slot_index));
});

const hasAny = computed(() => groups.value.some((g) => (g.spells || []).length > 0));
const isLevel1 = computed(() => Number(props.characterLevel) === 1);
const showGroupLabels = computed(() => isLevel1.value || groups.value.length > 1);

/**
 * @param {{ slot_index?: number }} group
 * @param {number} gIdx
 * @returns {string}
 */
function groupLabel(group, gIdx) {
    if (isLevel1.value) {
        return breedLevel1SpellChoiceLabel(group.slot_index ?? gIdx + 1);
    }
    if (groups.value.length > 1) {
        return `Choix ${gIdx + 1}`;
    }
    return "";
}

/**
 * @param {object} raw
 * @returns {Spell}
 */
const asSpell = (raw) => (raw instanceof Spell ? raw : new Spell(raw));
</script>

<template>
    <div
        v-if="hasAny"
        class="breed-level-spell-choices space-y-2"
        :data-character-level="characterLevel"
    >
        <div
            class="flex flex-wrap items-start gap-x-4 gap-y-3"
            :class="{ 'flex-col sm:flex-row': showGroupLabels && groups.length > 1 }"
        >
            <div
                v-for="(group, gIdx) in groups"
                :key="`lvl-${characterLevel}-${group.slot_index}-${gIdx}`"
                class="min-w-0 space-y-1.5"
            >
                <p
                    v-if="groupLabel(group, gIdx)"
                    class="text-[10px] font-semibold uppercase tracking-wide text-base-content/55"
                >
                    {{ groupLabel(group, gIdx) }}
                </p>
                <ul class="flex list-none flex-wrap gap-2 p-0 m-0 items-start">
                    <li
                        v-for="s in group.spells || []"
                        :key="`lvl-sp-${s.id}`"
                        class="min-w-0 max-w-[18rem]"
                    >
                        <component
                            :is="spellMinimalComponent"
                            :spell="asSpell(s)"
                            display-mode="hover"
                            :characteristic-runtime="characteristicRuntime"
                            :show-actions="true"
                        />
                    </li>
                </ul>
            </div>
        </div>
        <p class="text-xs text-base-content/75">
            Si tu regrettes une variante plus tard, rends-toi dans le temple de ton dieu.
            Tu pries pendant
            <strong>{{ characterLevel }} heure{{ characterLevel > 1 ? "s" : "" }}</strong>
            (autant d’heures que le niveau de cette variante) pour en changer une.
        </p>
    </div>
</template>
