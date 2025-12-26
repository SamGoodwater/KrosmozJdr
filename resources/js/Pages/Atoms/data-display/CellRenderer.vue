<script setup>
/**
 * CellRenderer Atom
 *
 * @description
 * Rend une cellule de tableau à partir d'un objet `Cell{type,value,params}`.
 * Le backend est responsable des paramètres métier/visuels (ex: badge color, href).
 *
 * @example
 * <CellRenderer :cell="row.cells.name" />
 */

import { computed } from "vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Route from "@/Pages/Atoms/action/Route.vue";

const props = defineProps({
    cell: {
        type: Object,
        required: true,
    },
});

const type = computed(() => String(props.cell?.type || "text"));
const value = computed(() => props.cell?.value ?? null);
const params = computed(() => props.cell?.params || {});

const text = computed(() => {
    const v = value.value;
    if (v === null || typeof v === "undefined" || v === "") return "—";
    return String(v);
});
</script>

<template>
    <span v-if="type === 'badge'">
        <Badge :color="params.color || 'primary'" size="sm">
            {{ text }}
        </Badge>
    </span>

    <span v-else-if="type === 'icon'" class="inline-flex items-center justify-center">
        <Icon
            v-if="value"
            :source="String(value)"
            :alt="params.alt || 'Icône'"
            size="sm"
        />
        <span v-else class="text-base-content/40">—</span>
    </span>

    <span v-else-if="type === 'image'" class="inline-flex items-center justify-center">
        <img
            v-if="value"
            :src="String(value)"
            :alt="params.alt || 'Image'"
            class="h-8 w-8 rounded object-contain bg-base-200"
            loading="lazy"
        />
        <span v-else class="text-base-content/40">—</span>
    </span>

    <span v-else-if="type === 'route'">
        <Route
            v-if="params.href"
            :href="String(params.href)"
            :target="params.target || undefined"
            color="primary"
            hover
        >
            {{ text }}
        </Route>
        <span v-else>{{ text }}</span>
    </span>

    <!-- custom: sera géré plus tard (Phase 1: fallback text) -->
    <span v-else>
        {{ text }}
    </span>
</template>


