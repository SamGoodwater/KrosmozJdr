<script setup>
/**
 * Coque du panneau latéral principal (menu app) : overlay, `<aside>` fixe, z-index,
 * animation slide, `data-kz-nav-app-sidebar` et `data-sidebar-open`.
 * Le contenu métier (logo, DynamicMenu, dock) est fourni par le slot.
 *
 * @see Aside.vue — contenu du menu principal
 * @see Main.vue — branchement ouverture / mode desktop
 */
import { computed } from 'vue';
import { LAYOUT_APP_SIDEBAR_WIDTH_CLASS } from '@/Composables/layout/viewport-breakpoints';

const props = defineProps({
    /** Sidebar considérée ouverte (sync avec useSidebar) */
    sidebarOpen: { type: Boolean, required: true },
    /** true = panneau fixe visible (≥ lg), false = mode drawer tablette / mobile */
    isDesktopMode: { type: Boolean, required: true },
});

const emit = defineEmits(['overlayDismiss']);

const asideChromeClasses = computed(() => {
    if (props.isDesktopMode) {
        return ['fixed z-40 top-0 left-0 bottom-0', LAYOUT_APP_SIDEBAR_WIDTH_CLASS];
    }
    return [
        'fixed z-50 top-0 left-0 bottom-0',
        LAYOUT_APP_SIDEBAR_WIDTH_CLASS,
        'bg-base-100/95 backdrop-blur-sm',
    ];
});

const showOverlay = computed(
    () => !props.isDesktopMode && props.sidebarOpen
);

function onOverlayClick() {
    emit('overlayDismiss');
}
</script>

<template>
    <div class="app-sidebar-shell contents">
        <div
            v-if="showOverlay"
            class="fixed inset-0 z-30 backdrop-blur-xs brightness-80 transition-all duration-300 ease-in-out overlay-animated"
            aria-hidden="true"
            @click="onOverlayClick"
        ></div>

        <aside
            data-kz-nav-app-sidebar
            :class="[
                ...asideChromeClasses,
                'sidebar-animated flex h-full min-h-full flex-col flex-nowrap border-glass-r-xs bd-blur-md',
            ]"
            :data-sidebar-open="sidebarOpen ? 'true' : 'false'"
        >
            <slot />
        </aside>
    </div>
</template>

<style scoped lang="scss">
/* `contents` : le shell ne crée pas de boîte de mise en page entre Main et aside */
.app-sidebar-shell {
    display: contents;
}

.sidebar-animated {
    position: fixed !important;
    transition: none;

    transform: translateX(-100%);
    filter: blur(8px);
    opacity: 0;
    visibility: hidden;

    &[data-sidebar-open='true'] {
        transform: translateX(0);
        filter: blur(0px);
        opacity: 1;
        visibility: visible;
        transition:
            transform 0.4s cubic-bezier(0.4, 0, 0.2, 1),
            filter 0.5s cubic-bezier(0.4, 0, 0.2, 1),
            opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1),
            visibility 0s;
    }

    &[data-sidebar-open='false'] {
        transform: translateX(-100%);
        filter: blur(8px);
        opacity: 0;
        visibility: hidden;
        transition:
            transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
            filter 0.2s cubic-bezier(0.4, 0, 0.2, 1),
            opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1),
            visibility 0s 0.3s;
    }
}

.overlay-animated {
    transition:
        backdrop-filter 0.4s cubic-bezier(0.4, 0, 0.2, 1),
        background-color 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
