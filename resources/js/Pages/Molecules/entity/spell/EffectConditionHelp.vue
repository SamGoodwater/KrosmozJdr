<script setup>
/**
 * Aide courte + exemples repliables pour les conditions d’effet.
 */
defineProps({
    mode: {
        type: String,
        default: "condition",
        validator: (v) => ["condition", "value"].includes(v),
    },
});
</script>

<template>
    <details class="rounded-box border border-base-300 bg-base-200/40 px-3 py-2 text-xs text-base-content/80">
        <summary class="cursor-pointer font-medium text-base-content/90">
            Comment écrire une condition
        </summary>
        <div class="mt-2 space-y-2 leading-relaxed">
            <p v-if="mode === 'condition'">
                Chaque condition compare deux formules :
                <code class="text-[0.7rem]">formule</code>
                + comparateur (
                <code class="text-[0.7rem]">&lt; ≤ &gt; ≥ = ≠</code>
                ) +
                <code class="text-[0.7rem]">formule</code>.
                Caractéristiques entre crochets :
                <code class="text-[0.7rem]">[clé]</code>
                ou
                <code class="text-[0.7rem]">[clé:caster]</code>
                = lanceur ;
                <code class="text-[0.7rem]">[clé:target]</code>
                = cible. Opérateurs autorisés :
                <code class="text-[0.7rem]">+ - * /</code>,
                <code class="text-[0.7rem]">min</code>,
                <code class="text-[0.7rem]">max</code>,
                <code class="text-[0.7rem]">floor</code>.
            </p>
            <p v-else>
                La valeur est une formule classique (dés, nombres,
                <code class="text-[0.7rem]">[clé]</code>
                = lanceur). Chaque règle ajoute des conditions puis remplace, ajoute
                ou soustrait une formule.
            </p>
            <ul class="list-disc pl-4 space-y-1">
                <li>
                    Repousse +1 si Agi ≥ 3 :
                    <code class="text-[0.7rem]">[modifier_agility_creature] &gt;= 3</code>
                    → action « s’ajouter », formule
                    <code class="text-[0.7rem]">1</code>
                </li>
                <li>
                    Portée plafonnée :
                    <code class="text-[0.7rem]">min(3 + [strong], 6)</code>
                </li>
                <li>
                    Effet si Intel &gt; Chance de la cible :
                    <code class="text-[0.7rem]">[modifier_intelligence_creature] &gt; [modifier_chance_creature:target]</code>
                </li>
            </ul>
        </div>
    </details>
</template>
