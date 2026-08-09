<script setup>
/**
 * PinnedEntitiesHost — Fenêtres flottantes multi-entités (épinglage local).
 *
 * @description
 * Monte dans le layout Main. Affiche une vue minimal `extended` par entité épinglée,
 * déplaçable, empilable (z-index).
 */
import { computed, ref, shallowRef, watch } from "vue";
import {
    bringPinnedWindowToFront,
    listPinnedWindows,
    toggleEntityPin,
    updatePinnedWindowPosition,
    usePinnedEntityVersion,
} from "@/Composables/entity/usePinnedEntityIds";
import { resolveEntityViewComponent } from "@/Utils/entity/resolveEntityViewComponent";
import { normalizeActionEntityType } from "@/Entities/entity-actions-config";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";

const pinVersion = usePinnedEntityVersion();

const windows = computed(() => {
    pinVersion.value;
    return listPinnedWindows();
});

/** @type {import('vue').ShallowRef<Record<string, object|null>>} */
const componentByType = shallowRef({});

watch(
    windows,
    async (list) => {
        const types = [...new Set(list.map((w) => w.entityType))];
        const next = { ...componentByType.value };
        await Promise.all(
            types.map(async (type) => {
                if (next[type]) return;
                next[type] = await resolveEntityViewComponent(type, "minimal");
            }),
        );
        componentByType.value = next;
    },
    { immediate: true, deep: true },
);

const PROP_BY_TYPE = Object.freeze({
    monsters: "monster",
    spells: "spell",
    items: "item",
    resources: "resource",
    consumables: "consumable",
    breeds: "breed",
    panoplies: "panoply",
    capabilities: "capability",
    npcs: "npc",
    conditions: "condition",
    shops: "shop",
    campaigns: "campaign",
    scenarios: "scenario",
    specializations: "specialization",
    "creature-traits": "creatureTrait",
    "resource-types": "resourceType",
});

function entityPropName(entityType) {
    const t = normalizeActionEntityType(entityType);
    return PROP_BY_TYPE[t] || "entity";
}

function windowKey(win) {
    return `${win.entityType}:${win.id}`;
}

function titleOf(win) {
    const e = win.entity;
    if (!e) return `${win.entityType} #${win.id}`;
    return (
        e.name ||
        e.title ||
        e.creature?.name ||
        e._data?.name ||
        `${win.entityType} #${win.id}`
    );
}

/** Drag state */
const drag = ref(null);

function onPointerDown(event, win) {
    if (event.button !== 0) return;
    const target = event.target;
    if (target?.closest?.("[data-pin-no-drag]")) return;
    bringPinnedWindowToFront(win.entityType, win.id);
    drag.value = {
        entityType: win.entityType,
        id: win.id,
        startX: event.clientX,
        startY: event.clientY,
        origX: win.x,
        origY: win.y,
    };
    window.addEventListener("pointermove", onPointerMove);
    window.addEventListener("pointerup", onPointerUp, { once: true });
}

function onPointerMove(event) {
    if (!drag.value) return;
    const dx = event.clientX - drag.value.startX;
    const dy = event.clientY - drag.value.startY;
    updatePinnedWindowPosition(
        drag.value.entityType,
        drag.value.id,
        drag.value.origX + dx,
        drag.value.origY + dy,
    );
}

function onPointerUp() {
    drag.value = null;
    window.removeEventListener("pointermove", onPointerMove);
}

function closeWindow(win) {
    toggleEntityPin(win.entityType, win.id);
}

function viewProps(win) {
    const prop = entityPropName(win.entityType);
    return {
        [prop]: win.entity || { id: Number(win.id) || win.id },
        displayMode: "extended",
        showActions: true,
    };
}
</script>

<template>
    <Teleport to="body">
        <div class="pinned-entities-host pointer-events-none fixed inset-0 z-[1200]" aria-live="polite">
            <div
                v-for="win in windows"
                :key="windowKey(win)"
                class="pinned-entity-window pointer-events-auto absolute flex max-h-[min(85vh,40rem)] w-[min(22rem,92vw)] flex-col overflow-hidden rounded-box border border-base-300 bg-base-100 shadow-2xl"
                :style="{
                    left: `${win.x}px`,
                    top: `${win.y}px`,
                    zIndex: win.z,
                }"
                @mousedown="bringPinnedWindowToFront(win.entityType, win.id)"
            >
                <header
                    class="flex cursor-grab items-center gap-2 border-b border-base-300 bg-base-200/80 px-2 py-1.5 active:cursor-grabbing"
                    @pointerdown="(e) => onPointerDown(e, win)"
                >
                    <Icon source="fa-solid fa-thumbtack" size="xs" class="text-primary opacity-80" />
                    <span class="min-w-0 flex-1 truncate text-xs font-semibold">{{ titleOf(win) }}</span>
                    <Btn
                        data-pin-no-drag
                        size="xs"
                        variant="ghost"
                        color="neutral"
                        class="btn-square"
                        aria-label="Fermer l’épinglage"
                        @click.stop="closeWindow(win)"
                    >
                        <Icon source="fa-solid fa-xmark" size="xs" />
                    </Btn>
                </header>
                <div class="min-h-0 flex-1 overflow-y-auto overflow-x-hidden p-1">
                    <component
                        :is="componentByType[win.entityType]"
                        v-if="componentByType[win.entityType] && win.entity"
                        v-bind="viewProps(win)"
                    />
                    <p v-else class="p-3 text-xs opacity-70">
                        Fiche épinglée #{{ win.id }} — rouvre depuis la liste pour recharger le contenu.
                    </p>
                </div>
            </div>
        </div>
    </Teleport>
</template>
