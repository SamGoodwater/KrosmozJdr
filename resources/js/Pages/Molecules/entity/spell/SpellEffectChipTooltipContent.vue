<script setup>
/**
 * Contenu visuel des tooltips des chips / lignes d’effets de sort.
 *
 * @props {object} model - Résultat de {@link buildUnifiedSubEffectModel}
 * @props {string} [detailText] - Texte détaillé (meta palier, zone, etc.)
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import {
    resolveDef,
    SPELL_EFFECT_CHIP_SOURCE_GROUPS,
} from "@/Composables/entity/useCharacteristicDisplay";
import { getElementIcon, getElementLabel, getElementPrimaries } from "@/Utils/Entity/Elements";
import { colord } from "colord";
import { resolvePresentationActionSlug } from "@/Composables/entity/useSpellSubEffectPresentation";

const props = defineProps({
    model: {
        type: Object,
        required: true,
    },
    detailText: {
        type: String,
        default: "",
    },
});

const m = computed(() => props.model ?? {});

/** Couleurs d’accent pour halo (éléments primaires 0–4). */
const ELEMENT_ACCENT_HEX = Object.freeze({
    0: "#94a3b8",
    1: "#d97706",
    2: "#ef4444",
    3: "#34d399",
    4: "#3b82f6",
});

const TOOLTIP_ACTION_LABELS = Object.freeze({
    "appliquer-etat": "Appliquer l'état",
    "s-appliquer-etat": "État sur soi",
    autre: "Effet",
    booster: "Bonus",
    déplacer: "Déplacement",
    frapper: "Attaque",
    soigner: "Soin",
    invoquer: "Invocation",
    protéger: "Protection",
    retirer: "Retrait",
    "voler-caracteristiques": "Vol de stat",
});

const actionKey = computed(() => resolvePresentationActionSlug(m.value.actionSlug));

const actionTitle = computed(() => {
    const k = actionKey.value;
    if (k && TOOLTIP_ACTION_LABELS[k]) {
        return TOOLTIP_ACTION_LABELS[k];
    }
    return "Effet";
});

const charDef = computed(() => {
    const key = m.value.characteristic;
    if (!key || typeof key !== "string" || key.trim() === "") {
        return null;
    }
    return resolveDef(key.trim(), undefined, {
        sourceGroups: [...SPELL_EFFECT_CHIP_SOURCE_GROUPS],
    });
});

const elementNum = computed(() => {
    const el = m.value.element;
    if (el === null || el === undefined || el === "") {
        return null;
    }
    const n = Number(el);
    return Number.isFinite(n) ? n : null;
});

const accentHex = computed(() => {
    const summon = m.value.summonMonster;
    if (actionKey.value === "invoquer" && summon?.id != null) {
        return "#a855f7";
    }
    if (elementNum.value != null) {
        const primaries = getElementPrimaries(elementNum.value);
        const first = primaries[0];
        if (first !== undefined && ELEMENT_ACCENT_HEX[first] != null) {
            return ELEMENT_ACCENT_HEX[first];
        }
    }
    const c = charDef.value?._resolvedColor ?? charDef.value?.color;
    if (typeof c === "string" && c.trim() !== "") {
        const co = colord(c.trim());
        if (co.isValid()) {
            return co.toHex();
        }
    }
    return "#6366f1";
});

/** Style inline : accent dynamique (élément / carac.) pour `_tooltip.scss` via `var(--color)`. */
const accentStyle = computed(() => ({
    "--color": accentHex.value,
}));

/**
 * @typedef {{ kind: 'summon'|'characteristic'|'element'|'default', icon?: string, label: string, src?: string, source?: string }} VisualSpec
 */
const visual = computed(() => {
    const summon = m.value.summonMonster;
    if (actionKey.value === "invoquer" && summon?.id != null) {
        const img = summon.image != null ? String(summon.image).trim() : "";
        const name = summon.name != null ? String(summon.name) : `Monstre #${summon.id}`;
        if (/^https?:\/\//i.test(img)) {
            return { kind: "summon", label: name, src: img };
        }
        if (img !== "") {
            return { kind: "summon", label: name, source: img };
        }
        return { kind: "summon", label: name, icon: "fa-solid fa-dragon" };
    }
    if (elementNum.value != null && (actionKey.value === "frapper" || actionKey.value === "soigner")) {
        return {
            kind: "element",
            icon: getElementIcon(elementNum.value),
            label: getElementLabel(elementNum.value),
        };
    }
    const d = charDef.value;
    if (d) {
        const icon = d._resolvedIcon ?? d.icon ?? "";
        const label = d.short_name || d.name || d.key || "Caractéristique";
        if (icon) {
            return { kind: "characteristic", icon, label };
        }
    }
    if (elementNum.value != null) {
        return {
            kind: "element",
            icon: getElementIcon(elementNum.value),
            label: getElementLabel(elementNum.value),
        };
    }
    return { kind: "default", icon: "fa-solid fa-wand-magic-sparkles", label: "Effet" };
});

const mainBadgeText = computed(() => {
    const v = m.value.valueDisplay;
    if (v != null && String(v).trim() !== "") {
        return String(v).trim();
    }
    const t = m.value.textFallback;
    if (t != null && String(t).trim() !== "") {
        return String(t).trim();
    }
    return "";
});

const critBadgeText = computed(() => {
    const c = m.value.valueFormulaCrit;
    return typeof c === "string" && c.trim() !== "" ? c.trim() : "";
});

const stateLine = computed(() => {
    const n = m.value.stateName;
    if (typeof n === "string" && n.trim() !== "") {
        return n.trim();
    }
    return "";
});

const detail = computed(() => {
    const t = props.detailText != null ? String(props.detailText).trim() : "";
    return t !== "" ? t : "";
});
</script>

<template>
    <div
        class="spell-effect-chip-tooltip tooltip-floating-surface max-w-[min(20rem,calc(100vw-2rem))] text-left"
        :style="accentStyle"
    >
        <div class="flex gap-3.5">
            <div
                class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-black/35 ring-1 ring-white/10"
            >
                <Image
                    v-if="visual.kind === 'summon' && visual.src"
                    :src="visual.src"
                    :alt="visual.label"
                    fit="cover"
                    rounded="lg"
                    width="56px"
                    height="56px"
                />
                <Image
                    v-else-if="visual.kind === 'summon' && visual.source"
                    :source="visual.source"
                    :alt="visual.label"
                    fit="cover"
                    rounded="lg"
                    width="56px"
                    height="56px"
                />
                <Icon
                    v-else-if="visual.icon"
                    :source="visual.icon"
                    :alt="visual.label"
                    size="3xl"
                    class="opacity-95"
                />
            </div>
            <div class="min-w-0 flex-1 space-y-2">
                <div class="text-base font-bold leading-tight tracking-tight">
                    {{ actionTitle }}
                </div>
                <div v-if="mainBadgeText || critBadgeText" class="flex flex-wrap items-center gap-1.5">
                    <span
                        v-if="mainBadgeText"
                        class="badge badge-md border-0 bg-white/15 font-semibold tabular-nums text-inherit"
                        >{{ mainBadgeText }}</span
                    >
                    <template v-if="critBadgeText">
                        <Icon
                            source="fa-solid fa-bolt"
                            alt="Critique"
                            size="sm"
                            class="text-warning"
                        />
                        <span
                            class="badge badge-md border border-warning/50 bg-warning/15 font-semibold tabular-nums text-warning"
                            >{{ critBadgeText }}</span
                        >
                    </template>
                </div>
                <p v-if="stateLine" class="text-sm font-medium text-white/90">
                    {{ stateLine }}
                </p>
                <p
                    v-if="detail"
                    class="whitespace-pre-wrap border-t border-white/15 pt-2 text-xs leading-snug text-white/55"
                >
                    {{ detail }}
                </p>
            </div>
        </div>
    </div>
</template>
