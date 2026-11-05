<script setup>
/**
 * Champ d’édition du filtre d’icône d’un sort.
 *
 * Choix : Image d’origine / Beige / Violet / Personnalisé (+ curseurs).
 * Suggestion si sort sans élément et image colorée (canvas, non bloquant).
 *
 * @example
 * <SpellImageFilterField v-model="form.image_filter" :image-url="form.image" :element="form.element" />
 */
import { computed, ref, watch } from "vue";
import {
    SPELL_IMAGE_FILTER_PRESETS,
    averageSaturationFromImageData,
    paramsToCssFilter,
    resolveSpellImageFilterParams,
    shouldHintBeigeFilter,
    spellLooksElementNeutral,
} from "@/Utils/Entity/spellImageFilter";
import { resolveEntityImageUrl } from "@/Utils/entity/entityThumb";

const props = defineProps({
    modelValue: { type: Object, default: null },
    imageUrl: { type: String, default: "" },
    element: { type: [Number, String, null], default: null },
    disabled: { type: Boolean, default: false },
});

const emit = defineEmits(["update:modelValue"]);

const MODE_OPTIONS = [
    { id: "none", label: "Image d’origine" },
    { id: "beige", label: "Beige" },
    { id: "violet", label: "Violet" },
    { id: "custom", label: "Personnalisé" },
];

const SLIDERS = [
    { key: "grayscale", label: "Niveaux de gris", min: 0, max: 2, step: 0.05 },
    { key: "sepia", label: "Sépia", min: 0, max: 2, step: 0.05 },
    { key: "hue", label: "Teinte (°)", min: 0, max: 360, step: 1 },
    { key: "saturate", label: "Saturation", min: 0, max: 3, step: 0.05 },
    { key: "brightness", label: "Luminosité", min: 0, max: 2, step: 0.05 },
    { key: "contrast", label: "Contraste", min: 0, max: 2, step: 0.05 },
];

const mode = computed(() => {
    const v = props.modelValue;
    if (!v || typeof v !== "object") return "none";
    const p = v.preset != null ? String(v.preset) : "";
    if (p === "beige" || p === "violet" || p === "custom") return p;
    return "none";
});

const customParams = computed(() => {
    if (mode.value === "custom") {
        return (
            resolveSpellImageFilterParams(props.modelValue) || {
                ...SPELL_IMAGE_FILTER_PRESETS.beige,
            }
        );
    }
    return { ...SPELL_IMAGE_FILTER_PRESETS.beige };
});

const previewFilter = computed(() => {
    if (mode.value === "none") return "";
    if (mode.value === "custom") return paramsToCssFilter(customParams.value);
    return paramsToCssFilter(SPELL_IMAGE_FILTER_PRESETS[mode.value]);
});

const previewStyle = computed(() =>
    previewFilter.value ? { filter: previewFilter.value } : undefined,
);

const resolvedImageUrl = computed(() => {
    const raw = props.imageUrl != null ? String(props.imageUrl).trim() : "";
    return raw ? resolveEntityImageUrl(raw) : "";
});

const saturationHint = ref(false);
const measuring = ref(false);

async function measureSaturation() {
    saturationHint.value = false;
    if (!resolvedImageUrl.value) return;
    if (!spellLooksElementNeutral({ element: props.element })) return;
    if (mode.value !== "none") return;
    measuring.value = true;
    try {
        const img = new Image();
        img.crossOrigin = "anonymous";
        const loaded = new Promise((resolve, reject) => {
            img.onload = () => resolve();
            img.onerror = () => reject(new Error("load"));
        });
        img.src = resolvedImageUrl.value;
        await loaded;
        const canvas = document.createElement("canvas");
        const w = Math.min(64, img.naturalWidth || 64);
        const h = Math.min(64, img.naturalHeight || 64);
        canvas.width = w;
        canvas.height = h;
        const ctx = canvas.getContext("2d", { willReadFrequently: true });
        if (!ctx) return;
        ctx.drawImage(img, 0, 0, w, h);
        let data;
        try {
            data = ctx.getImageData(0, 0, w, h);
        } catch {
            return;
        }
        const sat = averageSaturationFromImageData(data);
        saturationHint.value = shouldHintBeigeFilter(sat);
    } catch {
        saturationHint.value = false;
    } finally {
        measuring.value = false;
    }
}

watch(
    () => [resolvedImageUrl.value, props.element, mode.value],
    () => {
        measureSaturation();
    },
    { immediate: true },
);

/**
 * @param {"none"|"beige"|"violet"|"custom"} next
 */
function setMode(next) {
    if (props.disabled) return;
    if (next === "none") {
        emit("update:modelValue", null);
        return;
    }
    if (next === "beige" || next === "violet") {
        emit("update:modelValue", { preset: next });
        return;
    }
    const base =
        resolveSpellImageFilterParams(props.modelValue) || {
            ...SPELL_IMAGE_FILTER_PRESETS.beige,
        };
    emit("update:modelValue", { preset: "custom", ...base });
}

/**
 * @param {string} key
 * @param {string|number} raw
 */
function updateCustom(key, raw) {
    if (props.disabled) return;
    const next = { preset: "custom", ...customParams.value, [key]: Number(raw) };
    emit("update:modelValue", next);
}

function applyBeigeHint() {
    setMode("beige");
}

function btnClass(id) {
    const active = mode.value === id;
    return [
        "btn btn-sm",
        active ? "btn-primary" : "btn-ghost border border-base-300",
    ];
}
</script>

<template>
    <div class="spell-image-filter-field space-y-3" data-cy="spell-image-filter-field">
        <div class="flex flex-wrap gap-1.5">
            <button
                v-for="opt in MODE_OPTIONS"
                :key="opt.id"
                type="button"
                :class="btnClass(opt.id)"
                :disabled="disabled"
                :data-cy="`spell-image-filter-${opt.id}`"
                @click="setMode(opt.id)"
            >
                {{ opt.label }}
            </button>
        </div>

        <div
            v-if="saturationHint && mode === 'none'"
            class="flex flex-wrap items-center gap-2 rounded-box border border-warning/40 bg-warning/10 px-3 py-2 text-sm"
            data-cy="spell-image-filter-hint"
        >
            <span class="min-w-0 flex-1 text-base-content/90">
                Image colorée pour un sort basique : appliquer le filtre Beige ?
            </span>
            <button
                type="button"
                class="btn btn-warning btn-sm"
                :disabled="disabled || measuring"
                data-cy="spell-image-filter-hint-apply"
                @click="applyBeigeHint"
            >
                Appliquer Beige
            </button>
        </div>

        <div v-if="resolvedImageUrl" class="flex flex-wrap items-end gap-4">
            <div class="space-y-1">
                <p class="text-xs text-base-content/60">Origine</p>
                <img
                    :src="resolvedImageUrl"
                    alt="Icône d’origine"
                    class="h-16 w-16 rounded-box bg-base-200 object-contain"
                />
            </div>
            <div class="space-y-1">
                <p class="text-xs text-base-content/60">Aperçu</p>
                <img
                    :src="resolvedImageUrl"
                    alt="Aperçu filtré"
                    class="h-16 w-16 rounded-box bg-base-200 object-contain"
                    :style="previewStyle"
                    data-cy="spell-image-filter-preview"
                />
            </div>
        </div>

        <div v-if="mode === 'custom'" class="grid gap-3 sm:grid-cols-2" data-cy="spell-image-filter-custom">
            <label
                v-for="slider in SLIDERS"
                :key="slider.key"
                class="flex flex-col gap-1 text-xs"
            >
                <span class="flex justify-between gap-2">
                    <span>{{ slider.label }}</span>
                    <span class="tabular-nums opacity-70">{{ customParams[slider.key] }}</span>
                </span>
                <input
                    type="range"
                    class="range range-xs range-primary"
                    :min="slider.min"
                    :max="slider.max"
                    :step="slider.step"
                    :value="customParams[slider.key]"
                    :disabled="disabled"
                    @input="updateCustom(slider.key, $event.target.value)"
                />
            </label>
        </div>
    </div>
</template>
