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
import { usePage } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const page = usePage();
const user = ref(page.props.auth.user);
const avatar = ref(user.value.avatar);
const pseudo = ref(user.value.name);

watch(
    () => page.props.auth.user,
    (newUser) => {
        user.value = newUser;
        avatar.value = newUser.avatar;
        pseudo.value = newUser.name;
    },
    { deep: true },
);
</script>

<template>
    <div class="flex justify-end">
        <div class="flex items-center mx-6 max-sm:mx-4">
            <Dropdown placement="bottom-end">
                <Tooltip :tooltip="'Notifications'" tooltip_placement="bottom">
                    <div class="indicator">
                        <span
                            class="indicator-item badge bg-secondary/80 badge-xs text-content-light"
                            >0</span
                        >
                        <Btn color="neutral" variant="link">
                            <Icon
                                source="fa-bell"
                                alt="Notifications"
                                size="md"
                                pack="regular"
                            />
                        </Btn>
                    </div>
                    <template #tooltip>
                        <div>
                            <p>Vous avez 0 notifications</p>
                        </div>
                    </template>
                </Tooltip>
                <template #content>
                    <div
                        id="notifications-panel"
                        class="flex flex-col items-center justify-center min-h-32 max-h-96 overflow-y-auto text-content-dark bg-base-100/80 p-2"
                    >
                        <p>Vous avez 0 notifications</p>
                    </div>
                </template>
            </Dropdown>
        </div>
        <div class="flex flex-col text-right">
            <Btn color="neutral" variant="ghost">
                <Dropdown>
                    <template #trigger>
                        <div class="flex items-center gap-2">
                            <Avatar
                                :src="user.avatar"
                                :alt="user.name"
                                size="sm"
                            />
                            <span>{{ user.name }}</span>
                        </div>
                    </template>
                    <template #content>
                        <div class="flex flex-col gap-2">
                            <Btn
                                color="neutral"
                                variant="ghost"
                                size="md"
                                content="Mon compte"
                            />
                            <Btn
                                color="neutral"
                                variant="ghost"
                                size="md"
                                content="Se déconnecter"
                            />
                        </div>
                    </template>
                </Dropdown>
            </Btn>
        </div>
    </div>
</template>
