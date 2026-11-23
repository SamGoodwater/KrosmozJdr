<script setup>
/**
 * Corps Vue d’un niveau de classe (PV, caracs, sorts hover, capacité).
 * Affiché dans le slot `#read` de {@link SectionRenderer} (header CMS conservé).
 */
import CapabilityViewMinimal from "@/Pages/Molecules/entity/capability/CapabilityViewMinimal.vue";
import BreedLevelSpellChoices from "@/Pages/Molecules/entity/breed/BreedLevelSpellChoices.vue";

defineProps({
    breed: { type: Object, required: true },
    level: { type: Number, required: true },
    hitDieLabel: { type: String, default: "d8" },
    spells: { type: Boolean, default: false },
    caps: { type: Array, default: () => [] },
    caracOrTrait: { type: Boolean, default: false },
    creationCarac: { type: Boolean, default: false },
    characteristicRuntime: { type: Object, default: null },
});
</script>

<template>
    <div class="breed-level-body space-y-2">
        <section class="space-y-1" aria-label="Points de vie">
            <h4 class="text-[10px] font-semibold uppercase tracking-wide text-primary-300 m-0">
                Points de vie
            </h4>
            <p v-if="level === 1" class="text-xs text-base-content/85 m-0">
                Tu poses tes PV de départ :
                <strong>maximum du {{ hitDieLabel }}</strong>
                + ton modificateur de Vitalité.
            </p>
            <template v-else>
                <p class="text-xs text-base-content/85 m-0">
                    PV actuels + <strong>1{{ hitDieLabel }}</strong> (méthode de la table)
                    + ton modificateur de Vitalité.
                </p>
                <p class="text-xs text-base-content/70 m-0">
                    Si ton modificateur de Vitalité vient d’augmenter, on fait comme s’il avait
                    toujours eu cette valeur : tu ajoutes aussi
                    <strong>niveau × (nouveau mod. − ancien mod.)</strong> à tes PV.
                </p>
            </template>
        </section>

        <section
            v-if="creationCarac"
            class="space-y-1"
            aria-label="Caractéristiques de création"
        >
            <h4 class="text-[10px] font-semibold uppercase tracking-wide text-primary-300 m-0">
                Caractéristiques
            </h4>
            <p class="text-xs text-base-content/85 m-0">
                Tu répartis <strong>10 points</strong> (toutes les caracs à 8). Option : une carac
                à 6 pour récupérer 2 points.
            </p>
        </section>

        <section
            v-if="caracOrTrait"
            class="space-y-1"
            aria-label="Caractéristiques ou trait"
        >
            <h4 class="text-[10px] font-semibold uppercase tracking-wide text-primary-300 m-0">
                Caractéristiques ou trait
            </h4>
            <p class="text-xs text-base-content/85 m-0">Tu choisis <strong>une</strong> option :</p>
            <ul class="list-disc pl-5 text-xs text-base-content/85 space-y-0.5 m-0">
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

        <section v-if="spells" class="space-y-1" aria-label="Sorts">
            <h4 class="text-[10px] font-semibold uppercase tracking-wide text-primary-300 m-0">
                Sorts
            </h4>
            <BreedLevelSpellChoices
                :breed="breed"
                :character-level="level"
                :characteristic-runtime="characteristicRuntime"
            />
        </section>

        <section
            v-if="caps.length"
            class="space-y-1"
            aria-label="Capacité de classe"
        >
            <h4 class="text-[10px] font-semibold uppercase tracking-wide text-primary-300 m-0">
                Capacité de classe
            </h4>
            <ul class="flex list-none flex-wrap gap-2 p-0 m-0 items-start">
                <li
                    v-for="cap in caps"
                    :key="`cap-${level}-${cap.id}`"
                    class="min-w-0 max-w-[18rem]"
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
    </div>
</template>
