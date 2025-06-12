<script setup>
defineOptions({ inheritAttrs: false });

/**
 * DockItem Atom (DaisyUI + Custom Utility + Route)
 *
 * @description
 * Atomique item de dock stylé DaisyUI, conforme Atomic Design et KrosmozJDR.
 * - Rend un <button> ou <Route> stylé DaisyUI (dock)
 * - Props : active, disabled, icon (string), label (string), route (string), color, size, customUtility, accessibilité, etc.
 * - Slot icon prioritaire sur prop icon, slot label prioritaire sur prop label, slot default pour contenu custom
 * - mergeClasses pour les classes DaisyUI explicites (dock-active, dock-label, etc.)
 * - Utilise l'atom Icon pour l'icône
 * - getCommonAttrs pour l'accessibilité
 *
 * @see https://daisyui.com/components/dock/
 *
 * @example
 * <DockItem icon="fa-home" label="Accueil" active route="home" />
 * <DockItem icon="fa-cog" label="Paramètres" />
 *
 * @props {Boolean} active - Met l'item en état actif
 * @props {Boolean} disabled - Désactive l'item
 * @props {String} icon - Nom logique ou chemin de l'icône (optionnel, sinon slot #icon)
 * @props {String} label - Label du dock (optionnel, sinon slot #label)
 * @props {String} route - Nom de la route Inertia/Laravel (optionnel)
 * @props {String} color - Couleur DaisyUI (optionnel)
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @props {String|Object} id, ariaLabel, role, tabindex, class - hérités de commonProps
 * @slot icon - Slot pour l'icône (prioritaire sur prop icon)
 * @slot label - Slot pour le label (prioritaire sur prop label)
 * @slot default - Contenu custom
 */
import { computed } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import RouteAtom from '@/Pages/Atoms/action/Route.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { sizeXlList } from '@/Pages/Atoms/atomMap';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    active: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    icon: { type: String, default: '' },
    label: { type: String, default: '' },
    route: { type: String, default: '' },
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
            props.active && 'dock-active',
            props.size === 'xs' && 'dock-xs',
            props.size === 'sm' && 'dock-sm',
            props.size === 'md' && 'dock-md',
            props.size === 'lg' && 'dock-lg',
            props.size === 'xl' && 'dock-xl',
            props.class
        ],
        getCustomUtilityClasses(props)
    )
);
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <li :class="atomClasses" v-bind="attrs" v-on="$attrs">
        <RouteAtom v-if="route" :route="route" :disabled="props.disabled" :aria-label="props.ariaLabel"
            :tabindex="props.tabindex" :role="props.role" :id="props.id" :class="'flex flex-col items-center w-full'">
            <span v-if="$slots.icon || icon" class="mb-1 flex items-center justify-center">
                <slot name="icon">
                    <Icon v-if="icon" :source="icon" :alt="'icon'" :size="size || 'md'" :disabled="props.disabled" />
                </slot>
            </span>
            <span v-if="$slots.label || label" class="dock-label">
                <slot name="label">{{ label }}</slot>
            </span>
            <slot />
        </RouteAtom>
        <button v-else :disabled="props.disabled" :tabindex="props.tabindex" :aria-label="props.ariaLabel"
            :class="'flex flex-col items-center w-full'">
            <span v-if="$slots.icon || icon" class="mb-1 flex items-center justify-center">
                <slot name="icon">
                    <Icon v-if="icon" :source="icon" :alt="'icon'" :size="size || 'md'" :disabled="props.disabled" />
                </slot>
            </span>
            <span v-if="$slots.label || label" class="dock-label">
                <slot name="label">{{ label }}</slot>
            </span>
            <slot />
        </button>
    </li>
</template>

<style scoped></style>
