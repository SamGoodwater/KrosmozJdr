<script setup>
/**
 * CharacteristicBoolean — Atome d'affichage d'une caractéristique booléenne (oui/non).
 *
 * @description
 * Compact : icône seule, padding minimal. Normal : carte avec couleur (fond teinté, ombre, blur),
 * icône + label en dessous.
 *
 * @props {Object} def - Définition (key, name, icon, iconFalse?, color, descriptions)
 * @props {boolean} value - Valeur courante
 * @props {boolean} [compact] - Mode compact (icône seule)
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";

const props = defineProps({
    def: { type: Object, required: true },
    value: { type: Boolean, default: false },
    compact: { type: Boolean, default: false },
});

const description = computed(() => props.def?.descriptions || props.def?.helper || "");
const label = computed(() => props.def?.short_name || props.def?.name || props.def?.key || "—");
const iconSource = computed(() =>
    props.value ? props.def?.icon : (props.def?.iconFalse ?? props.def?.icon)
);
const alt = computed(() => props.def?.name || props.def?.key || "—");

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

const iconStyle = computed(() => (props.value && props.def?.color ? { color: props.def.color } : {}));
const containerStyle = computed(() => {
    const c = props.def?.color;
    if (!c || !props.value) return {};
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
        class="characteristic-boolean inline-flex min-w-0 transition-shadow"
        :class="compact ? 'items-center rounded px-1 py-0.5' : 'flex-col items-center rounded-lg border border-base-300 px-2 py-1.5 backdrop-blur-sm'"
        :style="compact ? {} : containerStyle"
    >
        <Tooltip v-if="description && compact" :content="description" placement="top">
            <span
                class="flex items-center"
                :class="value ? '' : 'opacity-50'"
                :style="iconStyle"
            >
                <Icon
                    v-if="iconSource"
                    :source="iconSource"
                    :alt="alt"
                    size="xs"
                    :disabled="!value"
                />
            </span>
        </Tooltip>
        <template v-else-if="compact">
            <span
                class="flex items-center"
                :class="value ? '' : 'opacity-50'"
                :style="iconStyle"
            >
                <Icon
                    v-if="iconSource"
                    :source="iconSource"
                    :alt="alt"
                    size="xs"
                    :disabled="!value"
                />
            </span>
        </template>
        <template v-else>
            <Tooltip v-if="description" :content="description" placement="top">
                <span
                    class="flex items-center"
                    :class="value ? '' : 'opacity-50'"
                    :style="iconStyle"
                >
                    <Icon
                        v-if="iconSource"
                        :source="iconSource"
                        :alt="alt"
                        size="sm"
                        :disabled="!value"
                    />
                </span>
            </Tooltip>
            <span
                v-else
                class="flex items-center"
                :class="value ? '' : 'opacity-50'"
                :style="iconStyle"
            >
                <Icon
                    v-if="iconSource"
                    :source="iconSource"
                    :alt="alt"
                    size="sm"
                    :disabled="!value"
                />
            </span>
            <p class="mt-0.5 text-xs opacity-80">{{ label }}</p>
        </template>
    </div>
</template>
