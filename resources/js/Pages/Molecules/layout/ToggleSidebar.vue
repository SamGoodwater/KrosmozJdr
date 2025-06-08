<script setup>
/**
* ToggleSidebar Molecule (DaisyUI Swap Hamburger)
*
* @description
* Molecule pour ouvrir/fermer l'aside via un bouton hamburger animé DaisyUI (swap).
* - Utilise l'atom Swap pour l'animation et l'accessibilité
* - Icônes FontAwesome (fa-bars, fa-xmark) ou SVG fallback
* - Tooltip sur chaque état (ouvrir/fermer le menu)
* - Props : size (xs-xl), shortcut (ex: alt+g)
* - Gère le raccourci clavier pour ouvrir/fermer le menu
*
* @see https://daisyui.com/components/swap/
*
* @example
*
<ToggleSidebar size="xs" />
*
* @props {String} size - Taille de l'icône (xs, sm, md, lg, xl, 2xl)
* @props {String} shortcut - Raccourci clavier (ex: alt+g)
*/
import { ref, computed, onMounted, onUnmounted } from "vue";
import Swap from "@/Pages/Atoms/action/Swap.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { useSidebar } from "@/Composables/layout/useSidebar";
import Kbd from "@/Pages/Atoms/data-display/Kbd.vue";

const { toggleSidebar, isSidebarOpen } = useSidebar();

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

const handleKeydown = (event) => {
    const [modifier, key] = props.shortcut.split("+");
    if (event[`${modifier}Key`] && event.key.toLowerCase() === key.toLowerCase()) {
        toggleSidebar();
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
    <Swap :model-value="isSidebarOpen" rotate @change="toggleSidebar">
        <template #on>
            <Tooltip placement="top">
                <template #content>
                    <span>Fermer le menu</span>
                    <Kbd size="xs" class="ml-2">{{ shortcut }}</Kbd>
                </template>
                <i :class="['fa-solid', 'fa-xmark', faSize, 'transition-all']" />
            </Tooltip>
        </template>
        <template #off>
            <Tooltip placement="top">
                <template #content>
                    <span>Ouvrir le menu</span>
                    <Kbd size="xs" class="ml-2">{{ shortcut }}</Kbd>
                </template>
                <i :class="['fa-solid', 'fa-bars', faSize, 'transition-all']" />
            </Tooltip>
        </template>
    </Swap>
</template>

<style scoped></style>
