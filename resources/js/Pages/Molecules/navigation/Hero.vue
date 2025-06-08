<script setup>
defineOptions({ inheritAttrs: false });

/**
 * Hero Molecule (DaisyUI + Custom Utility)
 *
 * @description
 * Molécule Hero stylée DaisyUI, conforme Atomic Design et KrosmozJDR.
 * - Props : bg (couleur de fond), minHeight (taille min), overlay (bool), overlayColor, overlayOpacity, textColor, class, + customUtility
 * - Slots : default (contenu principal), content (hero-content), overlay (overlay custom), figure (image/illustration), title, description, actions
 * - mergeClasses pour les classes DaisyUI explicites + utilitaires custom
 * - Accessibilité (role, aria-label, etc.)
 *
 * @see https://daisyui.com/components/hero/
 *
 * @example
 * <Hero bg="bg-base-200" minHeight="min-h-screen">
 *   <template #figure>
 *     <img src="..." class="max-w-sm rounded-lg shadow-2xl" />
 *   </template>
 *   <template #content>
 *     <h1 class="text-5xl font-bold">Hello there</h1>
 *     <p class="py-6">Description…</p>
 *     <Btn color="primary">Get Started</Btn>
 *   </template>
 * </Hero>
 *
 * @props {String} bg - Couleur de fond DaisyUI (bg-base-200, etc.)
 * @props {String} minHeight - Hauteur min (min-h-screen, min-h-[400px], etc.)
 * @props {Boolean} overlay - Affiche un overlay (défaut false)
 * @props {String} overlayColor - Couleur de l'overlay (bg-black, bg-base-100, etc.)
 * @props {String} overlayOpacity - Opacité de l'overlay (bg-opacity-50, etc.)
 * @props {String} textColor - Couleur du texte (text-neutral-content, etc.)
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @props {String|Object} id, ariaLabel, role, tabindex, class - hérités de commonProps
 * @slot default - Contenu custom du hero (remplace tout)
 * @slot content - Contenu principal (hero-content)
 * @slot overlay - Overlay custom (remplace l'overlay par défaut)
 * @slot figure - Image/illustration (optionnelle)
 * @slot title - Titre (optionnel)
 * @slot description - Description (optionnelle)
 * @slot actions - Actions (boutons, etc.)
 */
import { computed } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    bg: { type: String, default: 'bg-base-200' },
    minHeight: { type: String, default: 'min-h-screen' },
    overlay: { type: Boolean, default: false },
    overlayColor: { type: String, default: 'bg-black' },
    overlayOpacity: { type: String, default: 'bg-opacity-40' },
    textColor: { type: String, default: '' },
});

const moleculeClasses = computed(() =>
    mergeClasses(
        [
            'hero',
            props.bg,
            props.minHeight,
            props.textColor,
            props.class
        ],
        getCustomUtilityClasses(props)
    )
);
const contentClasses = computed(() =>
    mergeClasses([
        'hero-content',
        props.textColor,
        'text-center',
    ])
);
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <section :class="moleculeClasses" v-bind="attrs" v-on="$attrs" role="region">
        <template v-if="props.overlay">
            <div v-if="$slots.overlay" class="hero-overlay">
                <slot name="overlay" />
            </div>
            <div v-else class="hero-overlay" :class="[props.overlayColor, props.overlayOpacity]" />
        </template>
        <template v-if="$slots.default">
            <slot />
        </template>
        <template v-else>
            <div :class="contentClasses">
                <div v-if="$slots.figure" class="mr-8">
                    <slot name="figure" />
                </div>
                <div>
                    <slot name="title" />
                    <slot name="description" />
                    <slot name="content" />
                    <div v-if="$slots.actions" class="mt-6">
                        <slot name="actions" />
                    </div>
                </div>
            </div>
        </template>
    </section>
</template>

<style scoped></style>
