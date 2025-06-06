<script setup>
import { ref } from "vue";
import Icon from "@/Pages/Atoms/images/Icon.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import BaseTooltip from "@/Pages/Atoms/feedback/BaseTooltip.vue";

const convertStability = {
    alpha: "α",
    beta: "β",
    rc: "rc",
    stable: "",
};

const appName = ref(import.meta.env.VITE_APP_NAME);
const appVersion = ref(import.meta.env.VITE_APP_VERSION);
const appDescription = ref(import.meta.env.VITE_APP_DESCRIPTION);
const appStability = ref(convertStability[import.meta.env.VITE_APP_STABILITY]);

const footerItems = [
    {
        icon: "fa-solid fa-envelope",
        href: "mailto:contact@krosmoz-jdr.fr",
        label: "contact@krosmoz-jdr.fr",
        tooltip: "Envoyer un email"
    },
    {
        icon: "fa-brands fa-discord",
        href: "https://discord.gg/XVu4VWFskj",
        label: "#XVu4VWFskj",
        tooltip: "Rejoindre notre serveur Discord",
        target: "_blank"
    }
];
</script>

<template>
    <footer class="h-14 flex justify-center">
        <!-- Desktop Footer -->
        <div class="flex gap-6 max-sm:hidden content-center text-sm">
            <p class="text-content">
                {{ appName }} - version {{ appVersion + " " + appStability }} -
                {{ new Date().getFullYear() }}
            </p>

            <div v-for="item in footerItems" :key="item.label" class="flex items-center gap-2">
                <BaseTooltip :tooltip="{ custom: true }" tooltip-position="top">
                    <Route
                        :href="item.href"
                        :target="item.target"
                        class="flex items-center gap-2 text-content hover:text-primary-200 transition-colors"
                    >
                        <Icon :icon="item.icon" class="w-4 h-4" />
                        <span>{{ item.label }}</span>
                    </Route>

                    <template #tooltip>
                        <span>{{ item.tooltip }}</span>
                    </template>
                </BaseTooltip>
            </div>
        </div>

        <!-- Mobile Footer -->
        <div class="hidden max-sm:block w-full">
            <div class="btm-nav bg-base-200/80 backdrop-blur-lg">
                <button class="text-content hover:text-primary-200">
                    <Icon icon="fa-solid fa-bars" class="w-6 h-6" />
                </button>
                <button class="active text-content hover:text-primary-200">
                    <Icon icon="fa-solid fa-home" class="w-6 h-6" />
                </button>
                <button class="text-content hover:text-primary-200">
                    <Icon icon="fa-solid fa-user" class="w-6 h-6" />
                </button>
            </div>
        </div>
    </footer>
</template>

<style scoped>
footer {
    backdrop-filter: blur(5px);
    background: linear-gradient(
        to bottom,
        rgba(10, 12, 20, 0) 0%,
        rgba(10, 12, 20, 0.3) 20%,
        rgba(10, 12, 20, 0.5) 50%,
        rgb(10, 12, 20) 70%,
        rgb(10, 12, 20) 100%
    );
}
</style>
