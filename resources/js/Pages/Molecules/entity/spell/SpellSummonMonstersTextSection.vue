<script setup>
/**
 * Section « Invocations » — même disposition pour vues minimal et line du sort.
 * Chaque entrée : {@link SpellSummonMonsterInline} (vue texte monstre : icône + nom, survol → minimal).
 *
 * @props {Array<{ id: number, name: string, image?: string|null }>} monsters - Résumés `summon_monster` (effets)
 * @props {string} wrapperClass - Hook sémantique (ex. `spell-summon-monsters` | `spell-summon-line`)
 */
import SpellSummonMonsterInline from "@/Pages/Molecules/entity/spell/SpellSummonMonsterInline.vue";

defineProps({
    monsters: {
        type: Array,
        required: true,
    },
    wrapperClass: {
        type: String,
        default: "spell-summon-monsters",
    },
});
</script>

<template>
    <div
        v-if="monsters.length > 0"
        :class="[
            wrapperClass,
            'grid w-full grid-rows-[0fr] transition-[grid-template-rows] duration-200 ease-out group-hover:grid-rows-[1fr]',
        ]"
    >
        <div class="min-h-0 overflow-hidden group-hover:overflow-visible">
            <div class="flex flex-col gap-1.5 border-t border-base-300 pt-1.5 mt-1">
                <span class="text-[10px] font-semibold uppercase tracking-wide text-base-content/55">
                    Invocation{{ monsters.length > 1 ? "s" : "" }}
                </span>
                <div class="flex min-w-0 flex-wrap items-center gap-x-3 gap-y-1">
                    <SpellSummonMonsterInline
                        v-for="(m, idx) in monsters"
                        :key="`${m.id ?? 'm'}-${idx}`"
                        :monster-brief="m"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
