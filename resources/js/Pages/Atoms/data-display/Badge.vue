<script setup>
/**
 * Badge Atom (DaisyUI)
 *
 * @description
 * Composant atomique Badge conforme DaisyUI (v5.x) et Atomic Design.
 * - Slot par défaut : contenu du badge (texte, nombre, icône, etc.)
 * - Prop content : texte simple à afficher (prioritaire sur slot par défaut)
 * - Slot #content : contenu HTML complexe (prioritaire sur prop content)
 * - Props DaisyUI : color, size, variant (outline, dash, soft, ghost)
 * - Props utilitaires custom : shadow, backdrop, opacity
 * - Toutes les classes DaisyUI sont écrites en toutes lettres
 * - Accessibilité renforcée (role, aria, etc.)
 *
 * @see https://daisyui.com/components/badge/
 * @version DaisyUI v5.x
 *
 * @example
 * <Badge color="primary" content="Nouveau" />
 * <Badge color="error" size="lg" variant="outline">Erreur</Badge>
 * <Badge color="info" size="xs" variant="soft"><template #content><b>Info</b> <i>badge</i></template></Badge>
 *
 * @props {String} content - Texte simple à afficher dans le badge (optionnel, prioritaire sur slot)
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error')
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl'), défaut ''
 * @props {String} variant - Style DaisyUI ('', 'outline', 'dash', 'soft', 'ghost'), défaut ''
 * @props {String|Object} id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot default - Contenu du badge (fallback)
 * @slot content - Contenu HTML complexe prioritaire
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 */
import { computed } from "vue"
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { colorList, sizeXlList, variantList } from '@/Pages/Atoms/atomMap';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    content: { type: String, default: '' },
    color: {
        type: String,
        default: '',
        validator: v => colorList.includes(v),
    },
    size: {
        type: String,
        default: '',
        validator: v => sizeXlList.includes(v),
    },
    variant: {
        type: String,
        default: '',
        validator: v => variantList.includes(v),
    },
    outline: {
        type: Boolean,
        default: false
    }
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'badge',
            props.color === 'neutral' && 'badge-neutral',
            props.color === 'primary' && 'badge-primary',
            props.color === 'secondary' && 'badge-secondary',
            props.color === 'accent' && 'badge-accent',
            props.color === 'info' && 'badge-info',
            props.color === 'success' && 'badge-success',
            props.color === 'warning' && 'badge-warning',
            props.color === 'error' && 'badge-error',
            props.size === 'xs' && 'badge-xs',
            props.size === 'sm' && 'badge-sm',
            props.size === 'md' && 'badge-md',
            props.size === 'lg' && 'badge-lg',
            props.size === 'xl' && 'badge-xl',
            props.variant === 'outline' && 'badge-outline',
            props.variant === 'dash' && 'badge-dash',
            props.variant === 'soft' && 'badge-soft',
            props.variant === 'ghost' && 'badge-ghost',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <span :class="atomClasses" v-bind="attrs" v-on="$attrs">
        <span v-if="content && !$slots.default">{{ content }}</span>
        <slot name="content" v-else />
    </span>
</template>

<style scoped></style>
