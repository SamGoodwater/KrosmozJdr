<script setup>
/**
 * Progression classe : une carte par niveau (PV, caracs, sorts minimal, capacité).
 *
 * @props {Object} breed
 * @props {Object|null} characteristicRuntime
 */
import { computed } from "vue";
import CapabilityViewMinimal from "@/Pages/Molecules/entity/capability/CapabilityViewMinimal.vue";
import BreedLevelSpellChoices from "@/Pages/Molecules/entity/breed/BreedLevelSpellChoices.vue";
import BreedProgressionRecap from "@/Pages/Molecules/entity/breed/BreedProgressionRecap.vue";
import { partitionBreedCapabilities } from "@/Utils/entity/breedCapabilitiesPartition";
import { splitBreedSpellSlotGroups } from "@/Utils/entity/breedSpellSlots";
import { Capability } from "@/Models/Entity/Capability";

const CARAC_OR_TRAIT_LEVELS = new Set([4, 10, 16]);

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

const spellLevels = computed(() => {
    const set = new Set();
    for (const g of splitBreedSpellSlotGroups(rawBreed.value).variantGroups) {
        if ((g.spells || []).length > 0) set.add(Number(g.character_level));
    }
    return set;
});

const capabilitiesByLevel = computed(() => {
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

const levels = computed(() => {
    const out = [];
    for (let level = 1; level <= 20; level += 1) {
        const spells = spellLevels.value.has(level);
        const caps = capabilitiesByLevel.value.get(level) || [];
        const caracOrTrait = CARAC_OR_TRAIT_LEVELS.has(level);
        const creationCarac = level === 1;
        out.push({
            level,
            spells,
            caps,
            caracOrTrait,
            creationCarac,
            /** Toujours afficher : chaque niveau donne au moins les PV. */
            show: true,
        });
    }
    return out;
});
</script>

<template>
    <div class="breed-progression-read space-y-6" aria-label="Progression de classe">
        <header class="space-y-1">
            <h2 class="text-lg font-semibold text-primary-100">Progression</h2>
            <p class="text-sm text-base-content/70">
                Niveau après niveau : points de vie, sorts, caractéristiques et capacités de classe.
                Les gains de spécialisation sont sur sa fiche (paliers 1, 3, 6, 9, 12, 15, 20).
            </p>
        </header>

        <BreedProgressionRecap
            :breed="breed"
            :characteristic-runtime="characteristicRuntime"
        />

        <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">
            Détail par niveau
        </h3>

        <article
            v-for="row in levels"
            :id="`breed-level-${row.level}`"
            :key="row.level"
            class="rounded-box border border-base-300 bg-base-100/40 p-4 space-y-4"
        >
            <h3 class="text-base font-bold tracking-wide text-primary-100">
                Niveau {{ row.level }}
            </h3>

            <section class="space-y-2" aria-label="Points de vie">
                <h4 class="text-xs font-semibold uppercase tracking-wide text-primary-300">
                    Points de vie
                </h4>
                <p v-if="row.level === 1" class="text-sm text-base-content/85">
                    Tu poses tes PV de départ :
                    <strong>maximum du {{ hitDieLabel }}</strong>
                    + ton modificateur de Vitalité.
                </p>
                <template v-else>
                    <p class="text-sm text-base-content/85">
                        PV actuels + <strong>1{{ hitDieLabel }}</strong> (méthode de la table)
                        + ton modificateur de Vitalité.
                    </p>
                    <p class="text-sm text-base-content/75">
                        Si ton modificateur de Vitalité vient d’augmenter, on fait comme s’il avait
                        toujours eu cette valeur : tu ajoutes aussi
                        <strong>niveau × (nouveau mod. − ancien mod.)</strong> à tes PV.
                    </p>
                </template>
            </section>

            <section
                v-if="row.creationCarac"
                class="space-y-2"
                aria-label="Caractéristiques de création"
            >
                <h4 class="text-xs font-semibold uppercase tracking-wide text-primary-300">
                    Caractéristiques
                </h4>
                <p class="text-sm text-base-content/85">
                    Tu répartis <strong>10 points</strong> (toutes les caracs à 8). Option : une carac
                    à 6 pour récupérer 2 points.
                </p>
            </section>

            <section
                v-if="row.caracOrTrait"
                class="space-y-2"
                aria-label="Caractéristiques ou trait"
            >
                <h4 class="text-xs font-semibold uppercase tracking-wide text-primary-300">
                    Caractéristiques ou trait
                </h4>
                <p class="text-sm text-base-content/85">Tu choisis <strong>une</strong> option :</p>
                <ul class="list-disc pl-5 text-sm text-base-content/85 space-y-1">
                    <li>
                        <strong>2 points</strong> : +2 sur une caractéristique, ou +1 sur deux
                        caractéristiques différentes (Intelligence, Chance, Force, Agilité, Sagesse,
                        Vitalité) ;
                    </li>
                    <li>
                        <strong>1 trait</strong> parmi ceux marqués pour les personnages (niveau min.
                        respecté).
                    </li>
                </ul>
            </section>

            <section v-if="row.spells" class="space-y-2" aria-label="Sorts">
                <h4 class="text-xs font-semibold uppercase tracking-wide text-primary-300">
                    Sorts
                </h4>
                <BreedLevelSpellChoices
                    :breed="breed"
                    :character-level="row.level"
                    :characteristic-runtime="characteristicRuntime"
                />
            </section>

            <section
                v-if="row.caps.length"
                class="space-y-2"
                aria-label="Capacité de classe"
            >
                <h4 class="text-xs font-semibold uppercase tracking-wide text-primary-300">
                    Capacité de classe
                </h4>
                <ul
                    class="grid list-none grid-cols-[repeat(auto-fill,minmax(18rem,1fr))] gap-3 p-0 m-0 items-start"
                >
                    <li
                        v-for="cap in row.caps"
                        :key="`cap-${row.level}-${cap.id}`"
                        class="min-w-0 max-w-[22rem]"
                    >
                        <CapabilityViewMinimal
                            :capability="cap"
                            display-mode="hover"
                            :characteristic-runtime="characteristicRuntime"
                            :show-actions="true"
                        />
                    </li>
                </ul>
            </section>
        </article>
    </div>
</template>
