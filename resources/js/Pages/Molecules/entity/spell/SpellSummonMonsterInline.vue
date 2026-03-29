<script setup>
/**
 * Monstre invoqué — rendu « vue Texte » : miniature + nom, survol → MonsterViewMinimal.
 *
 * @props {{ id: number, name: string, image?: string|null }} monsterBrief - Résumé sérialisé (SpellResource)
 */
import { computed } from 'vue';
import EntityViewTextLink from '@/Pages/Molecules/entity/shared/EntityViewTextLink.vue';
import MonsterViewMinimal from '@/Pages/Molecules/entity/monster/MonsterViewMinimal.vue';

const props = defineProps({
    monsterBrief: {
        type: Object,
        required: true,
    },
});

/** Objet léger (sans `toCell`) : `EntityViewTextLink` lit `name` / `image` au premier niveau. */
const monsterEntity = computed(() => {
    const id = props.monsterBrief.id;
    const name = props.monsterBrief.name ?? `Monstre #${id}`;
    const image = props.monsterBrief.image ?? null;
    return {
        id,
        name,
        image,
        creature: { name, image },
        can: { view: true, update: false, delete: false },
    };
});
</script>

<template>
    <EntityViewTextLink
        :entity="monsterEntity"
        entity-prop="monster"
        :minimal-component="MonsterViewMinimal"
        fallback-icon="fa-solid fa-dragon"
        hover-width-class="w-72"
        :show-actions-on-hover="false"
        minimal-display-mode="hover"
        ui-color="primary"
    />
</template>
