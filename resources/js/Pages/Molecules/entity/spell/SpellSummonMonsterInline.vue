<script setup>
/**
 * Monstre invoqué — aligné sur la vue texte monstre ({@link MonsterViewText} / {@link EntityViewTextLink}) :
 * vignette ou icône + nom, survol → {@link MonsterViewMinimal} (même mode que {@link MonsterViewText} : `extended` = bandeau + détails / caractéristiques si données dispo).
 *
 * @props {{ id: number, name: string, image?: string|null }} monsterBrief - Résumé sérialisé (SpellResource, effets)
 */
import { computed } from "vue";
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import MonsterViewMinimal from "@/Pages/Molecules/entity/monster/MonsterViewMinimal.vue";

const props = defineProps({
    monsterBrief: {
        type: Object,
        required: true,
    },
});

/**
 * Objet compatible {@link MonsterViewMinimal} (créature + lien fiche si `id` connu).
 *
 * @returns {object}
 */
const monsterEntity = computed(() => {
    const id = props.monsterBrief.id;
    const name = props.monsterBrief.name ?? `Monstre #${id}`;
    const image = props.monsterBrief.image ?? null;
    return {
        id,
        name,
        image,
        creature: {
            name,
            image,
        },
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
        name-field="name"
        image-field="image"
        ui-color="primary"
        :show-actions-on-hover="false"
        hover-width-class="max-w-[min(20rem,calc(100vw-2rem))]"
        hover-card-class="border-0 shadow-none"
    />
</template>
