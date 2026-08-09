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

import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import Navbar from "@/Pages/Molecules/navigation/Navbar.vue";
import SearchInput from "@/Pages/Organismes/data-input/SearchInput.vue";
import LoginHeaderContainer from "@/Pages/Molecules/header/LoginHeaderContainer.vue";
import LoggedHeaderContainer from "@/Pages/Molecules/header/LoggedHeaderContainer.vue";
import AlmanaxHeaderBadge from "@/Pages/Molecules/header/AlmanaxHeaderBadge.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { useFavoritesUiStore } from "@/Composables/store/useFavoritesUiStore";
import {
    ensureFavoritesLoaded,
    invalidateFavoritesCache,
} from "@/Composables/entity/useFavoriteEntityIds";
import { onMounted, watch } from "vue";
import { usePage } from "@inertiajs/vue3";

// Composables
const { pageTitle } = usePageTitle();
const { isAuthenticated } = usePermissions();
const favoritesUi = useFavoritesUiStore();
const page = usePage();

function openFavorites() {
    favoritesUi.open();
}

onMounted(() => {
    if (isAuthenticated.value) {
        ensureFavoritesLoaded(page.props.auth?.user).catch(() => {});
    }
});

watch(
    () => page.props.auth?.user?.id,
    (id) => {
        if (id) {
            ensureFavoritesLoaded(page.props.auth?.user).catch(() => {});
        } else {
            invalidateFavoritesCache();
            ensureFavoritesLoaded(null).catch(() => {});
        }
    },
);

// Props
defineProps({
    isOpen: {
        type: Boolean,
        default: true
    }
});
</script>

<template>
    <Navbar :class="['header-animated', { 'header-open': isOpen  }]">
        <template #start>
            <Transition name="title" class="ml-14" mode="out-in">
                <h2 :key="pageTitle" id="pageTitle" class="items-center truncate text-lg font-semibold sm:text-2xl max-w-[min(100%,28rem)]">
                    {{ pageTitle }}
                </h2>
            </Transition>
        </template>
        <template #center>
            <SearchInput />
        </template>
        <template #end>
            <div class="flex gap-2 items-center mr-12">
                <AlmanaxHeaderBadge />
                <Tooltip content="Mes favoris" placement="bottom">
                    <Btn
                        variant="link"
                        color="neutral"
                        square
                        class="header-favorites-btn text-base-content/45 hover:text-base-content transition-colors duration-200"
                        aria-label="Mes favoris"
                        title="Mes favoris"
                        data-cy="header-favorites-btn"
                        @click="openFavorites"
                    >
                        <Icon
                            source="fa-heart"
                            pack="regular"
                            size="lg"
                            alt=""
                            class="transition-colors duration-200"
                        />
                    </Btn>
                </Tooltip>
                <LoggedHeaderContainer v-if="isAuthenticated" />
                <LoginHeaderContainer v-else />
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

/* Favoris header : icône atténuée → plus claire au survol (comme la cloche). */
.header-favorites-btn :deep(svg),
.header-favorites-btn :deep(i) {
    color: inherit;
    transition: color 0.2s ease;
}
</style>
