<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * List Atom (DaisyUI)
 *
 * @description
 * Composant atomique List conforme DaisyUI (v5.x) et Atomic Design.
 * - Rend un <ul class="list"> ou <div class="list"> stylé DaisyUI
 * - Slot #header : en-tête de la liste (optionnel)
 * - Slot #footer : pied de la liste (optionnel)
 * - Slot par défaut : éléments de la liste (ListRow ou <li>/<div>)
 * - Props DaisyUI : toutes les classes explicites (voir doc DaisyUI)
 * - Utilitaires : shadow, rounded, backdrop, opacity (via customUtility)
 * - Props custom : as, class
 * - Accessibilité : ariaLabel, role, tabindex, id, tooltip, etc.
 * - Tooltip intégré (hors Tooltip lui-même)
 *
 * @see https://daisyui.com/components/list/
 * @version DaisyUI v5.x
 *
 * @example
 * <List as="ul" shadow="md" rounded="box">
 *   <template #header>Mes titres</template>
 *   <ListRow>...</ListRow>
 *   <template #footer>Fin de liste</template>
 * </List>
 *
 * @props {String} as - Balise racine ('ul' ou 'div', défaut 'ul')
 * @props {String} class - Classes custom supplémentaires
 * @props {String} shadow, rounded, backdrop, opacity - utilitaires custom
 * @props {String} id, ariaLabel, role, tabindex, tooltip, tooltip_placement - accessibilité
 * @slot header - En-tête de la liste (optionnel)
 * @slot footer - Pied de la liste (optionnel)
 * @slot default - Éléments de la liste (ListRow ou <li>/<div>)
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 */
import { computed, h, resolveDynamicComponent } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    as: {
        type: String,
        default: 'ul',
        validator: v => ['ul', 'div'].includes(v),
    },
    class: { type: String, default: '' },
});

const atomClasses = computed(() => {
    return mergeClasses(
        ['list', 'bg-base-100', 'shadow-md', 'rounded-md'],
        getCustomUtilityClasses(props),
        props.class
    );
});

const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <component :is="props.as" :class="atomClasses" v-bind="attrs" v-on="$attrs">
            <slot />
        </component>
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped></style>
