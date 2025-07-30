<script setup>
/**
 * Header Layout (Atomic Design, DaisyUI)
 *
 * @description
 * Layout Header du projet KrosmozJDR, refactorisé pour n'utiliser que des atoms et molecules du design system.
 * - Utilise :
 *   - Molecule Navbar comme conteneur principal (slots start, center, end)
 *   - Molecules ToggleSidebar, SearchInput, LoggedHeaderContainer, LoginHeaderContainer
 *   - Atoms Btn, Swap, Icon, Tooltip pour le bouton toggle header
 * - Structure layout dans <Navbar>, tout le contenu est atomique/moleculaire
 * - Accessibilité et props transmises via les helpers du design system
 *
 * @see Navbar, Btn, Swap, Icon, Tooltip, ToggleSidebar, SearchInput, LoggedHeaderContainer, LoginHeaderContainer
 */
// Header Layout (structure globale)
//
// La logique d'affichage liée à l'authentification (connecté/non connecté)
// est déléguée à des molecules spécialisées :
// - Molecules/header/LoggedHeaderContainer.vue
// - Molecules/header/LoginHeaderContainer.vue
// Cela permet de garder ce layout propre, centré sur la structure, et de respecter l'Atomic Design.
//
// Voir les docblocks des molecules pour leur API détaillée.

import { usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import Navbar from "@/Pages/Molecules/navigation/Navbar.vue";
import SearchInput from "@/Pages/Organismes/data-input/SearchInput.vue";
import LoginHeaderContainer from "@/Pages/Molecules/header/LoginHeaderContainer.vue";
import LoggedHeaderContainer from "@/Pages/Molecules/header/LoggedHeaderContainer.vue";

// Composables
const page = usePage();
const { pageTitle } = usePageTitle();

// Props
defineProps({
    isOpen: {
        type: Boolean,
        default: true
    }
});
</script>

<template>
    <Navbar :class="['header-animated', { 'header-open': isOpen }]">
        <template #start>
            <Transition name="title" class="ml-14" mode="out-in">
                <h2 :key="pageTitle" id="pageTitle" class="items-center text-2xl font-semibold">
                    {{ pageTitle }}
                </h2>
            </Transition>
        </template>
        <template #center>
            <SearchInput />
        </template>
        <template #end>
            <div class="flex gap-2 items-center">
                <template v-if="page.props.auth.isLogged">
                    <LoggedHeaderContainer />
                </template>
                <template v-else>
                    <LoginHeaderContainer />
                </template>
            </div>
        </template>
    </Navbar>
</template>

<style scoped>
/* Animation du titre de page */
.title-enter-active,
.title-leave-active {
    transition: all 0.3s ease;
}

.title-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}

.title-leave-to {
    opacity: 0;
    transform: translateY(10px);
}
</style>
