/**
* Dashboard component that displays user information and their content modules.
* Provides sections for campaigns, scenarios, and NPCs with creation capabilities.
*
* Features:
* - User profile display with avatar and role
* - Email verification status
* - Content modules display (Campaigns, Scenarios, NPCs)
* - Creation buttons for each content type
* - Responsive design
*
* Props:
* - theme: Theme configuration for styling
*
* Events:
* - @moduleCreated: Emitted when a new module is created
* - @moduleUpdated: Emitted when a module is updated
*/
<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";

// Composants existants
import Avatar from "@/Pages/Molecules/images/Avatar.vue";
import Btn from "@/Pages/Atoms/actions/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import BadgeRole from "@/Pages/Molecules/user/BadgeRole.vue";
import Container from "@/Pages/Atoms/panels/Container.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import ModuleCard from "@/Pages/Molecules/modules/ModuleCard.vue";
import BaseTooltip from "@/Pages/Atoms/feedback/BaseTooltip.vue";
import VerifyMailAlert from "@/Pages/Molecules/user/VerifyMailAlert.vue";

// Récupération des données partagées par Inertia
const page = usePage();
const user = ref(page.props.user.data);
const { setPageTitle } = usePageTitle();

onMounted(() => {
    setPageTitle('Mon Compte');
});
</script>

<template>

    <Head title="Mon Compte" />

    <Container class="space-y-6">
        <!-- En-tête du profil -->
        <div class="flex flex-col space-y-4">
            <div class="flex justify-between gap-6 max-sm:gap-3 flex-wrap">
                <!-- Informations utilisateur -->
                <div
                    class="flex items-center gap-4 max-md:gap-6 max-sm:gap-2 max-[930px]:flex-wrap justify-between w-full">
                    <div class="flex items-center justify-center space-x-4">
                        <Avatar rounded="full" :source="user.avatar" :altText="user.name" size="xl" />

                        <div>
                            <h2 class="text-2xl font-bold text-primary-100">
                                {{ user.name }}
                            </h2>
                            <p class="text-primary-200">
                                {{ user.email }}
                            </p>
                            <div class="mt-2">
                                <BadgeRole :role="user.role" />
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end max-[930px]:w-full">
                        <Route route="user.edit">
                            <Btn theme="primary" size="sm" label="Éditer" tooltip="Modifier mon profil" />
                        </Route>
                    </div>
                </div>
                <div v-if="!user.is_verified">
                    <VerifyMailAlert />
                </div>
            </div>

            <!-- Trait de séparation -->
            <hr class="border-gray-300 dark:border-gray-700 my-4" />

            <!-- Section Campagnes -->
            <div class="flex flex-col items-start gap-4 my-5">
                <div class="flex justify-between gap-4 items-center w-full">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        Mes Campagnes
                        <Badge class="ml-2 uppercase" color="campaign-800">
                            Campagne
                        </Badge>
                    </h3>
                    <div>
                        <BaseTooltip tooltip="Aucune campagne en cours">
                            <Route route="">
                                <div class="indicator">
                                    <span class="indicator-item badge badge-secondary">0</span>
                                    <Btn theme="neutral" variant="glass" label="Voir mes campagnes"
                                        tooltip="Afficher toutes mes campagnes" />
                                </div>
                            </Route>
                        </BaseTooltip>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 max-sm:grid-cols-1 max-sm:gap-2 justify-items-center items-center">
                    <ModuleCard class="my-4"
                        image="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fexternal-preview.redd.it%2FgqAwVxC2dXU-5xVfOELCvNRYBotyqQH5I6QoLqQNOdE.jpg%3Fauto%3Dwebp%26s%3Deb300cd46e5373d222ef549427621df6aa44c31a&f=1&nofb=1&ipt=566b85f79c1f044372650b7fd3c0371313b4b9f8c60045bafa2f7773ba1dcb3d&ipo=images"
                        :type="{ name: 'campagne', color: 'campaign-800' }"
                        :actions="['pin', 'favorite', 'view', 'edit', 'share']" title="Ma Campagne">
                        <template #properties>
                            <Badge size="sm" color="primary"> Test </Badge>
                        </template>
                        <template #content>
                            <p>Description de la campagne</p>
                        </template>
                        <template #hoverContent>
                            <p class="text-primary-100">
                                Description détaillée de la campagne. Lorem ipsum dolor sit amet consectetur adipisicing
                                elit.
                            </p>
                        </template>
                    </ModuleCard>
                    <BaseTooltip tooltip="Créer une campagne">
                        <Route route="">
                            <Btn theme="secondary-900">
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="text-4xl text-content-light text-center fa-solid fa-plus"></i>
                                </div>
                            </Btn>
                        </Route>
                    </BaseTooltip>
                </div>
            </div>

            <!-- Section Scénarios -->
            <div class="flex flex-col items-start gap-4 my-5">
                <div class="flex justify-between gap-4 items-center w-full">
                    <h3 class="text-lg font-bold text-primary-100">
                        Mes Scénarios
                        <Badge class="ml-2 uppercase" color="scenario-800">
                            Scénario
                        </Badge>
                    </h3>
                    <div>
                        <BaseTooltip tooltip="Aucun scénario en cours">
                            <Route route="">
                                <div class="indicator">
                                    <span class="indicator-item badge badge-secondary">0</span>
                                    <Btn theme="neutral" variant="glass" size="sm" label="Voir mes scénarios"
                                        tooltip="Afficher tous mes scénarios" />
                                </div>
                            </Route>
                        </BaseTooltip>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 max-sm:grid-cols-1 max-sm:gap-2 justify-items-center items-center">
                    <ModuleCard class="my-4"
                        image="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Ftse1.mm.bing.net%2Fth%3Fid%3DOIP.KsyY2uAKnTa1N6HXbpg5swHaEI%26pid%3DApi&f=1&ipt=4d00f059f254b63c38cc6a12030cfb466843587d98f288fc5a1bfa5fd99a36bd&ipo=images"
                        :type="{ name: 'scenario', color: 'scenario-800' }"
                        :actions="['pin', 'favorite', 'view', 'edit', 'share']" title="Mon Scénario" theme="primary">
                        <template #properties>
                            <Badge size="sm" color="primary">Test</Badge>
                        </template>
                        <template #content>
                            <p class="text-primary-200">Description du scénario</p>
                        </template>
                        <template #hoverContent>
                            <p class="text-primary-100">
                                Description détaillée du scénario. Lorem ipsum dolor sit amet consectetur adipisicing
                                elit.
                            </p>
                        </template>
                    </ModuleCard>
                    <BaseTooltip tooltip="Créer un scénario">
                        <Route route="">
                            <Btn theme="secondary">
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="text-4xl text-content-light text-center fa-solid fa-plus"></i>
                                </div>
                            </Btn>
                        </Route>
                    </BaseTooltip>
                </div>
            </div>

            <!-- Section PNJ -->
            <div class="flex flex-col items-start gap-4 my-5">
                <div class="flex justify-between gap-4 items-center w-full">
                    <h3 class="text-lg font-bold text-primary-100">
                        Mes PNJ
                        <Badge class="ml-2 uppercase" color="npc-800">
                            PNJ
                        </Badge>
                    </h3>
                    <div>
                        <BaseTooltip tooltip="Aucun PNJ créé">
                            <Route route="">
                                <div class="indicator">
                                    <span class="indicator-item badge badge-secondary">0</span>
                                    <Btn theme="neutral" variant="glass" size="sm" label="Voir mes PNJ"
                                        tooltip="Afficher tous mes PNJ" />
                                </div>
                            </Route>
                        </BaseTooltip>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 max-sm:grid-cols-1 max-sm:gap-2 justify-items-center items-center">
                    <ModuleCard class="my-4"
                        image="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fi.pinimg.com%2Foriginals%2Fa7%2F4e%2F83%2Fa74e8393aa3abe1b4fd079e18517724d.jpg&f=1&nofb=1&ipt=61d0e34410ac05733ee0161fb2a1a6d767bc47b37f4b4542e8c5c8748c607d57&ipo=images"
                        :type="{ name: 'npc', color: 'npc-800' }"
                        :actions="['pin', 'favorite', 'view', 'edit', 'share']" title="Mon PNJ">
                        <template #properties>
                            <Badge size="sm" color="primary">Test</Badge>
                        </template>
                        <template #content>
                            <p class="text-primary-200">Description du PNJ</p>
                        </template>
                        <template #hoverContent>
                            <p class="text-primary-100">
                                Description détaillée du PNJ. Lorem ipsum dolor sit amet consectetur adipisicing elit.
                            </p>
                        </template>
                    </ModuleCard>
                    <BaseTooltip tooltip="Créer un PNJ">
                        <Route route="">
                            <Btn theme="secondary">
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="text-4xl text-content-light text-center fa-solid fa-plus"></i>
                                </div>
                            </Btn>
                        </Route>
                    </BaseTooltip>
                </div>
            </div>
        </div>
    </Container>
</template>
