<script setup>
/**
 * SpellEffectChips — Effets d’un sort en chips, avec sélecteurs de palier compacts.
 *
 * @description
 * Paliers : mini-badges (chiffre seul). Avec niveau créature requis → {@link LevelBadge}
 * (dégradé de couleur comme ailleurs dans l’app). Sinon → badge outline neutre (numéro de degré).
 *
 * @example
 * <SpellEffectChips :items="items" />
 */
import { ref, computed, watch } from "vue";
import CharacteristicInlineGroup from "@/Pages/Molecules/data-display/CharacteristicInlineGroup.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import SpellEffectUsageMinimalLine from "@/Pages/Molecules/entity/spell/SpellEffectUsageMinimalLine.vue";
import SpellEffectUsageActionLine from "@/Pages/Molecules/entity/spell/SpellEffectUsageActionLine.vue";

const props = defineProps({
    /** Chips avec { icon, color, value, tooltip, degree, requiredCreatureLevel } */
    items: {
        type: Array,
        default: () => [],
    },
    /** Transmis à CharacteristicInlineGroup (full | short | icon-only) */
    labelMode: {
        type: String,
        default: "full",
        validator: (v) => ["full", "short", "icon-only"].includes(v),
    },
    /** `minimal` : ligne icônes + zone (carte minimal, ligne tableau) ; sinon chips inline classiques */
    layout: {
        type: String,
        default: "default",
        validator: (v) => ["default", "minimal"].includes(v),
    },
});

/**
 * Clé de palier alignée sur le tri backend (niveau créature, puis degré).
 * null / absent niveau créature → -1 pour trier les paliers « de base » en premier.
 *
 * @param {object} item
 * @returns {string}
 */
function chipTierKey(item) {
    const raw = item?.requiredCreatureLevel?.min;
    const creature =
        raw != null && raw !== "" && Number.isFinite(Number(raw)) ? Number(raw) : -1;
    const deg = Number(item?.degree ?? 1);
    return `${creature}|${deg}`;
}

/**
 * @param {object} item
 * @returns {string}
 */
function chipTierLabel(item) {
    const lab = item?.requiredCreatureLevel?.label;
    if (lab != null && String(lab).trim() !== "") {
        return String(lab).trim();
    }
    const d = item?.degree;
    if (d != null && Number.isFinite(Number(d))) {
        return `Palier ${d}`;
    }
    return "Base";
}

const activeTierIndex = ref(0);

const tierTabs = computed(() => {
    const list = props.items || [];
    if (!list.length) {
        return [];
    }
    const byKey = new Map();
    for (const it of list) {
        const key = chipTierKey(it);
        if (!byKey.has(key)) {
            byKey.set(key, chipTierLabel(it));
        }
    }
    const keys = Array.from(byKey.keys());
    keys.sort((ka, kb) => {
        const [ca, da] = ka.split("|").map(Number);
        const [cb, db] = kb.split("|").map(Number);
        if (ca !== cb) {
            return ca - cb;
        }
        return da - db;
    });
    return keys.map((key) => {
        const [cRaw, deg] = key.split("|").map(Number);
        return {
            key,
            label: byKey.get(key),
            /** Niveau créature min. requis ; `null` si palier sans seuil (tri interne -1). */
            creatureLevel: cRaw < 0 ? null : cRaw,
            degree: deg,
        };
    });
});

watch(
    tierTabs,
    (tabs) => {
        if (activeTierIndex.value >= tabs.length) {
            activeTierIndex.value = 0;
        }
    },
    { deep: true },
);

const visibleItems = computed(() => {
    const list = props.items || [];
    if (!list.length) {
        return [];
    }
    const tabs = tierTabs.value;
    if (tabs.length <= 1) {
        return list;
    }
    const key = tabs[activeTierIndex.value]?.key;
    if (key == null) {
        return list;
    }
    return list.filter((it) => chipTierKey(it) === key);
});

/**
 * @param {object} it
 * @returns {boolean}
 */
function minimalRowHasContent(it) {
    const text = it?.value != null ? String(it.value).trim() : "";
    const area = it?.area != null ? String(it.area).trim() : "";
    const char = it?.characteristic != null ? String(it.characteristic).trim() : "";
    const el = Number(it?.element);
    const hasElement = Number.isFinite(el) && el > 0;
    const sm = it?.summon_monster;
    const hasSummon =
        sm && typeof sm === "object" && sm.id != null && Number.isFinite(Number(sm.id));
    const hasAction = it?.action_slug != null && String(it.action_slug).trim() !== "";
    return text !== "" || area !== "" || char !== "" || hasElement || hasSummon || hasAction;
}

const visibleMinimalItems = computed(() => visibleItems.value.filter(minimalRowHasContent));

/** Ligne structurée (API avec `action_slug`) : `minimal` vs `line` selon le layout des chips. */
const structuredLineDensity = computed(() => (props.layout === "minimal" ? "minimal" : "line"));

function itemUsesStructuredAction(it) {
    const s = it?.action_slug;
    return s != null && String(s).trim() !== "";
}
</script>

<template>
    <span class="inline-flex min-w-0 max-w-full flex-col gap-1 align-middle">
        <div
            v-if="tierTabs.length > 1"
            role="tablist"
            class="inline-flex max-w-full flex-wrap items-center gap-0.5"
            aria-label="Paliers d’effet"
        >
            <button
                v-for="(t, i) in tierTabs"
                :key="t.key"
                type="button"
                role="tab"
                class="inline-flex min-h-0 min-w-0 shrink-0 cursor-pointer items-center justify-center rounded border border-transparent bg-transparent p-0 leading-none transition-[opacity,box-shadow] hover:opacity-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 focus-visible:ring-offset-1 focus-visible:ring-offset-base-100"
                :class="
                    activeTierIndex === i
                        ? 'opacity-100 shadow-sm ring-1 ring-primary/50 ring-offset-1 ring-offset-base-100'
                        : 'opacity-55'
                "
                :aria-selected="activeTierIndex === i"
                :title="t.label"
                @click="activeTierIndex = i"
            >
                <LevelBadge
                    v-if="t.creatureLevel != null"
                    :level="t.creatureLevel"
                    size="xs"
                    variant="soft"
                    :tooltip="t.label"
                />
                <Badge
                    v-else
                    color="neutral"
                    variant="outline"
                    size="xs"
                    :strong="true"
                    :content="String(t.degree)"
                    :title="t.label"
                />
            </button>
        </div>
        <div v-if="layout === 'minimal'" class="flex min-w-0 max-w-full flex-col gap-1">
            <template v-for="(it, idx) in visibleMinimalItems" :key="idx">
                <SpellEffectUsageActionLine
                    v-if="itemUsesStructuredAction(it)"
                    :item="it"
                    :density="structuredLineDensity"
                    class="min-w-0"
                />
                <SpellEffectUsageMinimalLine v-else :item="it" class="min-w-0" />
            </template>
        </div>
        <CharacteristicInlineGroup
            v-else
            :items="visibleItems"
            :label-mode="labelMode"
            class="min-w-0"
        />
    </span>
</template>
