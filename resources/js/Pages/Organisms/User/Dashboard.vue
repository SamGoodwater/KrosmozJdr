<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

// Composants existants
import Avatar from "@/Pages/Atoms/images/Avatar.vue";
import Btn from "@/Pages/Atoms/actions/Btn.vue";
import Route from "@/Pages/Atoms/text/Route.vue";
import BadgeRole from "@/Pages/Organisms/User/Molecules/badgeRole.vue";
import Container from "@/Pages/Atoms/panels/Container.vue";
import Badge from "@/Pages/Atoms/text/Badge.vue";
import ModuleCard from "@/Pages/Molecules/modules/ModuleCard.vue";
import Card from "@/Pages/Atoms/panels/Card.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";

// Récupération des données partagées par Inertia
const page = usePage();
const user = ref(page.props.user);
const verifiedEmail = ref(page.props.verifiedEmail);
</script>

<template>
    <!-- Définition du titre de la page -->
    <Head title="Mon Compte" />

    <!-- Container principal -->
    <Container>
        <div class="flex flex-col space-y-4">
            <div
                class="flex justify-between gap-6 max-sm:gap-2 flex-wrap ml-20 max-sm:ml-0"
            >
                <!-- Informations utilisateur -->
                <div class="flex items-center gap-8 max-md:gap-6 max-sm:gap-2">
                    <div class="flex items-center justify-center space-x-4">
                        <!-- Avatar : on passe la source (image) et le texte alternatif (nom) -->
                        <Avatar
                            rounded="full"
                            :source="user.image"
                            :altText="user.name"
                            size="xl"
                        />

                        <div>
                            <!-- Nom/Pseudo -->
                            <h2
                                class="text-2xl font-bold text-gray-900 dark:text-gray-100"
                            >
                                {{ user.name }}
                            </h2>
                            <!-- Adresse mail -->
                            <p class="text-gray-600 dark:text-gray-300">
                                {{ user.email }}
                            </p>
                            <!-- Rôle affiché dans un badge -->
                            <div class="mt-2">
                                <BadgeRole :role="user.role" />
                            </div>
                        </div>
                    </div>
                    <div>
                        <div role="alert" class="alert alert-warning text-content-light py-2 px-4">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>Mail non vérifié.</span>
                            <div>
                                <Route route="">
                                    <Btn
                                        theme="link" class="text-secondary-950"
                                        label="Vérifier mon mail"
                                    />
                                </Route>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <Route route="user.edit">
                        <Btn theme="md primary glass" label="Éditer" />
                    </Route>
                </div>
            </div>

            <!-- Trait de séparation -->
            <hr class="border-gray-300 dark:border-gray-700 my-4" />

            <!-- Mes Campagnes -->
            <div class="flex flex-col items-start gap-4 my-5">
                <div class="flex justify-between gap-4 items-center w-full">
                    <h3
                        class="text-lg font-bold text-gray-900 dark:text-gray-100"
                    >
                        Mes Campagnes
                        <Badge class="ml-2 uppercase" color="campaign-800">
                            Campagne
                        </Badge>
                    </h3>
                    <div>
                        <Tooltip>
                            <Route route="">
                                <div class="indicator">
                                    <span
                                    class="indicator-item badge badge-secondary"
                                    >0</span
                                >
                                <Btn
                                    theme="sm neutral glass"
                                    label="Voir mes campagnes"
                                    />
                                </div>
                            </Route>
                            <template #content>
                                <p>Aucune campagne en cours</p>
                            </template>
                        </Tooltip>
                    </div>
                </div>
                <div
                    class="grid grid-cols-2 gap-4 max-sm:grid-cols-1 max-sm:gap-2 justify-items-center items-center"
                >
                    <ModuleCard
                        class="my-4"
                        image="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fexternal-preview.redd.it%2FgqAwVxC2dXU-5xVfOELCvNRYBotyqQH5I6QoLqQNOdE.jpg%3Fauto%3Dwebp%26s%3Deb300cd46e5373d222ef549427621df6aa44c31a&f=1&nofb=1&ipt=566b85f79c1f044372650b7fd3c0371313b4b9f8c60045bafa2f7773ba1dcb3d&ipo=images"
                        :type="{ name: 'campagne', color: 'campaign-800' }"
                        :actions="['pin', 'favorite', 'view', 'edit', 'share']"
                        title="Ma Campagne"
                    >
                        <template #properties>
                            <Badge size="sm" color="primary"> Test </Badge>
                        </template>
                        <template #content>
                            <p>Description de la campagne</p>
                        </template>
                        <template #hoverContent>
                            <p>
                                Description détaillée de la campagne. Lorem
                                ipsum dolor sit amet consectetur adipisicing
                                elit. Quisquam, quos.
                            </p>
                        </template>
                    </ModuleCard>
                    <Tooltip>
                        <Route route="">
                          <Btn BgColor="secondary-900">
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="text-4xl text-content-light text-center fa-solid fa-plus"></i>
                                    </div>
                            </Btn>
                            </Route>
                        <template #content> Créer une campagne </template>
                    </Tooltip>
                </div>
            </div>

            <!-- Mes Scénarios -->
            <div class="flex flex-col items-start gap-4 my-5">
                <div class="flex justify-between gap-4 items-center w-full">
                    <h3
                        class="text-lg font-bold text-gray-900 dark:text-gray-100"
                    >
                        Mes Scénarios
                        <Badge class="ml-2 uppercase" color="scenario-800">
                            Scénario
                        </Badge>
                    </h3>
                    <div>
                        <Tooltip>
                            <Route route="">
                                <div class="indicator">
                                    <span class="indicator-item badge badge-secondary">0</span>
                                    <Btn
                                        theme="sm neutral glass"
                                        label="Voir mes scénarios"
                                    />
                                </div>
                            </Route>
                            <template #content>
                                <p>Aucun scénario en cours</p>
                            </template>
                        </Tooltip>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 max-sm:grid-cols-1 max-sm:gap-2 justify-items-center items-center">
                    <ModuleCard
                        class="my-4"
                        image="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Ftse1.mm.bing.net%2Fth%3Fid%3DOIP.KsyY2uAKnTa1N6HXbpg5swHaEI%26pid%3DApi&f=1&ipt=4d00f059f254b63c38cc6a12030cfb466843587d98f288fc5a1bfa5fd99a36bd&ipo=images"
                        :type="{ name: 'scenario', color: 'scenario-800' }"
                        :actions="['pin', 'favorite', 'view', 'edit', 'share']"
                        title="Mon Scénario"
                    >
                        <template #properties>
                            <Badge size="sm" color="primary">Test</Badge>
                        </template>
                        <template #content>
                            <p>Description du scénario</p>
                        </template>
                        <template #hoverContent>
                            <p>Description détaillée du scénario. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.</p>
                        </template>
                    </ModuleCard>
                    <Tooltip>

                            <Route route="">
                           <Btn BgColor="secondary-900">
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="text-4xl text-content-light text-center fa-solid fa-plus"></i>
                                    </div>
                            </Btn>
                            </Route>

                        <template #content>Créer un scénario</template>
                    </Tooltip>
                </div>
            </div>

            <!-- Mes PNJ -->
            <div class="flex flex-col items-start gap-4 my-5">
                <div class="flex justify-between gap-4 items-center w-full">
                    <h3
                        class="text-lg font-bold text-gray-900 dark:text-gray-100"
                    >
                        Mes PNJ
                        <Badge class="ml-2 uppercase" color="npc-800">
                            PNJ
                        </Badge>
                    </h3>
                    <div>
                        <Tooltip>
                            <Route route="">
                                <div class="indicator">
                                    <span class="indicator-item badge badge-secondary">0</span>
                                    <Btn
                                        theme="sm neutral glass"
                                        label="Voir mes PNJ"
                                    />
                                </div>
                            </Route>
                            <template #content>
                                <p>Aucun PNJ créé</p>
                            </template>
                        </Tooltip>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 max-sm:grid-cols-1 max-sm:gap-2 justify-items-center items-center">
                    <ModuleCard
                        class="my-4"
                        image="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fi.pinimg.com%2Foriginals%2Fa7%2F4e%2F83%2Fa74e8393aa3abe1b4fd079e18517724d.jpg&f=1&nofb=1&ipt=61d0e34410ac05733ee0161fb2a1a6d767bc47b37f4b4542e8c5c8748c607d57&ipo=images"
                        :type="{ name: 'npc', color: 'npc-800' }"
                        :actions="['pin', 'favorite', 'view', 'edit', 'share']"
                        title="Mon PNJ"
                    >
                        <template #properties>
                            <Badge size="sm" color="primary">Test</Badge>
                        </template>
                        <template #content>
                            <p>Description du PNJ</p>
                        </template>
                        <template #hoverContent>
                            <p>Description détaillée du PNJ. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.</p>
                        </template>
                    </ModuleCard>
                    <Tooltip>
                        <Route route="">
                            <Btn BgColor="secondary-900">
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="text-4xl text-content-light text-center fa-solid fa-plus"></i>
                                    </div>
                            </Btn>
                        </Route>
                        <template #content>Créer un PNJ</template>
                    </Tooltip>
                </div>
            </div>

            <div class="flex justify-end gap-4 flex-wrap items-center">
                <div>
                    <Route route="logout" method="post">
                        <Btn
                            theme="neutral outline sm"
                            label="Se déconnecter"
                        />
                    </Route>
                </div>
                <div>
                    <Route route="">
                        <Btn
                            theme="neutral ing outline sm"
                            label="Obtenir mes informations"
                        />
                    </Route>
                </div>
                <div>
                    <Route route="">
                        <Btn
                            theme="error outline sm"
                            label="Supprimer le compte"
                        />
                    </Route>
                </div>
            </div>
        </div>
    </Container>
</template>
