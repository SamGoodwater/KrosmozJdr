<script setup>
/**
 * Aperçu schématique de la zone d’effet (damier) à partir de la notation Krosmoz.
 *
 * @description
 * Taille max du bloc fixe ; les cases rétrécissent si la zone couvre beaucoup de cellules.
 *
 * @see docs/50-Fonctionnalités/Spell-Effects/ZONE_NOTATION.md
 */
import { computed } from 'vue';
import {
    parseAreaToCells,
    buildSquareGridModel,
    computeCellPx,
} from '@/Utils/Entity/areaNotationGrid';

const props = defineProps({
    /** Notation (ex. circle-0-2, line-1x9) */
    area: {
        type: String,
        default: '',
    },
    /** Côté max du schéma (px) */
    maxViewportPx: {
        type: Number,
        default: 168,
    },
    /** Taille mini d’une case si le schéma est petit (px) */
    cellSizeMinPx: {
        type: Number,
        default: 12,
    },
});

const cells = computed(() => parseAreaToCells(props.area));

const model = computed(() => {
    const list = cells.value;
    if (!list.length) return null;
    return buildSquareGridModel(list, 1);
});

const cellPx = computed(() => {
    const m = model.value;
    if (!m) return props.cellSizeMinPx;
    return computeCellPx(m.side, props.maxViewportPx, props.cellSizeMinPx);
});

const viewportPx = computed(() => {
    const m = model.value;
    if (!m) return 0;
    return m.side * cellPx.value;
});

const gridStyle = computed(() => {
    const m = model.value;
    if (!m) return {};
    const px = cellPx.value;
    return {
        display: 'grid',
        width: `${viewportPx.value}px`,
        height: `${viewportPx.value}px`,
        gridTemplateColumns: `repeat(${m.side}, ${px}px)`,
        gridTemplateRows: `repeat(${m.side}, ${px}px)`,
    };
});

const cellIndices = computed(() => {
    const m = model.value;
    if (!m) return [];
    const n = m.side * m.side;
    return Array.from({ length: n }, (_, i) => i);
});

function isActive(m, x, y) {
    return m.active.has(`${x},${y}`);
}
</script>

<template>
    <div class="spell-zone-preview inline-flex flex-col gap-1.5">
        <div
            v-if="model"
            class="spell-zone-preview__frame rounded-md border border-base-content/10 bg-base-200/30 p-1"
            :style="{ width: `${viewportPx + 8}px` }"
        >
            <div
                class="spell-zone-preview__grid mx-auto"
                :style="gridStyle"
                role="img"
                :aria-label="`Zone d'effet ${area}`"
            >
                <div
                    v-for="idx in cellIndices"
                    :key="idx"
                    class="spell-zone-preview__cell box-border border-[0.5px] border-base-content/20"
                    :class="{
                        'bg-primary/45': isActive(model, idx % model.side, Math.floor(idx / model.side)),
                        'bg-base-100/20': !isActive(model, idx % model.side, Math.floor(idx / model.side)),
                    }"
                />
            </div>
        </div>
        <p v-else class="text-xs text-base-content/60 max-w-48">
            Schéma non disponible pour cette notation.
        </p>
        <p v-if="area && !model" class="font-mono text-[10px] text-base-content/50 break-all">
            {{ area }}
        </p>
    </div>
</template>
