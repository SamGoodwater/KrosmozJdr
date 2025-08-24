<script setup>
/**
 * LoggedHeaderContainer Molecule (Header Auth - Utilisateur connecté)
 *
 * @description
 * Molécule du design system KrosmozJDR pour l'affichage du header utilisateur connecté.
 * - À placer dans Molecules/header/ (atomicité, séparation des responsabilités)
 * - Utilisé exclusivement par Layouts/Header.vue pour la section droite du header quand l'utilisateur est connecté
 * - Gère l'affichage de l'avatar, du pseudo, du menu utilisateur, des notifications, etc.
 * - N'inclut aucune logique métier globale du header (seulement l'affichage auth)
 * - Utilise les atoms/molecules du design system (Btn, Dropdown, Avatar, Route, etc.)
 *
 * @see Layouts/Header.vue (intégration)
 *
 * @props {Aucune} (récupère l'utilisateur via usePage)
 * @slot default - (non utilisé)
 *
 * @note Ce composant ne doit être utilisé que dans le header principal.
 * @note Respecte la philosophie Atomic Design (niveau molecule, composition d'atoms).
 */
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import Avatar from "@/Pages/Atoms/data-display/Avatar.vue";
import Dropdown from "@/Pages/Atoms/action/Dropdown.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import { usePage, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const page = usePage();
const user = ref(page.props.auth.user);
const avatar = ref(user.value.avatar);
const pseudo = ref(user.value.name);

watch(
    () => page.props.auth.user,
    (newUser) => {
        if (newUser) {
            user.value = newUser;
            avatar.value = newUser.avatar;
            pseudo.value = newUser.name;
        }
    },
    { deep: true },
);

// Fonction de déconnexion
const logout = () => {
    router.post(route('logout'));
};
</script>
<template>
    <div class="flex justify-end">
        <!-- Mon compte -->
        <div class="flex flex-col text-right">
            <Dropdown :close-on-content-click="false">
                <template #trigger>
                    <Btn color="neutral" variant="ghost">
                        <div class="flex items-center gap-2">
                            <Avatar
                                :src="user.avatar"
                                :alt="user.name"
                                size="md"
                            />
                            <span>{{ user.name.charAt(0).toUpperCase() + user.name.slice(1) }}</span>
                        </div>
                    </Btn>
                </template>
                <template #content>
                    <div class="flex flex-col items-start gap-2">
                        <Btn
                            variant="ghost"
                            size="md"
                            content="Mon compte"
                        />
                        <span class="border-glass-b-sm w-full h-px"></span>
                        <Btn
                            variant="ghost"
                            size="sm"
                            content="Se déconnecter"
                            @click="logout"
                        />
                    </div>
                </template>
            </Dropdown>
        </div>
        <!-- Notifications -->
        <div class="flex items-center mx-6 max-sm:mx-4">
            <Dropdown placement="bottom-end">
                <template #trigger>
                    <div class="indicator">
                        <span class="indicator-item badge bg-primary/20 badge-xs rounded-full text-primary">0</span>
                        <Btn variant="link" color="neutral" circle>   
                            <Icon
                                    source="fa-bell"
                                    alt="Notifications"
                                    size="lg"
                                    pack="regular"
                                />
                        </Btn>
                    </div>
                </template>
                <template #content>
                    <div class="flex flex-col items-center justify-center min-h-32 max-h-96 overflow-y-auto p-2">
                        <p>Vous avez 0 notifications</p>
                    </div>
                </template>
            </Dropdown>
        </div>
    </div>
</template>
