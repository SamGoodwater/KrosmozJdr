<script setup>
/**
 * Onglets / pastilles de niveau d’un sort (« Niv 1 », « Niv 5 », …).
 *
 * - Plusieurs degrés : pastilles cliquables (outline = inactif, plein = actif),
 *   couleur via schéma `level`.
 * - Un seul degré : badge plein au même emplacement (pas d’onglets vides).
 *
 * @example
 * <SpellDegreeTabs v-model="activeIndex" :degrees="degrees" appearance="buttons" />
 */
import { computed } from "vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import {
    spellDegreeLevelNumber,
    spellDegreeTabLabel,
} from "@/Composables/entity/useSpellDisplayDegrees";

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
     * `tabs` : barre DaisyUI (legacy).
     * `buttons` : pastilles badge horizontales (fiche full).
     * `stack` : colonne pleine largeur (carte minimale, sous l’image).
     */
    appearance: {
        type: String,
        default: "tabs",
        validator: (v) => ["tabs", "buttons", "stack"].includes(v),
    },
    /**
     * En `stack` : n’affiche que le niveau actif. Les autres pastilles restent
     * masquées (pas de révélation au survol) — à utiliser en Minimal compact ;
     * le panneau étendu affiche toute la pile sans cette option.
     */
    collapseInactive: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue", "change"]);

const list = computed(() => (Array.isArray(props.degrees) ? props.degrees : []));
const singleDegree = computed(() => list.value.length === 1);
const useBadgePills = computed(
    () => props.appearance === "buttons" || props.appearance === "stack",
);

const stackCollapse = computed(
    () => props.appearance === "stack" && props.collapseInactive && !singleDegree.value,
);

/** En collapse : uniquement le degré actif (pas de pastilles fantômes en compact). */
const stackItems = computed(() => {
    const items = list.value.map((deg, idx) => ({ deg, idx }));
    if (!stackCollapse.value) return items;
    return items.filter(({ idx }) => isActive(idx));
});

const badgeSize = computed(() => (props.size === "xs" ? "xs" : "sm"));

/**
 * @param {object} deg
 * @param {number} idx
 * @returns {string}
 */
function levelAutoLabel(deg, idx) {
    return String(spellDegreeLevelNumber(deg, idx));
}

/**
 * @param {number} idx
 * @returns {boolean}
 */
function isActive(idx) {
    return singleDegree.value || props.modelValue === idx;
}

/**
 * @param {number} idx
 * @returns {''|'outline'}
 */
function badgeVariant(idx) {
    return isActive(idx) ? "" : "outline";
}

/**
 * Classes Daisy tab (appearance `tabs` uniquement).
 *
 * @param {number} idx
 * @returns {Array<string|object>}
 */
function tabClasses(idx) {
    return [
        "tab",
        props.size === "xs" ? "tab-xs" : "tab-sm",
        { "tab-active": isActive(idx) },
    ];
}

/**
 * @param {number} idx
 * @returns {string[]}
 */
function stackButtonWrapClasses(idx) {
    // Ne pas combiner `inline-flex` + `hidden` (ordre CSS Tailwind imprévisible).
    if (stackCollapse.value && !isActive(idx)) {
        return ["hidden"];
    }
    return ["inline-flex", "w-full", "justify-center", "p-0", "shrink-0"];
}

function select(idx) {
    if (singleDegree.value) return;
    emit("update:modelValue", idx);
    emit("change", list.value[idx] || null, idx);
}
</script>

<template>
    <div
        v-if="list.length > 0"
        :role="singleDegree ? undefined : 'tablist'"
        class="flex gap-1"
        :class="
            appearance === 'stack'
                ? [
                      'group/spell-degree-tabs w-full flex-col',
                      stackCollapse ? 'gap-0.5' : 'gap-1',
                  ]
                : appearance === 'tabs'
                  ? 'flex-wrap tabs tabs-boxed bg-base-200/60 p-1'
                  : 'flex-wrap items-center'
        "
        data-cy="spell-degree-tabs"
        :data-single="singleDegree ? 'true' : 'false'"
    >
        <!-- Pastilles badge (buttons / stack) -->
        <template v-if="useBadgePills && appearance === 'stack'">
            <component
                :is="singleDegree ? 'div' : 'button'"
                v-for="{ deg, idx } in stackItems"
                :key="deg.id ?? idx"
                v-bind="
                    singleDegree
                        ? { 'data-cy': 'spell-degree-level-badge' }
                        : {
                              type: 'button',
                              role: 'tab',
                              'aria-selected': isActive(idx),
                          }
                "
                :class="stackButtonWrapClasses(idx)"
                @click="select(idx)"
            >
                <Badge
                    color="auto"
                    :auto-label="levelAutoLabel(deg, idx)"
                    auto-scheme="level"
                    :variant="badgeVariant(idx)"
                    :size="badgeSize"
                    strong
                    class="pointer-events-none w-full justify-center"
                >
                    {{ spellDegreeTabLabel(deg, idx) }}
                </Badge>
            </component>
        </template>
        <template v-else-if="useBadgePills">
            <component
                :is="singleDegree ? 'div' : 'button'"
                v-for="(deg, idx) in list"
                :key="deg.id ?? idx"
                v-bind="
                    singleDegree
                        ? { 'data-cy': 'spell-degree-level-badge' }
                        : {
                              type: 'button',
                              role: 'tab',
                              'aria-selected': isActive(idx),
                          }
                "
                class="inline-flex shrink-0"
                @click="select(idx)"
            >
                <Badge
                    color="auto"
                    :auto-label="levelAutoLabel(deg, idx)"
                    auto-scheme="level"
                    :variant="badgeVariant(idx)"
                    :size="badgeSize"
                    strong
                    class="pointer-events-none"
                >
                    {{ spellDegreeTabLabel(deg, idx) }}
                </Badge>
            </component>
        </template>

        <!-- Daisy tabs (legacy) ; un seul degré → badge niveau -->
        <template v-else-if="singleDegree">
            <Badge
                color="auto"
                :auto-label="levelAutoLabel(list[0], 0)"
                auto-scheme="level"
                :size="badgeSize"
                strong
                data-cy="spell-degree-level-badge"
            >
                {{ spellDegreeTabLabel(list[0], 0) }}
            </Badge>
        </template>
        <template v-else>
            <button
                v-for="(deg, idx) in list"
                :key="deg.id ?? idx"
                type="button"
                role="tab"
                :class="tabClasses(idx)"
                :aria-selected="isActive(idx)"
                @click="select(idx)"
            >
                {{ spellDegreeTabLabel(deg, idx) }}
            </button>
        </template>
    </div>
</template>
