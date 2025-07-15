<script setup>
defineOptions({ inheritAttrs: false });

/**
 * MenuItem Atom (DaisyUI + Custom Utility + Route)
 *
 * @description
 * Atomique item de menu stylé DaisyUI, conforme Atomic Design et KrosmozJDR.
 * - Rend un <li><a>...</a></li> stylé DaisyUI, ou un <Route> si la prop 'route' est fournie
 * - Props : active, disabled, icon (string), color, size, route, customUtility, accessibilité, etc.
 * - Slot par défaut pour le contenu du menu
 * - Slot icon prioritaire sur la prop icon
 * - mergeClasses pour les classes DaisyUI explicites (menu-active, menu-disabled, etc.)
 * - Utilisation de l'atom Icon pour l'icône (comme Stat)
 *
 * @see https://daisyui.com/components/menu/
 *
 * @example
 * <MenuItem icon="fa-user" pack="solid" active route="user.profile">Profil</MenuItem>
 * <MenuItem disabled>Déconnexion</MenuItem>
 *
 * @props {Boolean} active - Met l'item en état actif
 * @props {Boolean} disabled - Désactive l'item
 * @props {String} icon - Nom logique ou chemin de l'icône (optionnel, sinon slot #icon)
 * @props {String} pack - Pack FontAwesome (solid, regular, brands, duotone)
 * @props {String} color - Couleur DaisyUI (optionnel)
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} route - Nom de la route Inertia/Laravel (optionnel)
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @props {String|Object} id, ariaLabel, role, tabindex, class - hérités de commonProps
 * @slot icon - Slot pour l'icône (prioritaire sur prop icon)
 * @slot default - Contenu du menu
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import RouteAtom from "@/Pages/Atoms/action/Route.vue";
import {
    getCommonProps,
    getCommonAttrs,
    getCustomUtilityProps,
    getCustomUtilityClasses,
    mergeClasses,
} from "@/Utils/atomic-design/uiHelper";
import { sizeXlList } from "@/Pages/Atoms/atomMap";

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    active: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    icon: { type: String, default: "" },
    pack: {
        type: String,
        default: "",
        validator: (v) => ["solid", "regular", "brands", "duotone"].includes(v),
    },
    color: { type: String, default: "" },
    size: {
        type: String,
        default: "",
        validator: (v) => sizeXlList.includes(v),
    },
    route: { type: String, default: "" },
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            props.active && "menu-active",
            props.disabled && "menu-disabled",
            props.size === "xs" && "menu-xs",
            props.size === "sm" && "menu-sm",
            props.size === "md" && "menu-md",
            props.size === "lg" && "menu-lg",
            props.size === "xl" && "menu-xl",
            props.class,
        ],
        getCustomUtilityClasses(props),
    ),
);
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <li :class="atomClasses" v-bind="attrs" v-on="$attrs">
        <RouteAtom
            v-if="route"
            :route="route"
            :disabled="props.disabled"
            :aria-label="props.ariaLabel"
            :tabindex="props.tabindex"
            :role="props.role"
            :id="props.id"
            :class="'flex items-center w-full'"
        >
            <span v-if="$slots.icon || icon" class="mr-2 flex items-center">
                <slot name="icon">
                    <Icon
                        v-if="icon"
                        :source="icon"
                        :pack="pack"
                        :alt="'icon'"
                        :size="size || 'md'"
                        :disabled="props.disabled"
                    />
                </slot>
            </span>
            <slot />
        </RouteAtom>
        <a
            v-else
            :tabindex="props.tabindex"
            :aria-disabled="props.disabled"
            :class="{
                'pointer-events-none': props.disabled,
                'flex items-center w-full': true,
            }"
        >
            <span v-if="$slots.icon || icon" class="mr-2 flex items-center">
                <slot name="icon">
                    <Icon
                        v-if="icon"
                        :source="icon"
                        :pack="pack"
                        :alt="'icon'"
                        :size="size || 'md'"
                        :disabled="props.disabled"
                    />
                </slot>
            </span>
            <slot />
        </a>
    </li>
</template>

<style scoped></style>
