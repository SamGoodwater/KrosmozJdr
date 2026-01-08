<script setup>
/**
* Main Layout (Atomic Design, DaisyUI)
*
* @description
* Layout principal du projet KrosmozJDR, refactorisé pour respecter l'atomic design.
* - Utilise l'atom Container pour le contenu principal (main)
* - Structure : background, Header, Aside, Container (main), Footer, notifications
* - Responsive, glassmorphism, accessibilité
* - Notifications centralisées via l'organism NotificationContainer (plus de slots de notifications)
*
* @see Container, Header, Aside, Footer, NotificationContainer
*
* @slot default - Contenu principal de la page
*/
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'

// Composants
import Header from "@/Pages/Layouts/Header.vue";
import Aside from "@/Pages/Layouts/Aside.vue";
import Footer from "@/Pages/Layouts/Footer.vue";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import NotificationContainer from "@/Pages/Organismes/feedback/NotificationContainer.vue";
import ToggleSidebar from "@/Pages/Molecules/layout/ToggleSidebar.vue";
import { useHeader } from "@/Composables/layout/useHeader";
import { useSidebar } from "@/Composables/layout/useSidebar";
import { useDevice } from "@/Composables/layout/useDevice";
import ToggleHeader from "@/Pages/Molecules/layout/ToggleHeader.vue";
import { useNotificationProvider } from "@/Composables/providers/useNotificationProvider";

// Centralisation des classes Tailwind pour le layout
const ASIDE_WIDTH_CLASS = 'w-64'      // 16rem = 256px
const HEADER_HEIGHT_CLASS = 'h-18'    // 
const OFFSET_LEFT_CLASS = 'left-64'
const OFFSET_TOP_CLASS = 'top-18'
const PADDING_TOP_CLASS = 'pt-18'

const { isHeaderOpen, toggleHeader } = useHeader();
const { isMobile, isTablet, isDesktop } = useDevice();
const { isSidebarOpen, toggleSidebar, closeSidebar } = useSidebar();

// Provider de notifications pour toute l'application
useNotificationProvider();

// Computed pour déterminer le comportement responsive
const isDesktopMode = computed(() => isDesktop.value);
const isMobileMode = computed(() => isMobile.value || isTablet.value);

// Computed pour les classes conditionnelles
const asideClasses = computed(() => {
    if (isDesktopMode.value) {
        // Desktop : Aside fixe à gauche
        return ['fixed z-40 top-0 left-0 bottom-0', ASIDE_WIDTH_CLASS];
    } else {
        // Mobile/Tablette : Aside en overlay (au-dessus de l'overlay)
        return ['fixed z-50 top-0 left-0 bottom-0', ASIDE_WIDTH_CLASS, 'bg-base-100/95 backdrop-blur-sm'];
    }
});

const mainClasses = computed(() => {
    const baseClasses = [
        'right-0 bottom-0 top-0 overflow-y-auto fixed'
    ];
    
    // Ajouter le padding-top seulement si le header est ouvert ET qu'on n'est pas en mobile
    if (isHeaderOpen.value && !isMobile.value) {
        baseClasses.push(PADDING_TOP_CLASS);
    } else {
        baseClasses.push('pt-0');
    }
    
    if (isDesktopMode.value && isSidebarOpen.value) {
        // Desktop avec sidebar ouverte : décaler le contenu
        return [...baseClasses, OFFSET_LEFT_CLASS];
    } else {
        // Desktop sans sidebar ou mobile/tablette : pas de décalage
        return [...baseClasses, 'left-0'];
    }
});

const headerClasses = computed(() => {
    const baseClasses = ['!fixed top-0 right-0 z-30', HEADER_HEIGHT_CLASS];
    
    if (isDesktopMode.value && isSidebarOpen.value) {
        return [...baseClasses, OFFSET_LEFT_CLASS];
    } else {
        return [...baseClasses, 'left-0'];
    }
});

const toggleClasses = computed(() => {
    const baseClasses = ['fixed top-6 z-50 max-sm:hidden'];
    
    if (isDesktopMode.value) {
        if (isSidebarOpen.value) {
            return [...baseClasses, OFFSET_LEFT_CLASS, 'ml-[-3rem] opacity-50'];
        } else {
            return [...baseClasses, 'left-4 opacity-70'];
        }
    } else {
        // Mobile/Tablette : toujours visible en haut à gauche
        return [...baseClasses, 'left-4'];
    }
});

const headerToggleClasses = computed(() => {
    const baseClasses = ['fixed top-5 right-4 z-50 max-sm:hidden'];
    
    if (isDesktopMode.value) {
        if (isHeaderOpen.value) {
            return [...baseClasses, 'opacity-50'];
        } else {
            return [...baseClasses, 'opacity-70'];
        }
    } else {
        // Mobile/Tablette : toujours visible en haut à droite
        return [...baseClasses];
    }
});

// Gestion du clic extérieur pour fermer l'Aside en mode superposition
// Note: Cette fonction n'est plus nécessaire car nous utilisons un overlay dédié
// mais nous la gardons pour la compatibilité et les clics sur d'autres éléments
const handleClickOutside = (event) => {
    // Seulement en mode mobile/tablette et si l'Aside est ouvert
    if (!isMobileMode.value || !isSidebarOpen.value) {
        return;
    }
    
    // Vérifier si le clic est sur le bouton toggle (pour éviter la fermeture)
    const toggleElement = event.target.closest('[data-toggle-sidebar]');
    
    // Si le clic n'est pas sur le bouton toggle, fermer l'Aside
    if (!toggleElement) {
        closeSidebar();
    }
};

// Gestion du resize avec logique responsive
function handleResize() {
    // Si on passe en mode desktop et que la sidebar est fermée, l'ouvrir
    if (isDesktopMode.value && !isSidebarOpen.value) {
        toggleSidebar();
    }
    // Si on passe en mode mobile/tablette et que la sidebar est ouverte, la fermer
    else if (isMobileMode.value && isSidebarOpen.value) {
        toggleSidebar();
    }
}

// Watcher pour réagir aux changements de mode d'appareil
watch([isDesktop, isMobile, isTablet], () => {
    handleResize();
}, { immediate: false });

// Watcher pour gérer les événements de clic selon l'état de l'Aside
// Note: Avec l'overlay, cette logique est moins critique mais gardée pour la compatibilité
watch(isSidebarOpen, (newValue) => {
    nextTick(() => {
        if (newValue && isMobileMode.value) {
            // Ajouter l'écouteur de clic quand l'Aside s'ouvre en mode mobile
            document.addEventListener('click', handleClickOutside);
        } else {
            // Retirer l'écouteur quand l'Aside se ferme ou en mode desktop
            document.removeEventListener('click', handleClickOutside);
        }
    });
}, { immediate: true });

onMounted(() => {
    window.addEventListener('resize', handleResize)
})
onUnmounted(() => {
    window.removeEventListener('resize', handleResize)
    // Nettoyer l'écouteur de clic au démontage
    document.removeEventListener('click', handleClickOutside);
})
</script>

<template>
    <div class="relative min-h-screen w-full overflow-x-hidden">
        <!-- Background (image + fallback dégradé) -->
        <div class="background">
            <div class="background-image" aria-hidden="true"></div>
            <div class="background-filter--1" aria-hidden="true"></div>
            <div class="background-filter--2" aria-hidden="true"></div>
        </div>

        <!-- Header -->
        <Header 
            v-if="!isMobile"
            :class="headerClasses" 
            :is-open="isHeaderOpen"
            class="header-animated"
        />

        <!-- Toggle Aside -->
        <ToggleSidebar :class="toggleClasses" data-toggle-sidebar />

        <!-- Toggle Header -->
        <ToggleHeader :class="headerToggleClasses" data-toggle-header />

        <!-- Overlay pour mobile/tablette (seulement quand Aside est ouvert) -->
        <div 
            v-if="isMobileMode && isSidebarOpen" 
            class="fixed inset-0 backdrop-blur-[4px] brightness-80 z-30 transition-all duration-300 ease-in-out overlay-animated"
            @click="closeSidebar"
            aria-hidden="true"
        ></div>

        <!-- Sidebar (comportement responsive) -->
        <Aside 
            :class="asideClasses" 
            :data-sidebar-open="isSidebarOpen"
            class="sidebar-animated"
        />

        <!-- Main content -->
        <main :class="mainClasses" class="main-animated">
            <div class="min-h-full flex flex-col">
                <!-- Contenu principal - utilise toute la largeur disponible -->
                <div class="flex-1 w-full p-4">
                    <Container fluid>
                        <slot />
                    </Container>
                </div>
                
                <!-- Footer -->
                <Footer :class="['relative z-30']" />
            </div>
        </main>

        <!-- Notifications -->
        <NotificationContainer />
    </div>
</template>

<style scoped lang="scss">
.background {
    position: fixed;
    z-index: -1;
    inset: 0;
    filter: blur(24px) brightness(0.5);
    background-color: var(--color-stone-900);

    &-image {
        position: absolute;
        z-index: 1;
        inset: 0;
        background-image: url('/storage/images/backgrounds/background.webp');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        opacity: 0.8;
    }

    &-filter--1 {
        position: absolute;
        z-index: 2;
        inset: 0;
        background-image: linear-gradient(
            195deg,
            var(--color-primary-400) 0%,
            var(--color-primary-500) 3%,
            var(--color-primary-600) 10%,
            var(--color-primary-700) 25%,
            var(--color-primary-800) 40%,
            var(--color-primary-900) 62%,
            var(--color-primary-950) 100%
        );
        opacity: 0.2;
    }

    &-filter--2 {
        position: absolute;
        z-index: 3;
        inset: 0;
        opacity: 0.2;
        background: radial-gradient(circle, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0) 60%, mix-color(in oklch var(--color-primary-900) 100%));
    }
}

/* Animation personnalisée pour l'Aside */
.sidebar-animated {
    position: fixed !important;
    transition: none; /* Désactiver les transitions par défaut */
    
    /* État initial : caché et flou */
    transform: translateX(-100%);
    filter: blur(8px);
    opacity: 0;
    visibility: hidden;
    
    /* Animation d'entrée */
    &[data-sidebar-open="true"] {
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
    
    /* Animation de sortie */
    &[data-sidebar-open="false"] {
        transform: translateX(-100%);
        filter: blur(8px);
        opacity: 0;
        visibility: hidden;
        transition: 
            transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
            filter 0.2s cubic-bezier(0.4, 0, 0.2, 1),
            opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1),
            visibility 0s 0.3s; /* Délai pour la visibilité */
    }
}

/* Animation personnalisée pour le Header */
.header-animated {
    position: fixed !important;
    transition: none; /* Désactiver les transitions par défaut */
    
    /* État initial : caché en haut */
    transform: translateY(-100%);
    opacity: 0;
    visibility: hidden;
    
    /* Animation d'entrée */
    &.header-open {
        transform: translateY(0);
        opacity: 1;
        visibility: visible;
        transition: 
            transform 0.4s cubic-bezier(0.4, 0, 0.2, 1),
            opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1),
            visibility 0s,
            left 0.4s cubic-bezier(0.4, 0, 0.2, 1); /* Transition de la largeur */
    }
    
    /* Animation de sortie */
    &:not(.header-open) {
        transform: translateY(-100%);
        opacity: 0;
        visibility: hidden;
        transition: 
            transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
            opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1),
            visibility 0s 0.3s, /* Délai pour la visibilité */
            left 0.3s cubic-bezier(0.4, 0, 0.2, 1); /* Transition de la largeur */
    }
}

/* Animation du contenu principal */
.main-animated {
    transition: 
        padding-top 0.4s cubic-bezier(0.4, 0, 0.2, 1),
        left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Animation de l'overlay */
.overlay-animated {
    transition: 
        backdrop-filter 0.4s cubic-bezier(0.4, 0, 0.2, 1),
        background-color 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Styles pour le bouton toggle header */
.header-toggle-btn {
    position: relative;
    overflow: hidden;
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: var(--color-base-content);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.header-toggle-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transform: translateX(-100%);
    transition: transform 0.3s ease;
}

.header-toggle-btn:hover::before {
    transform: translateX(100%);
}

.header-toggle-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.3);
}

.header-toggle-btn:active {
    transform: scale(0.95);
}

.header-toggle-btn i {
    font-size: 1.25rem;
    transition: transform 0.3s ease-in-out;
}

.header-toggle-btn:hover i {
    transform: scale(1.1);
}
</style>
