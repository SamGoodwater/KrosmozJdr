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
import { onMounted } from "vue";
import { useHeader } from "@/Composables/layout/useHeader";
import { useSidebar } from "@/Composables/layout/useSidebar";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import Navbar from "@/Pages/Molecules/navigation/Navbar.vue";
import SearchInput from "@/Pages/Molecules/data-input/SearchInput.vue";
import ToggleSidebar from "@/Pages/Molecules/layout/ToggleSidebar.vue";
import LoginHeaderContainer from "@/Pages/Molecules/header/LoginHeaderContainer.vue";
import LoggedHeaderContainer from "@/Pages/Molecules/header/LoggedHeaderContainer.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Swap from "@/Pages/Atoms/action/Swap.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import ThemeController from "@/Pages/Molecules/action/ThemeController.vue";

// Composables
const page = usePage();
const { isSidebarOpen } = useSidebar();
const { isHeaderOpen, toggleHeader } = useHeader();
const { pageTitle } = usePageTitle();

// Raccourci clavier pour le header
onMounted(() => {
    window.addEventListener("keydown", (e) => {
        if (e.altKey && e.key === "h") {
            toggleHeader();
        }
    });
});
</script>

<template>
    <Navbar>
        <template #start>
            <ToggleSidebar v-if="!isSidebarOpen" />
            <Transition name="title" mode="out-in">
                <h2
                    :key="pageTitle"
                    id="pageTitle"
                    class="items-center text-content hover:text-content/75 text-2xl font-semibold"
                >
                    {{ pageTitle }}
                </h2>
            </Transition>
        </template>
        <template #center>
            <div class="max-sm:hidden">
                <SearchInput />
            </div>
        </template>
        <template #end>
            <div class="flex gap-2 items-center">
                <template v-if="page.props.auth.isLogged">
                    <LoggedHeaderContainer />
                </template>
                <template v-else>
                    <LoginHeaderContainer />
                </template>
                <ThemeController class="mx-1" />
                <Tooltip
                    :content="'Masquer ou afficher l\'entête'"
                    placement="bottom"
                >
                    <Btn
                        variant="ghost"
                        circle
                        class="swap swap-rotate text-content hover:text-content/50 transition-colors"
                        @click="toggleHeader"
                    >
                        <Swap :active="!isHeaderOpen" rotate>
                            <template #on>
                                <Icon
                                    source="fa-chevron-down"
                                    alt="Menu déroulant"
                                    size="md"
                                    pack="solid"
                                />
                            </template>
                            <template #off>
                                <Icon
                                    source="fa-chevron-up"
                                    alt="Menu remonté"
                                    size="md"
                                    pack="solid"
                                />
                            </template>
                        </Swap>
                    </Btn>
                    <template #tooltip>
                        <div
                            class="w-52 bg-secondary-900/75 text-center flex flex-col p-2"
                        >
                            <p
                                class="text-md text-content flex justify-between gap-3"
                            >
                                <span>Masquer ou afficher l'entête</span>
                                <span class="flex flex-nowrap items-center">
                                    <kbd class="kbd kbd-sm">alt</kbd> +
                                    <kbd class="kbd kbd-sm">h</kbd>
                                </span>
                            </p>
                            <p class="text-sm text-content/70 my-2">
                                Déplacer la souris vers le haut du site pour
                                faire réaparaître l'entête
                            </p>
                            <div class="flex justify-center relative">
                                <div class="header-vector bg-slate-500"></div>
                                <div class="h-40 w-40 bg-slate-800"></div>
                                <div class="mouse-vector">
                                    <Icon
                                        source="fa-mouse-pointer"
                                        alt="Cliquer"
                                        size="md"
                                        pack="solid"
                                    />
                                </div>
                            </div>
                        </div>
                    </template>
                </Tooltip>
            </div>
        </template>
    </Navbar>
</template>

<style scoped>
.header-vector {
    position: absolute;
    top: 0;
    width: 10rem;
    height: 1.7rem;
    animation: moveHeader 8s infinite;
    transform-origin: top;
}
.mouse-vector {
    position: absolute;
    top: 4rem;
    left: 3rem;
    transform: translate(-50%, -50%);
    animation: moveMouse 4s infinite;
}
@keyframes moveMouse {
    0%,
    100% {
        transform: translate(-50%, -50%) translateY(0);
    }
    50% {
        transform: translate(-50%, -50%) translateY(1rem);
    }
}
@keyframes moveHeader {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(1rem);
    }
}
</style>
