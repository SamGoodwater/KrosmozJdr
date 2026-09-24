<script setup>
defineOptions({ inheritAttrs: false });

/**
 * Drawer Molecule (DaisyUI + Custom Utility)
 *
 * @description
 * Molécule Drawer stylée DaisyUI, conforme Atomic Design et KrosmozJDR.
 * - Props : modelValue (v-model), side ('start'|'end'), overlay, id, width, closeOnOverlay, portal, overlayOnly
 * - Mode `portal` + `overlayOnly` : panneau flottant téléporté sur `body` (filtres, sous-menus…)
 *   sans envelopper le contenu de page dans `.drawer-content`.
 *
 * @see https://daisyui.com/components/drawer/
 *
 * @example
 * <Drawer v-model="open" side="end" portal overlay-only width="w-full max-w-sm">
 *   <template #sidebar>
 *     <div class="menu h-full bg-base-100 p-4">Filtres…</div>
 *   </template>
 * </Drawer>
 *
 * @slot default - Contenu principal (drawer-content) — ignoré si overlayOnly
 * @slot sidebar - Contenu du panneau latéral
 * @slot toggle - Bouton d'ouverture custom
 */
import { computed, watch } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const emit = defineEmits(['update:modelValue', 'open', 'close']);
const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    modelValue: { type: Boolean, default: false },
    side: { type: String, default: 'start', validator: (v) => ['start', 'end'].includes(v) },
    overlay: { type: Boolean, default: true },
    width: { type: String, default: 'w-80' },
    closeOnOverlay: { type: Boolean, default: true },
    /**
     * Téléporte le drawer sur `body` (évite les overflow parents).
     */
    portal: { type: Boolean, default: false },
    /**
     * Panneau seul (pas de wrapper autour du contenu page). Idéal pour filtres / options.
     */
    overlayOnly: { type: Boolean, default: false },
});

const drawerId = computed(() => props.id || `drawer-${Math.random().toString(36).substr(2, 9)}`);
const isOpen = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
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
            props.overlayOnly && 'drawer-overlay-only',
            props.portal && 'drawer-portal',
            props.class,
        ],
        getCustomUtilityClasses(props)
    )
);
const attrs = computed(() => getCommonAttrs(props));

const sidebarClasses = computed(() =>
    mergeClasses([
        'drawer-side',
        props.overlayOnly && 'z-[80]',
        props.width,
    ])
);

function closeFromOverlay() {
    if (props.closeOnOverlay) {
        isOpen.value = false;
    }
}
</script>

<template>
    <Teleport to="body" :disabled="!portal">
        <div
            v-if="!overlayOnly || modelValue"
            :class="moleculeClasses"
            v-bind="attrs"
            v-on="$attrs"
        >
            <input
                :id="drawerId"
                type="checkbox"
                class="drawer-toggle"
                :checked="isOpen"
                @change="isOpen = !isOpen"
                style="display: none"
            />
            <slot name="toggle" />
            <div v-if="!overlayOnly" class="drawer-content">
                <slot />
            </div>
            <div v-else class="drawer-content pointer-events-none !min-h-0 !h-0 overflow-hidden" aria-hidden="true" />
            <div :class="sidebarClasses">
                <label
                    v-if="overlay"
                    :for="drawerId"
                    class="drawer-overlay"
                    tabindex="-1"
                    @click="closeFromOverlay"
                />
                <div
                    class="drawer-panel flex h-full min-h-full max-h-dvh flex-col overflow-hidden bg-base-100 shadow-xl"
                    :class="width"
                >
                    <slot name="sidebar" />
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.drawer-portal.drawer-overlay-only {
    position: fixed;
    inset: 0;
    z-index: 80;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.drawer-portal.drawer-overlay-only.drawer-open {
    pointer-events: auto;
}

.drawer-portal.drawer-overlay-only :deep(.drawer-side) {
    pointer-events: auto;
}

.drawer-panel {
    position: relative;
    z-index: 1;
}
</style>
