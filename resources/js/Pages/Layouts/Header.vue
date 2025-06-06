<script setup>
import { usePage } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { useHeader } from "@/Composables/layout/useHeader";
import { useSidebar } from "@/Composables/layout/useSidebar";
import { usePageTitle } from "@/Composables/layout/usePageTitle";

// Composants
import SearchInput from "@/Pages/Molecules/layout/SearchInput.vue";
import ToggleSidebar from "@/Pages/Molecules/layout/ToggleSidebar.vue";
import LoginHeaderContainer from "@/Pages/Molecules/layout/LoginHeaderContainer.vue";
import LoggedHeaderContainer from "@/Pages/Molecules/layout/LoggedHeaderContainer.vue";
import BaseTooltip from "@/Pages/Atoms/feedback/BaseTooltip.vue";
import Icon from "@/Pages/Atoms/images/Icon.vue";

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
    <header
        class="flex justify-between p-2 px-4 gap-2 items-center"
        :class="[isHeaderOpen ? 'header-on' : 'header-off hover:header-on']"
    >
        <!-- Section gauche : Toggle Sidebar et Titre -->
        <div class="flex gap-3 items-center">
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
        </div>

        <!-- Section centrale : Barre de recherche -->
        <div class="max-sm:hidden">
            <SearchInput />
        </div>

        <!-- Section droite : Authentification et Toggle Header -->
        <div class="flex gap-2 items-center">
            <template v-if="page.props.auth.isLogged">
                <LoggedHeaderContainer />
            </template>
            <template v-else>
                <LoginHeaderContainer />
            </template>

            <!-- Bouton pour déplier/replier le header -->
            <BaseTooltip
                :tooltip="{ custom: true }"
                tooltip-position="bottom"
            >
                <button
                    class="swap swap-rotate text-content hover:text-content/50 transition-colors"
                    @click="toggleHeader"
                >
                    <Icon
                        icon="fa-solid fa-chevron-up"
                        class="swap-off w-4 h-4"
                    />
                    <Icon
                        icon="fa-solid fa-chevron-down"
                        class="swap-on w-4 h-4"
                    />
                </button>

                <template #tooltip>
                    <div class="w-52 bg-secondary-900/75 text-center flex flex-col p-2">
                        <p class="text-md text-content flex justify-between gap-3">
                            <span>Masquer ou afficher l'entête</span>
                            <span class="flex flex-nowrap items-center">
                                <kbd class="kbd kbd-sm">alt</kbd> +
                                <kbd class="kbd kbd-sm">h</kbd>
                            </span>
                        </p>
                        <p class="text-sm text-content/70 my-2">
                            Déplacer la souris vers le haut du site pour faire réaparaître l'entête
                        </p>
                        <div class="flex justify-center relative">
                            <div class="header-vector bg-slate-500"></div>
                            <div class="h-40 w-40 bg-slate-800"></div>
                            <div class="mouse-vector">
                                <Icon
                                    icon="fa-solid fa-mouse-pointer"
                                    class="w-4 h-4 text-content rotate-270"
                                />
                            </div>
                        </div>
                    </div>
                </template>
            </BaseTooltip>
        </div>
    </header>
</template>

<style scoped>
header {
    backdrop-filter: blur(5px);
    background: linear-gradient(
        to top,
        rgba(23, 27, 36, 0) 0%,
        rgba(23, 27, 36, 0.3) 20%,
        rgba(23, 27, 36, 0.5) 50%,
        rgb(23, 27, 36) 70%,
        rgb(23, 27, 36) 100%
    );
    box-shadow: 0 1px 10px -1px rgba(23, 27, 36, 0.3);
    transition: transform 0.3s ease-in-out;
}

.header-on {
    transform: translateY(0);
    opacity: 1;
}

.header-off {
    transform: translateY(-60%);
    opacity: 0;
}

.header-off:hover {
    transform: translateY(0);
    opacity: 1;
}

.mouse-vector {
    position: absolute;
    top: 4rem;
    left: 3rem;
    transform: translate(-50%, -50%);
    animation: moveMouse 4s infinite;
}

.header-vector {
    position: absolute;
    top: 0;
    width: 10rem;
    height: 1.7rem;
    animation: moveHeader 8s infinite;
    transform-origin: top;
}

@keyframes moveMouse {
    0%, 100% {
        transform: translate(-50%, -50%) translateY(0);
    }
    50% {
        transform: translate(-50%, -50%) translateY(1rem);
    }
}

@keyframes moveHeader {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(1rem);
    }
}
</style>
