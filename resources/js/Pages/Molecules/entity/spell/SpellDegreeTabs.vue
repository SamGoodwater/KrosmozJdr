<script setup>
/**
 * Onglets de niveau d’un sort (« Niv 1 », « Niv 5 », … selon les degrés).
 *
 * @example
 * <SpellDegreeTabs v-model="activeIndex" :degrees="degrees" />
 */
import { computed } from "vue";
import { spellDegreeTabLabel } from "@/Composables/entity/useSpellDisplayDegrees";

const props = defineProps({
    degrees: {
        type: Array,
        default: () => [],
    },
    modelValue: {
        type: Number,
        default: 0,
    },
    size: {
        type: String,
        default: "sm",
        validator: (v) => ["xs", "sm"].includes(v),
    },
    /**
     * `tabs` : barre DaisyUI.
     * `buttons` : pastilles horizontales lisibles sur carte sombre.
     * `stack` : colonne pleine largeur (carte minimale, sous l’image).
     */
    appearance: {
        type: String,
        default: "tabs",
        validator: (v) => ["tabs", "buttons", "stack"].includes(v),
    },
    /**
     * En `stack` : n’affiche que le niveau actif ; les autres apparaissent
     * au survol de la pile (ou de la carte `group/entity-minimal`).
     */
    collapseInactive: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue", "change"]);

const list = computed(() => (Array.isArray(props.degrees) ? props.degrees : []));

const stackCollapse = computed(
    () => props.appearance === "stack" && props.collapseInactive,
);

function select(idx) {
    emit("update:modelValue", idx);
    emit("change", list.value[idx] || null, idx);
}

/**
 * @param {number} idx
 * @returns {string[]}
 */
function buttonClasses(idx) {
    const active = props.modelValue === idx;
    if (props.appearance === "stack") {
        const base = [
            "btn w-full justify-center font-semibold",
            props.size === "xs" ? "btn-xs px-1" : "btn-sm",
            active
                ? "btn-primary"
                : "btn-ghost border border-base-content/30 text-base-content",
        ];
        if (stackCollapse.value && !active) {
            base.push(
                "hidden",
                "group-hover/spell-degree-tabs:inline-flex",
                "group-hover/entity-minimal:inline-flex",
                "group-focus-within/entity-minimal:inline-flex",
            );
        }
        return base;
    }
    if (props.appearance === "buttons") {
        return [
            "btn font-semibold",
            props.size === "xs" ? "btn-xs" : "btn-sm",
            active
                ? "btn-primary"
                : "btn-ghost border border-base-content/30 text-base-content",
        ];
    }
    return [
        "tab",
        props.size === "xs" ? "tab-xs" : "tab-sm",
        { "tab-active": active },
    ];
}
</script>

<template>
    <div
        v-if="list.length > 1"
        role="tablist"
        class="flex gap-1"
        :class="
            appearance === 'stack'
                ? [
                      'group/spell-degree-tabs w-full flex-col',
                      stackCollapse ? 'gap-0.5' : '',
                  ]
                : appearance === 'tabs'
                  ? 'flex-wrap tabs tabs-boxed bg-base-200/60 p-1'
                  : 'flex-wrap'
        "
        data-cy="spell-degree-tabs"
    >
        <button
            v-for="(deg, idx) in list"
            :key="deg.id ?? idx"
            type="button"
            role="tab"
            :class="buttonClasses(idx)"
            :aria-selected="modelValue === idx"
            @click="select(idx)"
        >
            {{ spellDegreeTabLabel(deg, idx) }}
        </button>
    </div>
</template>
