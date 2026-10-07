<script setup>
/**
 * Porteurs d’un sort : liste compacte + aperçu minimal (monstres).
 */
import { computed, markRaw, ref } from 'vue';
import MonsterViewMinimal from '@/Pages/Molecules/entity/monster/MonsterViewMinimal.vue';
import OverlayTrigger from '@/Pages/Molecules/overlay/OverlayTrigger.vue';

const props = defineProps({
    spellHolders: { type: Object, default: () => ({}) },
});

const open = ref(false);

const groups = computed(() =>
    [
        { key: 'monsters', label: 'Monstres', icon: 'fa-solid fa-dragon', items: props.spellHolders?.monsters || [] },
        { key: 'npcs', label: 'PNJ', icon: 'fa-solid fa-user', items: props.spellHolders?.npcs || [] },
        { key: 'creatures', label: 'Créatures', icon: 'fa-solid fa-paw', items: props.spellHolders?.creatures || [] },
        { key: 'breeds', label: 'Classes', icon: 'fa-solid fa-hat-wizard', items: props.spellHolders?.breeds || [] },
    ].filter((g) => g.items.length > 0),
);

const count = computed(() =>
    groups.value.reduce((total, group) => total + group.items.length, 0),
);

function statsLine(holder) {
    const s = holder?.stats;
    if (!s) return '';
    const parts = [];
    if (s.vitality != null && s.vitality !== '') parts.push(`PV ${s.vitality}`);
    if (s.pa != null && s.pa !== '') parts.push(`${s.pa} PA`);
    if (s.pm != null && s.pm !== '') parts.push(`${s.pm} PM`);
    return parts.join(' · ');
}

function levelLabel(holder) {
    if (holder?.level == null) return null;
    return `Niv. ${holder.level}`;
}

function monsterOverlayContent(holder) {
    return {
        component: markRaw(MonsterViewMinimal),
        props: {
            monster: holder.monster,
            showActions: false,
            displayMode: 'compact',
        },
    };
}
</script>

<template>
    <div v-if="count" class="spell-holders-panel" data-cy="spell-holders-panel">
        <button
            type="button"
            class="btn btn-ghost btn-xs gap-1.5 border border-base-300"
            @click="open = !open"
        >
            <i class="fa-solid fa-users" aria-hidden="true" />
            Porteurs ({{ count }})
        </button>

        <div
            v-if="open"
            class="mt-2 rounded-box border border-base-300 bg-base-100 p-3 max-h-80 overflow-y-auto space-y-3"
        >
            <div v-for="group in groups" :key="group.key" class="space-y-1.5">
                <h4 class="text-xs font-semibold text-base-content/70 uppercase tracking-wide">
                    <i :class="group.icon" class="mr-1" aria-hidden="true" />
                    {{ group.label }}
                </h4>
                <ul class="space-y-1">
                    <li
                        v-for="holder in group.items"
                        :key="`${group.key}-${holder.id}`"
                        class="flex flex-wrap items-center gap-2 text-sm"
                    >
                        <OverlayTrigger
                            v-if="group.key === 'monsters' && holder.monster"
                            :content="monsterOverlayContent(holder)"
                            trigger="click"
                            placement="bottom-start"
                            :interactive="true"
                            :chromeless="true"
                            panel-class="max-w-[min(92vw,22rem)]"
                        >
                            <button
                                type="button"
                                class="link link-hover font-medium text-left"
                            >
                                {{ holder.name }}
                            </button>
                        </OverlayTrigger>
                        <a v-else :href="holder.href" class="link link-hover font-medium">
                            {{ holder.name }}
                        </a>

                        <span
                            v-if="levelLabel(holder)"
                            class="badge badge-ghost badge-sm"
                        >
                            {{ levelLabel(holder) }}
                        </span>
                        <span
                            v-if="holder.accessible_degree?.label"
                            class="badge badge-outline badge-sm"
                            title="Degré accessible"
                        >
                            {{ holder.accessible_degree.label }}
                        </span>
                        <span
                            v-if="statsLine(holder)"
                            class="text-xs text-base-content/70 tabular-nums"
                        >
                            {{ statsLine(holder) }}
                        </span>
                        <a
                            :href="holder.href"
                            class="btn btn-ghost btn-xs ml-auto"
                            title="Ouvrir la fiche"
                        >
                            <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true" />
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
