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
import Card from "@/Pages/Atoms/panels/Card.vue";

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
                        <div role="alert" class="alert alert-warning">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 shrink-0 stroke-current"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                            <span>Mail non vérifié.</span>
                            <div>
                                <Route route="">
                                    <Btn
                                        theme="sm neutral glass"
                                        label="Vérifier mon mail"
                                    />
                                </Route>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <Route route="">
                        <Btn theme="md primary glass" label="Éditer" />
                    </Route>
                </div>
            </div>

            <!-- Trait de séparation -->
            <hr class="border-gray-300 dark:border-gray-700 my-4" />

            <!-- Mes Campagnes -->
            <div class="flex flex-col items-start gap-4 my-5">
                <div>
                    <h3
                        class="text-lg font-bold text-gray-900 dark:text-gray-100"
                    >
                        Mes Campagnes
                        <Badge class="ml-2 uppercase" color="campaign-800">
                            Campagne
                        </Badge>
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        0 campagnes en cours
                    </p>
                    <div>
                        <Card theme="w-62" class="m-6">
                            <Badge size="sm" class="absolute top-[-16px] uppercase left-1 z-[-1]" color="campaign-800">Campagne</Badge>
                            <div class="flex gap-4 z-2">
                                <!-- Image à gauche -->
                                <div class="w-24 bg-gray-200 dark:bg-gray-800 rounded-lg">
                                    <img src="https://solomonk.fr/img/items/16/53.svg" alt="Campaign image" class="w-full h-full object-cover rounded-lg" />
                                </div>

                                <!-- Contenu à droite -->
                                <div class="flex flex-col flex-1 justify-between">
                                    <!-- Ligne 1: Boutons icônes -->
                                    <div class="flex gap-2">
                                        <button class="btn btn-circle btn-ghost btn-xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </button>
                                        <button class="btn btn-circle btn-ghost btn-xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Ligne 2: Titre -->
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">Titre de la campagne</h4>
                                    </div>

                                    <!-- Ligne 3: Propriétés -->
                                    <div class="flex gap-2 text-sm text-gray-600 dark:text-gray-400">
                                        <span>4 joueurs</span>
                                        <span>•</span>
                                        <span>Niveau 5</span>
                                        <span>•</span>
                                        <span>Fantasy</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Bloc de texte en dessous -->
                            <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                            </div>

                            <template #hover>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Iure nulla optio, doloribus a nam magnam quaerat perspiciatis laudantium quod, voluptatem velit est, repellendus voluptate accusamus consequatur saepe repellat! Nam, culpa? Pariatur earum voluptate, beatae error eaque praesentium ut vitae, blanditiis expedita distinctio repellendus ad veniam facere nulla repellat quo corrupti!</p>
                            </template>
                        </Card>
                    </div>
                </div>
                <div class="flex gap-4">
                    <Route route="">
                        <Btn
                            theme="sm primary glass"
                            label="Créer une campagne"
                        />
                    </Route>
                    <Route route="">
                        <Btn
                            theme="sm neutral glass"
                            label="Voir mes campagnes"
                        />
                    </Route>
                </div>
            </div>

            <!-- Mes Scénarios -->
            <div class="flex flex-col items-start gap-4 my-5">
                <div>
                    <h3
                        class="text-lg font-bold text-gray-900 dark:text-gray-100"
                    >
                        Mes Scénarios
                        <Badge class="ml-2 uppercase" color="scenario-800">
                            Scénario
                        </Badge>
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        0 scénario en cours
                    </p>
                </div>
                <div class="flex gap-4">
                    <Route route="">
                        <Btn
                            theme="sm primary glass"
                            label="Créer un scénario"
                        />
                    </Route>
                    <Route route="">
                        <Btn
                            theme="sm neutral glass"
                            label="Voir mes scénarios"
                        />
                    </Route>
                </div>
            </div>

            <!-- Mes PNJ -->
            <div class="flex flex-col items-start gap-4 my-5">
                <div>
                    <h3
                        class="text-lg font-bold text-gray-900 dark:text-gray-100"
                    >
                        Mes PNJ
                        <Badge class="ml-2 uppercase" color="npc-800"> PNJ </Badge>
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300">0 pnj créé</p>
                </div>
                <div class="flex gap-4">
                    <Route route="">
                        <Btn theme="sm primary glass" label="Créer un PNJ" />
                    </Route>
                    <Route route="">
                        <Btn theme="sm neutral glass" label="Voir mes PNJ" />
                    </Route>
                </div>
            </div>

            <div class="flex justify-end gap-4 flex-wrap items-center">
                <div>
                    <Route route="logout" method="post">
                        <Btn theme="neutral outline sm" label="Se déconnecter" />
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
