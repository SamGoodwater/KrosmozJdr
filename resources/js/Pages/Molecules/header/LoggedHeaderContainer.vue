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
import { ref, watch, computed } from "vue";

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

// Vérifier si l'utilisateur est admin ou super_admin
// Le rôle est stocké comme un entier : 4 = admin, 5 = super_admin
const isAdmin = computed(() => {
    if (!user.value) return false;
    // Vérifier par valeur entière (4 = admin, 5 = super_admin)
    return user.value.role === 4 || user.value.role === 5;
});

// Vérifier si l'utilisateur est game_master, admin ou super_admin
// Le rôle est stocké comme un entier : 3 = game_master, 4 = admin, 5 = super_admin
const canManagePages = computed(() => {
    if (!user.value) return false;
    return user.value.role === 3 || user.value.role === 4 || user.value.role === 5;
});

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
                                :label="user.name"
                                :alt="user.name"
                                size="md"
                            />
                            <span>{{ user.name.charAt(0).toUpperCase() + user.name.slice(1) }}</span>
                        </div>
                    </Btn>
                </template>
                <template #content>
                    <div class="flex flex-col items-start gap-2">
                        <Route route="user.show" class="w-full">
                            <Btn
                                variant="ghost"
                                size="md"
                                content="Mon compte"
                            />
                        </Route>
                        <span class="border-glass-b-sm w-full h-px"></span>
                        <template v-if="isAdmin">
                            <div class="w-full">
                                <p class="text-xs text-subtitle/60 px-2 py-1 font-semibold text-center">Administration</p>
                                <Route route="scrapping.index" class="w-full">
                                    <Btn variant="ghost" size="md" class="w-full justify-start">
                                        <Icon source="fa-magnifying-glass" pack="solid" size="sm" alt="Scrapping" class="mr-2"/>
                                        <span>Scrapping</span>
                                    </Btn>
                                </Route>
                                <Route route="user.index" class="w-full">
                                    <Btn variant="ghost" size="md" class="w-full justify-start">
                                        <Icon source="fa-users" pack="solid" size="sm" alt="Utilisateurs" class="mr-2"/>
                                        <span>Utilisateurs</span>
                                    </Btn>
                                </Route>
                            </div>
                            <span class="border-glass-b-sm w-full h-px"></span>
                        </template>
                        <template v-if="canManagePages">
                            <div class="w-full">
                                <p v-if="!isAdmin" class="text-xs text-subtitle/60 px-2 py-1 font-semibold text-center">Gestion</p>
                                <Route route="pages.index" class="w-full">
                                    <Btn variant="ghost" size="md" class="w-full justify-start">
                                        <Icon source="fa-file-lines" pack="solid" size="sm" alt="Pages" class="mr-2"/>
                                        <span>Pages</span>
                                    </Btn>
                                </Route>
                            </div>
                            <span v-if="!isAdmin" class="border-glass-b-sm w-full h-px"></span>
                        </template>
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
