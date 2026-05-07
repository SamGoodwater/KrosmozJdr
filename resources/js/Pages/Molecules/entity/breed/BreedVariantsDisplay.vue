<script setup>
/**
 * Affichage des variantes de sorts (choix exclusifs) et des sorts toujours disponibles.
 *
 * @props {Object} breed - Breed ou payload (_data, spells, spell_slots)
 * @props {'text'|'compact'|'large'} density - text = Minimal/Line, compact = modal, large = fiche
 */
import { computed } from "vue";
import SpellViewText from "@/Pages/Molecules/entity/spell/SpellViewText.vue";
import SpellViewMinimal from "@/Pages/Molecules/entity/spell/SpellViewMinimal.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import { Spell } from "@/Models/Entity/Spell";
import { splitBreedSpellSlotGroups } from "@/Utils/entity/breedSpellSlots";

const props = defineProps({
    breed: {
        type: Object,
        required: true,
    },
    density: {
        type: String,
        default: "large",
        validator: (v) => ["text", "compact", "large"].includes(v),
    },
    characteristicRuntime: {
        type: Object,
        default: null,
    },
    /** Afficher le paragraphe sur le temple et le temps de changement de variante */
    showTempleNote: {
        type: Boolean,
        default: true,
    },
    lightweight: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["open-spell"]);

const rawBreed = computed(() => props.breed?._data ?? props.breed);

const split = computed(() => splitBreedSpellSlotGroups(rawBreed.value));

const variantGroupsList = computed(() => split.value.variantGroups);
const alwaysList = computed(() => split.value.alwaysAvailableGroups);

const hasVariants = computed(() => variantGroupsList.value.some((g) => (g.spells || []).length > 0));
const hasAlways = computed(() => alwaysList.value.some((g) => (g.spells || []).length > 0));
const hasAny = computed(() => hasVariants.value || hasAlways.value);
const ICON_LIMIT = 12;

/**
 * @param {object} raw
 * @returns {Spell}
 */
const asSpellModel = (raw) => (raw instanceof Spell ? raw : new Spell(raw));

const allVariantSpells = computed(() => variantGroupsList.value.flatMap((g) => g.spells || []));
const allAlwaysSpells = computed(() => alwaysList.value.flatMap((g) => g.spells || []));

const variantPreview = computed(() => allVariantSpells.value.slice(0, ICON_LIMIT).map((s) => asSpellModel(s)));
const alwaysPreview = computed(() => allAlwaysSpells.value.slice(0, ICON_LIMIT).map((s) => asSpellModel(s)));
const hiddenVariantCount = computed(() => Math.max(0, allVariantSpells.value.length - variantPreview.value.length));
const hiddenAlwaysCount = computed(() => Math.max(0, allAlwaysSpells.value.length - alwaysPreview.value.length));

const spellName = (spell) => spell?.name || spell?._data?.name || "Sort";
const spellImage = (spell) => spell?.image || spell?._data?.image || "";
const openSpell = (spell) => emit("open-spell", spell);

/**
 * @param {{ character_level: number, slot_index: number }} g
 */
const variantTitle = (g) => {
    const L = Number(g.character_level);
    const s = Number(g.slot_index);
    if (L === 1) {
        return `Niveau 1 · Choix ${s}`;
    }
    return `Niveau ${L} · Variante ${s}`;
};
</script>

<template>
    <div v-if="hasAny" class="breed-variants-display space-y-4" :data-density="density">
        <template v-if="density === 'large'">
            <div v-if="hasVariants" class="rounded-box border border-base-300 bg-base-100/40 p-4 space-y-3">
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Variantes de sorts</h3>
                    <p v-if="showTempleNote" class="text-xs text-primary-400/90 mt-1">
                        À chaque variante, un seul sort est retenu en jeu. Changer une variante se fait dans le temple de
                        la classe : compter 1 h par niveau du sort pour modifier un seul choix de variante.
                    </p>
                </div>
                <div
                    v-for="(group, gIdx) in variantGroupsList"
                    :key="`vg-${group.character_level}-${group.slot_index}-${gIdx}`"
                    class="space-y-2 border-l-2 border-primary-500/50 pl-3"
                >
                    <div class="text-xs font-semibold text-primary-200">
                        {{ variantTitle(group) }}
                        <span class="font-normal text-primary-400/80"> — choisir 1 parmi {{ group.spells?.length || 0 }}</span>
                    </div>
                    <ul class="flex flex-wrap items-start gap-x-2 gap-y-2">
                        <template v-for="(s, si) in group.spells || []" :key="`sg-${s.id}`">
                            <li class="min-w-0 max-w-full">
                                <SpellViewMinimal
                                    :spell="asSpellModel(s)"
                                    :display-mode="'extended'"
                                    :characteristic-runtime="characteristicRuntime"
                                    :show-actions="false"
                                />
                            </li>
                            <li
                                v-if="si < (group.spells || []).length - 1"
                                class="text-xs text-base-content/45 select-none self-center px-0.5"
                                aria-hidden="true"
                            >
                                ou
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

            <div v-if="hasAlways" class="rounded-box border border-base-300 bg-base-100/40 p-4 space-y-3">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Sorts toujours disponibles</h3>
                <p class="text-xs text-primary-400/90">
                    Sorts utilisables sans avoir à faire un choix de variante au préalable.
                </p>
                <div
                    v-for="(group, gIdx) in alwaysList"
                    :key="`al-${gIdx}`"
                    class="flex flex-wrap gap-x-3 gap-y-2"
                >
                    <div v-for="s in group.spells || []" :key="`al-sp-${s.id}`" class="min-w-0 max-w-full">
                        <SpellViewMinimal
                            :spell="asSpellModel(s)"
                            :display-mode="'extended'"
                            :characteristic-runtime="characteristicRuntime"
                            :show-actions="false"
                        />
                    </div>
                </div>
            </div>
        </template>

        <template v-else-if="density === 'compact'">
            <div v-if="hasVariants" class="rounded-box border border-base-300/80 bg-base-200/30 p-2 space-y-2">
                <div class="text-[10px] font-semibold uppercase tracking-wide text-base-content/50">Variantes de sorts</div>
                <div class="grid gap-2 sm:grid-cols-2">
                    <div
                        v-for="(group, gIdx) in variantGroupsList"
                        :key="`vc-${group.character_level}-${group.slot_index}-${gIdx}`"
                        class="rounded-md bg-base-100/60 border border-base-300/50 p-2 min-w-0"
                    >
                        <div class="text-[10px] font-semibold text-primary-300 mb-1.5 truncate">
                            {{ variantTitle(group) }}
                        </div>
                        <ul class="space-y-2">
                            <li v-for="s in group.spells || []" :key="`vc-sp-${s.id}`" class="min-w-0">
                                <SpellViewMinimal
                                    :spell="asSpellModel(s)"
                                    :display-mode="'extended'"
                                    :characteristic-runtime="characteristicRuntime"
                                    :show-actions="false"
                                />
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div v-if="hasAlways" class="rounded-box border border-base-300/80 bg-base-200/30 p-2 space-y-2">
                <div class="text-[10px] font-semibold uppercase tracking-wide text-base-content/50">
                    Sorts toujours disponibles
                </div>
                <div class="flex flex-wrap gap-2">
                    <div v-for="group in alwaysList" :key="`ca-${group.character_level}-${group.slot_index}`">
                        <div v-for="s in group.spells || []" :key="`ca-sp-${s.id}`" class="min-w-0">
                            <SpellViewMinimal
                                :spell="asSpellModel(s)"
                                :display-mode="'extended'"
                                :characteristic-runtime="characteristicRuntime"
                                :show-actions="false"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template v-else>
            <!-- density === text : Minimal / Line -->
            <div v-if="hasVariants" class="space-y-1 text-[11px] leading-snug text-base-content/85">
                <div class="text-[10px] font-semibold uppercase tracking-wide text-base-content/50">Variantes</div>
                <div v-if="lightweight" class="flex flex-wrap items-center gap-1.5">
                    <Tooltip v-for="s in variantPreview" :key="`vt-i-${s.id}`" :content="spellName(s)" placement="top">
                        <button
                            type="button"
                            class="btn btn-ghost btn-xs btn-square"
                            :aria-label="`Ouvrir ${spellName(s)}`"
                            @click.stop="openSpell(s)"
                        >
                            <Image
                                v-if="spellImage(s)"
                                :source="spellImage(s)"
                                :alt="spellName(s)"
                                fit="contain"
                                class="h-4 w-4"
                            />
                            <Icon v-else source="fa-solid fa-wand-magic-sparkles" size="xs" />
                        </button>
                    </Tooltip>
                    <span v-if="hiddenVariantCount > 0" class="text-[10px] text-base-content/60">+{{ hiddenVariantCount }}</span>
                </div>
                <div
                    v-else
                    v-for="(group, gIdx) in variantGroupsList"
                    :key="`vt-${group.character_level}-${group.slot_index}-${gIdx}`"
                    class="flex flex-wrap gap-x-1 gap-y-0.5"
                >
                    <span class="text-base-content/55 shrink-0 font-medium" :title="variantTitle(group)">
                        {{ group.character_level }}/{{ group.slot_index }}
                    </span>
                    <span class="text-base-content/40">·</span>
                    <span class="min-w-0 flex flex-wrap items-center gap-x-1">
                        <template v-for="(s, si) in group.spells || []" :key="`vt-sp-${s.id}`">
                            <span class="spell-text-embed inline-flex min-w-0 max-w-full">
                                <SpellViewText :spell="asSpellModel(s)" />
                            </span>
                            <span v-if="si < (group.spells || []).length - 1" class="text-base-content/35">,</span>
                        </template>
                    </span>
                </div>
            </div>
            <div v-if="hasAlways" class="space-y-1 text-[11px] leading-snug text-base-content/85">
                <div class="text-[10px] font-semibold uppercase tracking-wide text-base-content/50">
                    Sorts toujours dispo.
                </div>
                <div v-if="lightweight" class="flex flex-wrap items-center gap-1.5">
                    <Tooltip v-for="s in alwaysPreview" :key="`ta-i-${s.id}`" :content="spellName(s)" placement="top">
                        <button
                            type="button"
                            class="btn btn-ghost btn-xs btn-square"
                            :aria-label="`Ouvrir ${spellName(s)}`"
                            @click.stop="openSpell(s)"
                        >
                            <Image
                                v-if="spellImage(s)"
                                :source="spellImage(s)"
                                :alt="spellName(s)"
                                fit="contain"
                                class="h-4 w-4"
                            />
                            <Icon v-else source="fa-solid fa-wand-magic-sparkles" size="xs" />
                        </button>
                    </Tooltip>
                    <span v-if="hiddenAlwaysCount > 0" class="text-[10px] text-base-content/60">+{{ hiddenAlwaysCount }}</span>
                </div>
                <div v-else class="flex flex-wrap gap-x-1 gap-y-0.5">
                    <template v-for="group in alwaysList" :key="`ta-${group.character_level}-${group.slot_index}`">
                        <template v-for="s in group.spells || []" :key="`ta-sp-${s.id}`">
                            <span class="spell-text-embed inline-flex min-w-0 max-w-full">
                                <SpellViewText :spell="asSpellModel(s)" />
                            </span>
                        </template>
                    </template>
                </div>
            </div>
        </template>
    </div>
</template>
