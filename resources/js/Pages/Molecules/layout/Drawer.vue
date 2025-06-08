<script setup>
defineOptions({ inheritAttrs: false });

/**
 * Drawer Molecule (DaisyUI + Custom Utility)
 *
 * @description
 * Molécule Drawer stylée DaisyUI, conforme Atomic Design et KrosmozJDR.
 * - Props : modelValue (v-model), side ('start'|'end'), overlay (bool), id (string), width (string), closeOnOverlay (bool), ariaLabel, class, + customUtility
 * - Slots : default (drawer-content), sidebar (drawer-side), toggle (bouton d'ouverture)
 * - Events : update:modelValue, open, close
 * - Structure DaisyUI : .drawer, .drawer-toggle, .drawer-content, .drawer-side, .drawer-overlay, .drawer-end, .drawer-open
 * - mergeClasses pour les classes DaisyUI explicites + utilitaires custom
 * - Accessibilité (aria-label, role, tabindex)
 *
 * @see https://daisyui.com/components/drawer/
 *
 * @example
 * <Drawer v-model="drawerOpen" side="end" width="w-96">
 *   <template #toggle>
 *     <Btn @click="drawerOpen = true">Ouvrir le menu</Btn>
 *   </template>
 *   <template #sidebar>
 *     <ul class="menu p-4 w-80 bg-base-200">
 *       <li><a>Item 1</a></li>
 *       <li><a>Item 2</a></li>
 *     </ul>
 *   </template>
 *   <template #default>
 *     <div>Contenu principal de la page</div>
 *   </template>
 * </Drawer>
 *
 * @props {Boolean} modelValue - Contrôle l'ouverture du drawer (v-model)
 * @props {String} side - Côté du drawer ('start'|'end'), défaut 'start'
 * @props {Boolean} overlay - Affiche l'overlay (défaut true)
 * @props {String} id - Identifiant unique (pour l'input/label)
 * @props {String} width - Largeur custom (ex: 'w-80', 'w-96')
 * @props {Boolean} closeOnOverlay - Ferme au clic sur l'overlay (défaut true)
 * @props {String} ariaLabel - Accessibilité
 * @props {String} class - Classes custom
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @slot default - Contenu principal (drawer-content)
 * @slot sidebar - Contenu du drawer (menu, navigation, etc.)
 * @slot toggle - Bouton d'ouverture custom
 * @event update:modelValue - v-model
 * @event open - Drawer ouvert
 * @event close - Drawer fermé
 */
import { computed, ref, watch } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const emit = defineEmits(['update:modelValue', 'open', 'close']);
const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    modelValue: { type: Boolean, default: false },
    side: { type: String, default: 'start', validator: v => ['start', 'end'].includes(v) },
    overlay: { type: Boolean, default: true },
    width: { type: String, default: 'w-80' },
    closeOnOverlay: { type: Boolean, default: true },
});

const drawerId = computed(() => props.id || `drawer-${Math.random().toString(36).substr(2, 9)}`);
const isOpen = computed({
    get: () => props.modelValue,
    set: v => emit('update:modelValue', v),
});

watch(() => props.modelValue, (val) => {
    if (val) emit('open');
    else emit('close');
});

const moleculeClasses = computed(() =>
    mergeClasses(
        [
            'drawer',
            props.side === 'end' && 'drawer-end',
            props.modelValue && 'drawer-open',
            props.class
        ],
        getCustomUtilityClasses(props)
    )
);
const attrs = computed(() => getCommonAttrs(props));

const sidebarClasses = computed(() =>
    mergeClasses([
        'drawer-side',
        props.width,
    ])
);
</script>

<template>
    <div :class="moleculeClasses" v-bind="attrs" v-on="$attrs">
        <!-- Hidden checkbox pour DaisyUI (contrôle via v-model) -->
        <input :id="drawerId" type="checkbox" class="drawer-toggle" :checked="isOpen" @change="isOpen = !isOpen"
            style="display:none;" />
        <!-- Toggle button (optionnel) -->
        <slot name="toggle" />
        <!-- Contenu principal -->
        <div class="drawer-content">
            <slot />
        </div>
        <!-- Sidebar (drawer-side) -->
        <div :class="sidebarClasses">
            <label v-if="props.overlay" :for="drawerId" class="drawer-overlay" tabindex="-1"
                @click="props.closeOnOverlay ? isOpen = false : null" />
            <slot name="sidebar" />
        </div>
    </div>
</template>

<style scoped></style>
