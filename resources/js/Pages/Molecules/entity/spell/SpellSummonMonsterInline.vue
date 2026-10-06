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
import NpcViewMinimal from "@/Pages/Molecules/entity/npc/NpcViewMinimal.vue";

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

const entityType = computed(() => props.monsterBrief.entity_type || "monster");
const entityProp = computed(() => (entityType.value === "npc" ? "npc" : "monster"));
const minimalComponent = computed(() =>
    entityType.value === "npc" ? NpcViewMinimal : MonsterViewMinimal,
);
</script>

<template>
    <a
        v-if="entityType === 'creature'"
        :href="route('entities.creatures.show', { creature: monsterBrief.creature_id || monsterBrief.id })"
        class="inline-flex min-w-0 items-center gap-1.5 font-medium hover:underline"
    >
        <i class="fa-solid fa-paw shrink-0" aria-hidden="true"></i>
        <span class="truncate">{{ monsterBrief.name }}</span>
    </a>
    <EntityViewTextLink
        v-else
        :entity="monsterEntity"
        :entity-prop="entityProp"
        :minimal-component="minimalComponent"
        fallback-icon="fa-solid fa-dragon"
        name-field="name"
        image-field="image"
        ui-color="primary"
        :show-actions-on-hover="false"
        hover-width-class="max-w-[min(20rem,calc(100vw-2rem))]"
        hover-card-class="border-0 shadow-none"
    />
</template>
