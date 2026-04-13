<script setup>
/**
 * MonsterCreatureSpellsList — Sorts liés à la créature du monstre (vue texte + aperçu minimal au survol)
 *
 * @description
 * Affiche une liste compacte : icône / vignette + nom ; au survol, `SpellViewMinimal`.
 * Données attendues : `creature.spells[]` au format API table (SpellResource + usages d’effets), pour PA/PO/effets dans le tooltip.
 *
 * @props {Object|null} creature - Créature (souvent `monster.creature`) avec relation `spells`
 * @props {Object} [tableMeta] - Meta tableau / contexte pour les cellules
 * @props {Object|null} [characteristicRuntime] - Runtime tooltips (optionnel)
 * @props {string} [title] - Libellé de section (défaut : « Sorts »)
 * @props {string} [sectionClass] - Classes du conteneur de section
 */
import { computed } from "vue";
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import SpellViewMinimal from "@/Pages/Molecules/entity/spell/SpellViewMinimal.vue";
import { Spell } from "@/Models/Entity/Spell";

const props = defineProps({
    creature: { type: Object, default: null },
    tableMeta: { type: Object, default: () => ({}) },
    characteristicRuntime: { type: Object, default: null },
    title: { type: String, default: "Sorts" },
    sectionClass: { type: String, default: "" },
});

const spellModels = computed(() => {
    const raw = props.creature?.spells;
    if (!Array.isArray(raw) || raw.length === 0) return [];
    return raw.map((row) => (row instanceof Spell ? row : new Spell(row)));
});
</script>

<template>
    <div v-if="spellModels.length" class="monster-creature-spells-list" :class="sectionClass">
        <p
            class="mb-1 text-[0.625rem] font-semibold uppercase tracking-wide text-base-content/60"
        >
            {{ title }}
        </p>
        <ul class="flex list-none flex-wrap gap-x-3 gap-y-1.5 p-0 m-0">
            <li v-for="spell in spellModels" :key="spell.id" class="min-w-0 max-w-full">
                <EntityViewTextLink
                    :entity="spell"
                    entity-prop="spell"
                    :minimal-component="SpellViewMinimal"
                    fallback-icon="fa-solid fa-wand-magic-sparkles"
                    ui-color="primary"
                    :show-actions-on-hover="false"
                    hover-width-class="w-80 max-w-[min(92vw,22rem)]"
                    :table-meta="tableMeta"
                    :characteristic-runtime="characteristicRuntime"
                />
            </li>
        </ul>
    </div>
</template>
