<script setup>
/**
 * Tableau récap de progression de classe (Vue) — sorts en puces élément, DV, passif, capacités.
 */
import { computed, markRaw } from "vue";
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import SpellViewMinimal from "@/Pages/Molecules/entity/spell/SpellViewMinimal.vue";
import CapabilityViewText from "@/Pages/Molecules/entity/capability/CapabilityViewText.vue";
import { Spell } from "@/Models/Entity/Spell";
import { Capability } from "@/Models/Entity/Capability";
import { partitionBreedCapabilities } from "@/Utils/entity/breedCapabilitiesPartition";
import { splitBreedSpellSlotGroups } from "@/Utils/entity/breedSpellSlots";

const CARAC_LEVELS = new Set([4, 10, 16]);

/** @type {import('vue').Raw<typeof SpellViewMinimal>} */
const spellMinimalComponent = markRaw(SpellViewMinimal);

const props = defineProps({
    breed: { type: Object, required: true },
    characteristicRuntime: { type: Object, default: null },
});

const rawBreed = computed(() => props.breed?._data ?? props.breed);

const hitDieLabel = computed(() => {
    const raw = rawBreed.value?.life_dice ?? props.breed?.lifeDice ?? "d8";
    const s = String(raw).trim();
    if (!s) return "d8";
    return s.startsWith("d") || s.startsWith("D") ? s.toLowerCase() : `d${s}`;
});

const passiveCapabilities = computed(() => {
    const { passiveCapabilities: list } = partitionBreedCapabilities(rawBreed.value?.capabilities);
    return list;
});

const capsByLevel = computed(() => {
    const map = new Map();
    const { otherCapabilities } = partitionBreedCapabilities(rawBreed.value?.capabilities);
    for (const raw of otherCapabilities) {
        const cap = raw instanceof Capability ? raw : new Capability(raw);
        const level = Number(cap.level) || 0;
        if (level < 1) continue;
        if (!map.has(level)) map.set(level, []);
        map.get(level).push(cap);
    }
    return map;
});

const spellsByLevel = computed(() => {
    const map = new Map();
    for (const g of splitBreedSpellSlotGroups(rawBreed.value).variantGroups) {
        const level = Number(g.character_level);
        if (!map.has(level)) map.set(level, []);
        for (const s of g.spells || []) {
            map.get(level).push(s instanceof Spell ? s : new Spell(s));
        }
    }
    return map;
});

const rows = computed(() => {
    const out = [];
    for (let level = 1; level <= 20; level += 1) {
        const spells = spellsByLevel.value.get(level) || [];
        const caps = capsByLevel.value.get(level) || [];
        const carac = CARAC_LEVELS.has(level);
        const isStart = level === 1;
        if (!isStart && spells.length === 0 && caps.length === 0 && !carac) {
            // niveaux « +1 DV » seulement
            out.push({
                level,
                spells: [],
                caps: [],
                carac: false,
                isStart: false,
                hitDieOnly: true,
            });
            continue;
        }
        out.push({
            level,
            spells,
            caps,
            carac,
            isStart,
            hitDieOnly: false,
        });
    }
    return out;
});
</script>

<template>
    <div class="breed-progression-recap space-y-3" aria-label="Récapitulatif de progression">
        <div class="overflow-x-auto rounded-box border border-base-300/70">
            <table class="table table-sm w-full">
                <thead>
                    <tr class="bg-base-200/40">
                        <th class="w-16 whitespace-nowrap">Niv.</th>
                        <th>Gains</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in rows" :key="row.level" class="align-top">
                        <th scope="row" class="whitespace-nowrap font-bold text-primary-100">
                            {{ row.level }}
                        </th>
                        <td class="min-w-0">
                            <div class="flex flex-col gap-2 py-1">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-base-content/80">
                                    <span v-if="row.isStart" class="badge badge-ghost badge-sm">
                                        PV max {{ hitDieLabel }} + Vita
                                    </span>
                                    <span v-else class="badge badge-ghost badge-sm">
                                        +1 {{ hitDieLabel }}
                                    </span>
                                    <template v-if="row.isStart">
                                        <span
                                            v-for="cap in passiveCapabilities"
                                            :key="`pass-${cap.id}`"
                                            class="inline-flex max-w-full"
                                        >
                                            <CapabilityViewText :capability="cap" />
                                        </span>
                                        <span class="text-base-content/70">10 pts carac</span>
                                    </template>
                                    <span v-if="row.carac" class="text-base-content/70">
                                        2 pts carac ou 1 trait
                                    </span>
                                </div>

                                <div
                                    v-if="row.spells.length"
                                    class="flex flex-wrap items-center gap-1.5"
                                >
                                    <template
                                        v-for="(spell, idx) in row.spells"
                                        :key="`sp-${row.level}-${spell.id}`"
                                    >
                                        <span
                                            v-if="idx > 0"
                                            class="text-base-content/35 text-xs"
                                            aria-hidden="true"
                                        >/</span>
                                        <EntityViewTextLink
                                            :entity="spell"
                                            entity-prop="spell"
                                            :minimal-component="spellMinimalComponent"
                                            fallback-icon="fa-solid fa-wand-magic-sparkles"
                                            ui-color="neutral"
                                            hydrate-type="spells"
                                            hover-width-class="w-[min(92vw,36rem)] max-w-[36rem]"
                                            :characteristic-runtime="characteristicRuntime"
                                        />
                                    </template>
                                </div>

                                <div
                                    v-if="row.caps.length"
                                    class="flex flex-wrap items-center gap-2"
                                >
                                    <span
                                        v-for="cap in row.caps"
                                        :key="`cap-${row.level}-${cap.id}`"
                                        class="inline-flex max-w-full"
                                    >
                                        <CapabilityViewText :capability="cap" />
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="text-xs text-base-content/60">
            Détail niveau par niveau ci-dessous (cartes de sorts et capacités).
        </p>
    </div>
</template>
