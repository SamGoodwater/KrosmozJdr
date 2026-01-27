<script setup>
/**
 * ResponsiveActionButton Atom (Glass)
 *
 * @description
 * Bouton d’action responsive :
 * - En lg+ : bouton "classique" (texte à gauche, icône à droite)
 * - En <lg : bouton icône seule, et au hover/focus le bouton "s’étend" en overlay (sans reflow)
 *
 * Contraintes:
 * - Un seul style (glass) via l’Atom `Btn`
 * - Pas de classes Tailwind dynamiques (classes explicites)
 *
 * @example
 * <ResponsiveActionButton
 *   label="Ajouter"
 *   icon="fa-solid fa-plus"
 *   color="primary"
 *   size="sm"
 *   @click="add"
 * />
 *
 * @props {String} label - Texte du bouton (fallback si slot default absent)
 * @props {String} icon - Source d’icône (FontAwesome ou chemin), obligatoire
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', ...)
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} type - Type HTML ('button', 'submit', 'reset', 'radio', 'checkbox')
 * @props {Boolean} disabled - Désactive le bouton
 * @props {String} id, ariaLabel, role, tabindex - hérités de commonProps
 *
 * @slot default - Contenu texte (prioritaire sur `label`)
 * @slot icon - Contenu icône (prioritaire sur `icon`)
 */
import { computed, useSlots } from "vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import { colorList, sizeList } from "@/Pages/Atoms/atomMap";
import { typeList } from "@/Pages/Atoms/action/actionMap";
import { getCommonProps } from "@/Utils/atomic-design/uiHelper";

const props = defineProps({
    ...getCommonProps(),
    label: {
        type: String,
        default: "",
    },
    icon: {
        type: String,
        required: true,
    },
    color: {
        type: String,
        default: "",
        validator: (v) => colorList.includes(v),
    },
    size: {
        type: String,
        default: "",
        validator: (v) => sizeList.includes(v),
    },
    type: {
        type: String,
        default: "button",
        validator: (v) => typeList.includes(v),
    },
});

const emit = defineEmits(["click"]);

const slots = useSlots();
const hasLabel = computed(() => !!props.label || !!slots.default);

function onClick(event) {
    if (props.disabled) return;
    emit("click", event);
}
</script>

<template>
    <span class="group relative inline-flex items-center">
        <!-- Bouton principal :
             - <lg : icône seule
             - lg+ : texte + icône
        -->
        <Btn
            :id="id"
            :aria-label="ariaLabel || label"
            :role="role"
            :tabindex="tabindex"
            :disabled="disabled"
            :type="type"
            :color="color"
            :size="size"
            variant="glass"
            animation="glass"
            class="relative gap-2"
            @click="onClick"
        >
            <!-- Desktop (lg+) : texte à gauche -->
            <span class="hidden lg:inline">
                <slot>{{ label }}</slot>
            </span>

            <!-- Icône (toujours visible) -->
            <span class="inline-flex items-center justify-center">
                <slot name="icon">
                    <Icon :source="icon" :alt="label || ariaLabel || 'Action'" :size="size || 'md'" />
                </slot>
            </span>
        </Btn>

        <!-- Overlay (uniquement <lg) : texte + icône, sans reflow -->
        <span
            v-if="hasLabel && !disabled"
            class="lg:hidden absolute right-0 top-1/2 -translate-y-1/2 z-50 opacity-0 pointer-events-none origin-right scale-90 translate-x-1 transition-[opacity,transform] duration-200 ease-out group-hover:opacity-100 group-hover:scale-100 group-hover:translate-x-0 group-hover:pointer-events-auto group-focus-within:opacity-100 group-focus-within:scale-100 group-focus-within:translate-x-0 group-focus-within:pointer-events-auto"
        >
            <Btn
                :id="id ? `${id}--overlay` : ''"
                aria-label=""
                role="presentation"
                :tabindex="-1"
                :disabled="disabled"
                :type="type"
                :color="color"
                :size="size"
                variant="glass"
                animation="glass"
                class="gap-2 whitespace-nowrap"
                @click="onClick"
            >
                <span>
                    <slot>{{ label }}</slot>
                </span>
                <span class="inline-flex items-center justify-center">
                    <slot name="icon">
                        <Icon :source="icon" :alt="label || ariaLabel || 'Action'" :size="size || 'md'" />
                    </slot>
                </span>
            </Btn>
        </span>
    </span>
</template>

