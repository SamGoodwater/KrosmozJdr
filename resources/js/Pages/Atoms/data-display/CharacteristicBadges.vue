<script setup>
/**
 * CharacteristicBadges — Atome d'affichage d'une liste de badges (ex. taille, propriétés).
 *
 * @description
 * Compact : gap et padding minimaux. Normal : carte avec couleur optionnelle (fond teinté, ombre, blur).
 *
 * @props {Object} [def] - Définition (value_available, color pour le conteneur en mode normal)
 * @props {Array} [items] - Liste directe de badges
 * @props {Array} [value] - Valeurs (si def fourni, pour résoudre les libellés)
 * @props {boolean} [compact] - Mode compact (gap réduit)
 */
import { computed } from "vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";

const props = defineProps({
    def: { type: Object, default: null },
    items: { type: Array, default: () => [] },
    value: { type: Array, default: () => [] },
    compact: { type: Boolean, default: false },
});

const badges = computed(() => {
    if (Array.isArray(props.items) && props.items.length > 0) {
        return props.items.map((it) =>
            typeof it === "string" ? { label: it } : { label: it.label ?? String(it), color: it.color }
        );
    }
    const vals = Array.isArray(props.value) ? props.value : [];
    const available = props.def?.value_available;
    if (Array.isArray(available)) {
        return vals.map((v) => {
            const entry = available.find((a) => (typeof a === "object" && a?.value === v) || a === v);
            const label = typeof entry === "object" && entry?.label != null ? entry.label : String(v);
            return { label, color: typeof entry === "object" ? entry?.color : undefined };
        });
    }
    return vals.map((v) => ({ label: String(v) }));
});

function hexToRgba(hex, a) {
    if (!hex || typeof hex !== "string") return null;
    let h = hex.replace(/^#/, "");
    if (h.length === 3) h = h[0] + h[0] + h[1] + h[1] + h[2] + h[2];
    if (h.length !== 6) return null;
    const r = parseInt(h.slice(0, 2), 16);
    const g = parseInt(h.slice(2, 4), 16);
    const b = parseInt(h.slice(4, 6), 16);
    return `rgba(${r},${g},${b},${a})`;
}

const containerStyle = computed(() => {
    const c = props.def?.color;
    if (!c || props.compact) return {};
    const rgba = hexToRgba(c, 0.08);
    const shadowRgba = hexToRgba(c, 0.15);
    if (!rgba || !shadowRgba) return {};
    return {
        backgroundColor: rgba,
        boxShadow: `0 1px 3px ${shadowRgba}`,
        ...(hexToRgba(c, 0.2) ? { borderColor: hexToRgba(c, 0.2) } : {}),
    };
});
</script>

<template>
    <div
        class="characteristic-badges flex flex-wrap"
        :class="compact ? 'gap-1 px-1 py-0.5' : 'gap-1.5 rounded-lg border border-base-300 px-2 py-1.5 backdrop-blur-sm'"
        :style="compact ? {} : containerStyle"
    >
        <Badge
            v-for="(badge, i) in badges"
            :key="i"
            :content="badge.label"
            :color="badge.color || 'neutral'"
            :size="compact ? 'xs' : 'sm'"
            variant="soft"
        />
    </div>
</template>
