<script setup>
/**
 * Footer Layout (Atomic Design, DaisyUI)
 *
 * @description
 * Layout Footer du projet KrosmozJDR, refactorisé pour n'utiliser que la molecule Footer et les atoms du design system.
 * - Utilise :
 * - Molecule Footer comme conteneur principal (slots logo, section, copyright)
 * - Atom Icon pour les icônes
 * - Atom Route pour les liens
 * - Atom Tooltip pour les tooltips
 * - Responsive : version desktop (footer classique), version mobile (dock/btm-nav)
 * - Accessibilité et props transmises via les helpers du design system
 *
 * @see Footer, Icon, Route, Tooltip
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
const githubUrl = import.meta.env.VITE_GITHUB_URL ?? "";

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
    ...(githubUrl ? [{
        icon: "fa-github",
        pack: "brands",
        href: githubUrl,
        label: "GitHub",
        tooltip: "Voir le dépôt GitHub",
        target: "_blank",
    }] : []),
];
</script>

<template>
    <FooterMolecule
        direction="vertical"
        textColor="text-base-content"
        class="box-glass-t-xs border-t border-base-content/10 bg-base-300/30 px-4 py-5 max-sm:hidden"
        v-bind="$attrs"
    >
        <template #logo>
            <Image source="logos/logo.webp" :alt="`Logo de ${appName}`" height="28px" class="mx-auto opacity-90" />
        </template>
        <template #section>
            <div class="flex w-full max-w-6xl flex-col gap-4 text-sm lg:flex-row lg:items-center lg:justify-between">
                <div class="min-w-0 space-y-1 text-center lg:text-left">
                    <p class="font-medium text-base-content">
                        {{ appName }}
                        | version {{ appVersion + " " + appStability }}
                        | {{ new Date().getFullYear() }}
                    </p>
                    <p class="text-xs text-base-content/70">
                        {{ appDescription }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-2 lg:justify-end">
                    <span v-for="item in footerItems" :key="item.label">
                        <Tooltip :content="item.tooltip" placement="top">
                            <Route
                                :href="item.href"
                                :target="item.target"
                                hover
                                class="inline-flex items-center gap-2 rounded-box border border-base-content/10 bg-base-100/40 px-3 py-2 text-base-content/80 transition-colors hover:bg-base-100/70 hover:text-base-content"
                            >
                                <Icon :source="item.icon" :pack="item.pack" :alt="item.tooltip" size="sm" class="h-4 w-4" />
                                <span>{{ item.label }}</span>
                            </Route>
                        </Tooltip>
                    </span>
                    <div class="ml-1">
                        <CookieConsentTriggerButton />
                    </div>
                </div>
            </div>
        </template>
    </FooterMolecule>
    <!-- Mobile : cookies dans le flux (au-dessus du dock), aligné à droite — non fixe -->
    <div class="flex w-full justify-end px-3 py-2 sm:hidden">
        <CookieConsentTriggerButton />
    </div>
    <!-- Mobile Footer (Dock) -->
    <div class="fixed bottom-0 left-0 right-0 z-50 max-sm:block hidden">
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
