<script setup>
/**
* ToggleSidebar Molecule (Custom Toggle)
*
* @description
* Molecule pour ouvrir/fermer l'aside via un bouton hamburger avec animation personnalisée.
* - Utilise un bouton classique avec animation CSS pour éviter les conflits avec les tooltips
* - Icônes FontAwesome (fa-bars, fa-xmark) avec transition fluide
* - Tooltip sur le bouton (ouvrir/fermer le menu)
* - Props : size (xs-xl), shortcut (ex: alt+g)
* - Gère le raccourci clavier pour ouvrir/fermer le menu
* - Comportement responsive : adapte les tooltips selon le mode (desktop vs mobile/tablette)
*
* @example
*
<ToggleSidebar size="xs" />
*
* @props {String} size - Taille de l'icône (xs, sm, md, lg, xl, 2xl)
* @props {String} shortcut - Raccourci clavier (ex: alt+g)
*/
import { ref, computed, onMounted, onUnmounted } from "vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { useSidebar } from "@/Composables/layout/useSidebar";
import { useDevice } from "@/Composables/layout/useDevice";
import Kbd from "@/Pages/Atoms/data-display/Kbd.vue";

const { toggleSidebar, isSidebarOpen, isDesktop } = useSidebar();
const { isMobile, isTablet } = useDevice();

const props = defineProps({
    size: {
        type: String,
        default: "md",
        validator: (value) => ["xs", "sm", "md", "lg", "xl", "2xl"].includes(value),
    },
    shortcut: {
        type: String,
        default: "alt+g"
    }
});

const faSize = computed(() => {
    switch (props.size) {
        case "xs": return "fa-xs";
        case "sm": return "fa-sm";
        case "md": return "fa-lg";
        case "lg": return "fa-2x";
        case "xl": return "fa-3x";
        case "2xl": return "fa-4x";
        default: return "fa-lg";
    }
});

// Computed pour les tooltips selon le mode
const tooltipContent = computed(() => {
    const isMobileMode = isMobile.value || isTablet.value;
    if (isSidebarOpen.value) {
        return isMobileMode ? "Fermer le menu" : "Masquer la barre latérale";
    } else {
        return isMobileMode ? "Ouvrir le menu" : "Afficher la barre latérale";
    }
});

const handleKeydown = (event) => {
    // Empêcher le déclenchement si on est dans un champ de saisie
    if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA' || event.target.contentEditable === 'true') {
        return;
    }
    
    // Gestion spécifique pour Alt+G (raccourci par défaut)
    if (event.altKey && event.key.toLowerCase() === 'g') {
        event.preventDefault(); // Empêcher le comportement par défaut
        toggleSidebar();
        return;
    }
    
    // Gestion générique pour d'autres raccourcis configurés
    const [modifier, key] = props.shortcut.split("+");
    if (modifier !== 'alt' || key !== 'g') { // Éviter le double traitement pour Alt+G
        if (event[`${modifier}Key`] && event.key.toLowerCase() === key.toLowerCase()) {
            event.preventDefault();
            toggleSidebar();
        }
    }
};

onMounted(() => {
    window.addEventListener("keydown", handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener("keydown", handleKeydown);
});
</script>

<template>
    <Tooltip placement="end">
        <template #content>
            <span>{{ tooltipContent }}</span>
            <Kbd size="xs" class="ml-2">{{ shortcut }}</Kbd>
        </template>
        
        <button 
            @click="toggleSidebar"
            class="transition-all duration-300 ease-in-out hover:scale-110 focus:scale-95"
            :aria-label="tooltipContent"
            :title="tooltipContent"
        >
            <!-- Icône avec animation de rotation -->
            <i 
                :class="[
                    'fa-solid transition-all duration-300 ease-in-out',
                    faSize,
                    isSidebarOpen ? 'fa-xmark rotate-90' : 'fa-bars'
                ]" 
            />
        </button>
    </Tooltip>
</template>

<style scoped>
/* Centrer le bouton dans la div du tooltip */
.tooltip {
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Animation personnalisée pour l'icône */
button:hover i {
    transform: scale(1.1);
}

button:active i {
    transform: scale(0.95);
}

/* Animation de rotation pour l'icône X */
.fa-xmark {
    transform: rotate(90deg);
}

/* Animation de retour pour l'icône bars */
.fa-bars {
    transform: rotate(0deg);
}
</style>
