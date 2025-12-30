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
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { getTruncateClass } from "@/Utils/entity/text-truncate";

const props = defineProps({
    cell: {
        type: Object,
        required: true,
    },
    /**
     * Couleur UI par défaut (Design System) utilisée pour les fallbacks (badge/route)
     * quand le backend ne fournit pas de `params.color`.
     */
    uiColor: {
        type: String,
        default: "primary",
    },
});

const type = computed(() => String(props.cell?.type || "text"));
const value = computed(() => props.cell?.value ?? null);
const params = computed(() => props.cell?.params || {});

const effectiveTruncateClass = computed(() => {
    const p = params.value || {};
    if (p.truncateClass) return String(p.truncateClass);
    if (p.truncate && typeof p.truncate === "object") {
        return getTruncateClass({
            context: p.truncate.context,
            scale: p.truncate.scale,
        });
    }
    return "";
});

const text = computed(() => {
    const v = value.value;
    if (v === null || typeof v === "undefined" || v === "") return "—";
    return String(v);
});

/**
 * Détection des booléens affichés en badge (actuellement "Oui/Non") pour les compacter en icône.
 *
 * IMPORTANT: on évite d'inférer à partir de `filterValue` car certaines colonnes "enum"
 * (ex: rareté) peuvent utiliser des codes numériques qui ne sont PAS des booléens.
 *
 * Possibilité d'override serveur: `params.boolean === true` et `params.booleanValue` (true/false/1/0).
 */
const boolBadgeValue = computed(() => {
    if (type.value !== "badge") return null;

    // Override explicite si le backend le fournit
    if (params.value?.boolean === true) {
        const v = params.value?.booleanValue;
        const s = String(v ?? "").toLowerCase();
        if (s === "1" || s === "true") return true;
        if (s === "0" || s === "false") return false;
    }

    // Heuristique locale stricte: uniquement "Oui/Non"
    const t = text.value.toLowerCase();
    if (t === "oui") return true;
    if (t === "non") return false;
    return null;
});

const isBooleanBadge = computed(() => boolBadgeValue.value !== null);
</script>

<template>
    <span v-if="type === 'badge'">
        <span v-if="isBooleanBadge" class="inline-flex items-center justify-center">
                <Icon
                    :source="boolBadgeValue ? 'fa-solid fa-check' : 'fa-solid fa-xmark'"
                    :alt="boolBadgeValue ? 'Oui' : 'Non'"
                    size="sm"
                    :class="boolBadgeValue ? 'text-success-800' : 'text-error-800'"
                />
                <span class="sr-only">{{ boolBadgeValue ? "Oui" : "Non" }}</span>
        </span>

        <Tooltip
            v-else-if="params.tooltip || effectiveTruncateClass"
            class="inline-block align-middle"
            :content="String(params.tooltip || text)"
            placement="top"
        >
            <Badge
                :color="params.color || uiColor"
                :auto-label="params.autoLabel || ''"
                :auto-scheme="params.autoScheme || undefined"
                :auto-tone="params.autoTone || undefined"
                :glassy="Boolean(params.glassy)"
                :variant="params.variant || undefined"
                size="sm"
                :class="effectiveTruncateClass"
            >
                {{ text }}
            </Badge>
        </Tooltip>

        <Badge
            v-else
            :color="params.color || uiColor"
            :auto-label="params.autoLabel || ''"
            :auto-scheme="params.autoScheme || undefined"
            :auto-tone="params.autoTone || undefined"
            :glassy="Boolean(params.glassy)"
            :variant="params.variant || undefined"
            size="sm"
        >
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
        <Tooltip
            v-if="params.href && (params.tooltip || effectiveTruncateClass)"
            class="inline-block align-middle"
            :content="String(params.tooltip || text)"
            placement="top"
        >
            <Route
                :href="String(params.href)"
                :target="params.target || undefined"
                :color="uiColor"
                hover
            >
                <span :class="effectiveTruncateClass">{{ text }}</span>
            </Route>
        </Tooltip>

        <Route
            v-else-if="params.href"
            :href="String(params.href)"
            :target="params.target || undefined"
            :color="uiColor"
            hover
        >
            <span v-if="effectiveTruncateClass" :class="effectiveTruncateClass">{{ text }}</span>
            <template v-else>{{ text }}</template>
        </Route>

        <Tooltip
            v-else-if="params.tooltip || effectiveTruncateClass"
            class="inline-block align-middle"
            :content="String(params.tooltip || text)"
            placement="top"
        >
            <span :class="effectiveTruncateClass">{{ text }}</span>
        </Tooltip>

        <span v-else>{{ text }}</span>
    </span>

    <!-- custom: sera géré plus tard (Phase 1: fallback text) -->
    <span v-else>
        <Tooltip
            v-if="params.tooltip || effectiveTruncateClass"
            class="inline-block align-middle"
            :content="String(params.tooltip || text)"
            placement="top"
        >
            <span :class="effectiveTruncateClass">{{ text }}</span>
        </Tooltip>
        <span v-else>{{ text }}</span>
    </span>
</template>


