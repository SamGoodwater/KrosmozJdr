<script setup>
/**
 * Panneau riche pour tooltips « usage sort » (icône BDD, teinte, libellé métier).
 *
 * @description
 * Utilisé par {@link SpellMinimalUsageMetaRow} pour portée modifiable, ligne de vue,
 * magie/physique, incantation, rituel.
 */
import Image from "@/Pages/Atoms/data-display/Image.vue";
import {
    spellUsageIconBackdropStyle,
    spellUsageTextColorStyle,
    spellUsageTooltipPanelStyle,
} from "@/Utils/Entity/spellUsageCharacteristicVisual";

defineProps({
    /** Sortie de {@link resolveSpellUsageCharacteristicVisual} */
    visual: { type: Object, required: true },
    /** Libellé d’état explicite (ex. « Ligne de vue obligatoire »). */
    statusText: { type: String, default: "" },
    /** Afficher le nom BDD au-dessus du statut. */
    showCharacteristicName: { type: Boolean, default: true },
    /** Affiche un glyphe V / X (vert / rouge pâles) pour les booléens. */
    showBooleanGlyph: { type: Boolean, default: false },
    /** Valeur booléenne affichée par le glyphe (true → V, false → X). */
    booleanOn: { type: Boolean, default: false },
    /** Taille de l’icône dans le tooltip (px). */
    imagePx: { type: String, default: "20" },
});
</script>

<template>
    <div
        class="max-w-xs rounded-md border-l-[3px] border-solid py-2 pl-2.5 pr-2 text-xs shadow-sm"
        :class="visual.color?.startsWith('#') ? '' : 'border-base-content/20 bg-base-200/80'"
        :style="visual.color?.startsWith('#') ? spellUsageTooltipPanelStyle(visual.color) : {}"
    >
        <div class="flex items-start gap-2">
            <span
                v-if="visual.hasIcon"
                class="inline-flex shrink-0 items-center justify-center rounded p-0.5"
                :style="spellUsageIconBackdropStyle(visual.color)"
            >
                <Image
                    :source="visual.source"
                    alt=""
                    :width="imagePx"
                    :height="imagePx"
                    fit="contain"
                    class="block shrink-0"
                />
            </span>
            <div class="min-w-0 flex-1">
                <div
                    v-if="
                        showBooleanGlyph ||
                        (showCharacteristicName && visual.characteristicName)
                    "
                    class="flex flex-wrap items-center gap-1.5 leading-tight"
                >
                    <span
                        v-if="showBooleanGlyph"
                        class="inline-flex h-4 min-w-4 shrink-0 items-center justify-center rounded border px-1 text-[10px] font-black leading-none"
                        :class="
                            booleanOn
                                ? 'border-emerald-600/20 bg-emerald-500/12 text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/15 dark:text-emerald-300'
                                : 'border-red-600/20 bg-red-500/12 text-red-700 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-300'
                        "
                        aria-hidden="true"
                    >{{ booleanOn ? "V" : "X" }}</span>
                    <span
                        v-if="showCharacteristicName && visual.characteristicName"
                        class="font-semibold"
                        :style="spellUsageTextColorStyle(visual.color)"
                    >{{ visual.characteristicName }}</span>
                    <span
                        v-else-if="showBooleanGlyph && statusText"
                        class="font-semibold text-base-content"
                    >{{ statusText }}</span>
                </div>
                <div
                    v-else-if="statusText"
                    class="font-semibold leading-snug text-base-content"
                >
                    {{ statusText }}
                </div>
                <div
                    v-if="statusText && showCharacteristicName && visual.characteristicName"
                    class="mt-0.5 font-semibold leading-snug text-base-content"
                >
                    {{ statusText }}
                </div>
                <p
                    v-if="visual.characteristicHelper"
                    class="mt-1 text-[11px] leading-snug text-base-content/75 whitespace-pre-wrap"
                >
                    {{ visual.characteristicHelper }}
                </p>
            </div>
        </div>
    </div>
</template>
