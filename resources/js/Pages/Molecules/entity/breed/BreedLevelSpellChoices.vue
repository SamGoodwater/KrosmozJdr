<script setup>
/**
 * Variantes de sorts pour un niveau de classe — cartes SpellViewMinimal.
 *
 * @props {Object} breed - Breed / payload avec spell_slots
 * @props {number} characterLevel - Niveau de personnage (1, 2, 4…)
 */
import { computed, markRaw } from "vue";
import SpellViewMinimal from "@/Pages/Molecules/entity/spell/SpellViewMinimal.vue";
import { Spell } from "@/Models/Entity/Spell";
import { splitBreedSpellSlotGroups } from "@/Utils/entity/breedSpellSlots";

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
    return splitBreedSpellSlotGroups(rawBreed.value).variantGroups.filter(
        (g) => Number(g.character_level) === level,
    );
});

const hasAny = computed(() => groups.value.some((g) => (g.spells || []).length > 0));

/**
 * @param {object} raw
 * @returns {Spell}
 */
const asSpell = (raw) => (raw instanceof Spell ? raw : new Spell(raw));
</script>

<template>
    <div
        v-if="hasAny"
        class="breed-level-spell-choices space-y-3"
        :data-character-level="characterLevel"
    >
        <div
            v-for="(group, gIdx) in groups"
            :key="`lvl-${characterLevel}-${group.slot_index}-${gIdx}`"
            class="space-y-2"
        >
            <p
                v-if="groups.length > 1"
                class="text-[10px] font-semibold uppercase tracking-wide text-base-content/55"
            >
                Choix {{ gIdx + 1 }}
            </p>
            <ul
                class="grid list-none grid-cols-[repeat(auto-fill,minmax(18rem,1fr))] gap-3 p-0 m-0 items-start"
            >
                <li
                    v-for="s in group.spells || []"
                    :key="`lvl-sp-${s.id}`"
                    class="min-w-0 max-w-[22rem]"
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
        <p class="text-sm text-base-content/80">
            Si tu regrettes une variante plus tard, rends-toi dans le temple de ton dieu.
            Tu pries pendant
            <strong>{{ characterLevel }} heure{{ characterLevel > 1 ? "s" : "" }}</strong>
            (autant d’heures que le niveau de cette variante) pour en changer une.
        </p>
    </div>
</template>
