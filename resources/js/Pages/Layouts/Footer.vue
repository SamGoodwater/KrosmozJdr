<script setup>
/**
 * Footer Layout (Atomic Design, DaisyUI)
 *
 * @description
 * Pied de page desktop en deux lignes compactes :
 * - ligne 1 : nom + version à gauche, logo au centre, liens (contact, Discord, GitHub) à droite ;
 * - ligne 2 : texte de présentation à gauche, bouton cookies à droite.
 * Mobile : bouton cookies dans le flux, dock de navigation en bas.
 *
 * @see Footer, Icon, Route, Tooltip, CookieConsentTriggerButton
 */
import { ref } from "vue";
import FooterMolecule from "@/Pages/Molecules/navigation/Footer.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import Dock from "@/Pages/Molecules/navigation/Dock.vue";
import DockItem from "@/Pages/Atoms/navigation/DockItem.vue";
import { useSidebar } from "@/Composables/layout/useSidebar";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import CookieConsentTriggerButton from "@/Pages/Molecules/privacy/CookieConsentTriggerButton.vue";

const { toggleSidebar } = useSidebar();

const convertStability = {
    alpha: "α",
    beta: "β",
    rc: "rc",
    stable: "",
};

const appName = ref(import.meta.env.VITE_APP_NAME);
const appVersion = ref(import.meta.env.VITE_APP_VERSION);
const appDescription = ref(import.meta.env.VITE_APP_DESCRIPTION);
const appStability = ref(convertStability[import.meta.env.VITE_APP_STABILITY] ?? "");
const githubUrl = import.meta.env.VITE_GITHUB_URL || "https://github.com/SamGoodwater/KrosmozJdr";
const currentYear = new Date().getFullYear();

const footerItems = [
    {
        icon: "fa-envelope",
        pack: "solid",
        href: "mailto:contact@krosmoz-jdr.fr",
        label: "Contact",
        tooltip: "Envoyer un email",
    },
    {
        icon: "fa-discord",
        pack: "brands",
        href: "https://discord.gg/XVu4VWFskj",
        label: "Discord",
        tooltip: "Rejoindre notre serveur Discord",
        target: "_blank",
    },
    {
        icon: "fa-github",
        pack: "brands",
        href: githubUrl,
        label: "GitHub",
        tooltip: "Voir le dépôt GitHub",
        target: "_blank",
    },
];
</script>

<template>
    <FooterMolecule
        class="relative box-glass-t-xs w-full border-t border-base-content/10 bg-base-300/30 px-4 py-1.5 max-sm:hidden"
        textColor="text-base-content"
        v-bind="$attrs"
    >
        <div class="pointer-events-none absolute inset-x-0 top-1.5 z-0 flex justify-center">
            <div class="pointer-events-auto">
                <Image
                    source="logos/logo.webp"
                    :alt="`Logo de ${appName}`"
                    width="24px"
                    height="24px"
                    fit="contain"
                    rounded="full"
                    class="opacity-90"
                />
            </div>
        </div>
        <div class="flex w-full flex-col gap-0.5 pr-16">
            <div class="relative z-10 flex min-h-6 items-center justify-between gap-3">
                <p class="min-w-0 truncate text-sm font-medium text-base-content">
                    {{ appName }}
                    <span class="font-normal text-base-content/70">
                        v{{ appVersion }}{{ appStability ? ` ${appStability}` : "" }}
                    </span>
                </p>
                <nav
                    class="flex shrink-0 items-center justify-end gap-x-3"
                    aria-label="Liens du pied de page"
                >
                    <Tooltip
                        v-for="item in footerItems"
                        :key="item.label"
                        :content="item.tooltip"
                        placement="top"
                    >
                        <Route
                            :href="item.href"
                            :target="item.target"
                            :rel="item.target === '_blank' ? 'noopener noreferrer' : undefined"
                            hover
                            class="inline-flex items-center gap-1.5 text-sm text-base-content/80 hover:text-base-content"
                        >
                            <Icon :source="item.icon" :pack="item.pack" :alt="item.tooltip" size="sm" class="h-3.5 w-3.5" />
                            <span class="hidden md:inline">{{ item.label }}</span>
                        </Route>
                    </Tooltip>
                </nav>
            </div>
            <div class="relative z-10 flex items-center justify-between gap-3">
                <p class="min-w-0 truncate text-xs text-base-content/70" :title="appDescription">
                    © {{ currentYear }} · {{ appDescription }}
                </p>
                <div class="shrink-0">
                    <CookieConsentTriggerButton />
                </div>
            </div>
        </div>
    </FooterMolecule>
    <!-- Mobile : cookies dans le flux (au-dessus du dock), aligné à droite — non fixe -->
    <div class="flex w-full justify-end px-3 py-2 sm:hidden">
        <CookieConsentTriggerButton />
    </div>
    <!-- Mobile Footer (Dock) -->
    <div class="fixed bottom-0 left-0 right-0 z-50 hidden max-sm:block">
        <Dock size="md" class="px-1 py-2 flex justify-between box-glass-md">
            <!-- Bouton sidebar -->
            <DockItem
                icon="fa-bars"
                pack="solid"
                label="Menu"
                data-kz-nav-toggle-sidebar
                @click="toggleSidebar()"
            />
            <!-- Bouton recherche (placeholder) -->
            <DockItem icon="fa-magnifying-glass" pack="solid" label="Recherche" />
            <!-- Bouton compte/utilisateur (placeholder dropdown) -->
            <DockItem icon="fa-user" pack="solid" label="Compte" />
        </Dock>
    </div>
</template>

<style scoped>
</style>
