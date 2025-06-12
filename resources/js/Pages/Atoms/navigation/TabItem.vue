<script setup>
defineOptions({ inheritAttrs: false });

/**
 * TabItem Atom (DaisyUI + Custom Utility)
 *
 * @description
 * Atomique item de tab stylé DaisyUI, conforme Atomic Design et KrosmozJDR.
 * - Rend un <a role="tab"> ou <button role="tab"> stylé DaisyUI
 * - Props : active, disabled, icon (string), label (string), color, size, customUtility, accessibilité, etc.
 * - Slot icon prioritaire sur prop icon, slot label prioritaire sur prop label, slot default pour le contenu du tab (tab-content)
 * - Slot nommé 'title' pour le titre du tab (label + icône)
 * - mergeClasses pour les classes DaisyUI explicites (tab, tab-active, tab-disabled, etc.)
 * - Utilise l'atom Icon pour l'icône si besoin
 * - getCommonAttrs pour l'accessibilité
 *
 * @see https://daisyui.com/components/tab/
 *
 * @example
 * <TabItem active icon="fa-user" label="Profil">Contenu du tab</TabItem>
 * <TabItem><template #title><Icon source="fa-cog" /> Paramètres</template>Contenu</TabItem>
 *
 * @props {Boolean} active - Met l'item en état actif
 * @props {Boolean} disabled - Désactive l'item
 * @props {String} icon - Nom logique ou chemin de l'icône (optionnel, sinon slot #icon)
 * @props {String} label - Label du tab (optionnel, sinon slot #label)
 * @props {String} color - Couleur DaisyUI (optionnel)
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @props {String|Object} id, ariaLabel, role, tabindex, class - hérités de commonProps
 * @slot title - Slot pour le titre du tab (icône + label)
 * @slot icon - Slot pour l'icône (prioritaire sur prop icon)
 * @slot label - Slot pour le label (prioritaire sur prop label)
 * @slot default - Contenu du tab (tab-content)
 */
import { computed } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { sizeXlList } from '@/Pages/Atoms/atomMap';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    active: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    icon: { type: String, default: '' },
    label: { type: String, default: '' },
    color: { type: String, default: '' },
    size: {
        type: String,
        default: '',
        validator: v => sizeXlList.includes(v),
    },
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'tab',
            props.active && 'tab-active',
            props.disabled && 'tab-disabled',
            props.size === 'xs' && 'tab-xs',
            props.size === 'sm' && 'tab-sm',
            props.size === 'md' && 'tab-md',
            props.size === 'lg' && 'tab-lg',
            props.size === 'xl' && 'tab-xl',
            props.class
        ],
        getCustomUtilityClasses(props)
    )
);
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <a role="tab" :class="atomClasses" v-bind="attrs" v-on="$attrs" :aria-selected="active" :aria-disabled="disabled">
        <template v-if="$slots.title">
            <slot name="title" />
        </template>
        <template v-else>
            <span v-if="$slots.icon || icon" class="mr-2 flex items-center">
                <slot name="icon">
                    <Icon v-if="icon" :source="icon" :alt="'icon'" :size="size || 'md'" :disabled="props.disabled" />
                </slot>
            </span>
            <span v-if="$slots.label || label">
                <slot name="label">{{ label }}</slot>
            </span>
        </template>
        <slot />
    </a>
</template>

<style scoped></style>
