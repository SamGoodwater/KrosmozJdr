<script setup>
defineOptions({ inheritAttrs: false });

/**
 * Footer Molecule (DaisyUI + Custom Utility)
 *
 * @description
 * Molécule Footer stylée DaisyUI, conforme Atomic Design et KrosmozJDR.
 * - Props : direction (vertical/horizontal), center (bool), color, textColor, class, + customUtility
 * - Slots : default (contenu du footer), section (pour chaque colonne/nav), logo (pour le logo/aside), copyright
 * - mergeClasses pour les classes DaisyUI explicites + utilitaires custom
 * - Accessibilité (role, aria-label, etc.)
 *
 * @see https://daisyui.com/components/footer/
 *
 * @example
 * <Footer direction="horizontal" color="bg-neutral" textColor="text-neutral-content">
 *   <template #logo>
 *     <Logo />
 *     <span>Mon projet</span>
 *   </template>
 *   <template #section>
 *     <nav>
 *       <h6 class="footer-title">Services</h6>
 *       <a class="link link-hover">Branding</a>
 *       <a class="link link-hover">Design</a>
 *     </nav>
 *   </template>
 *   <template #copyright>
 *     <span>&copy; 2024 Mon projet</span>
 *   </template>
 * </Footer>
 *
 * @props {String} direction - Direction du footer ('vertical', 'horizontal'), défaut 'vertical'
 * @props {Boolean} center - Centre le contenu (footer-center)
 * @props {String} color - Couleur de fond DaisyUI (bg-neutral, bg-base-200, etc.)
 * @props {String} textColor - Couleur du texte DaisyUI (text-neutral-content, etc.)
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @props {String|Object} id, ariaLabel, role, tabindex, class - hérités de commonProps
 * @slot default - Contenu custom du footer (remplace tout)
 * @slot logo - Section logo/aside (optionnelle)
 * @slot section - Section nav/colonne (répétable)
 * @slot copyright - Section copyright (optionnelle)
 */
import { computed } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    direction: {
        type: String,
        default: 'vertical',
        validator: v => ['vertical', 'horizontal'].includes(v),
    },
    center: { type: Boolean, default: false },
    color: { type: String, default: '' },
    textColor: { type: String, default: 'text-neutral-content' },
});

const moleculeClasses = computed(() =>
    mergeClasses(
        [
            'mx-auto flex',
            props.direction === 'horizontal' ? 'flex-row' : 'flex-col',
            props.center ? 'items-center justify-center' : '',
            props.color,
            props.textColor,
            'p-10 sm:p-5 gap-2',
            props.class
        ],
        getCustomUtilityClasses(props)
    )
);
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <footer :class="moleculeClasses" v-bind="attrs" v-on="$attrs" role="contentinfo">
        <template v-if="$slots.default">
            <slot />
        </template>
        <template v-else>
            <div class="flex gap-8 sm:gap-4 items-center justify-center">
                <aside v-if="$slots.logo" class="footer-logo">
                    <slot name="logo" />
                </aside>
                <div class="flex flex-wrap gap-8 flex-1">
                    <slot name="section" />
                </div>
            </div>
            <div v-if="$slots.copyright" class="text-xs opacity-70">
                <slot name="copyright" />
            </div>
        </template>
    </footer>
</template>

<style scoped></style>
