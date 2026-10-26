<script setup>
/**
 * Monstre invoqué en vue minimale (page / modal de sort).
 *
 * @example
 * <SpellSummonMonsterMinimal :monster-brief="{ id: 1, name: 'Tofu' }" />
 */
import { computed } from "vue";
import MonsterViewMinimal from "@/Pages/Molecules/entity/monster/MonsterViewMinimal.vue";
import NpcViewMinimal from "@/Pages/Molecules/entity/npc/NpcViewMinimal.vue";

const props = defineProps({
    monsterBrief: {
        type: Object,
        required: true,
    },
});

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
    <NpcViewMinimal
        v-else-if="entityType === 'npc'"
        :npc="monsterEntity"
        display-mode="compact"
        :show-actions="false"
        class="min-w-0 max-w-xs"
    />
    <MonsterViewMinimal
        v-else
        :monster="monsterEntity"
        display-mode="compact"
        :show-actions="false"
        class="min-w-0 max-w-xs"
    />
</template>
