<script setup>
/**
* Dashboard component (Atomic Design refonte)
* Affiche les infos utilisateur et des exemples de modules (campagnes, scénarios, PNJ) pour tests de design.
*
* - Affichage profil utilisateur (avatar, nom, email, rôle, bouton éditer, alerte mail)
* - Sections Campagnes, Scénarios, PNJ (titre, badge, bouton voir/créer, EntityCard de test)
* - Utilise les atoms/molecules à jour (chemins, API)
* - Pas de logique métier complexe, juste du design
*/
import { Head, usePage } from "@inertiajs/vue3";
import { computed, onMounted } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";

// Atoms & Molecules (nouveaux chemins)
import Avatar from '@/Pages/Atoms/data-display/Avatar.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Route from '@/Pages/Atoms/action/Route.vue';
import BadgeRole from '@/Pages/Molecules/user/BadgeRole.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import EntityCard from '@/Pages/Molecules/entity/EnityCard.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import VerifyMailAlert from '@/Pages/Molecules/user/VerifyMailAlert.vue';

const page = usePage();
// Le user est passé via page.props.user (UserResource)
// On utilise computed pour réactivité
// Note: Si les données sont dans user.data (pagination), on les extrait
const user = computed(() => {
    const userData = page.props.user || {};
    // Si les données sont dans user.data (cas de pagination), on les extrait
    if (userData.data && typeof userData.data === 'object') {
        return userData.data;
    }
    return userData;
});
const { setPageTitle } = usePageTitle();

onMounted(() => {
    setPageTitle('Mon Compte');
});
</script>

<template>

    <Head title="Mon Compte" />
    <Container class="space-y-6">
        <!-- Profil utilisateur -->
        <div class="flex flex-col space-y-4">
            <div class="flex justify-between gap-6 max-sm:gap-3 flex-wrap">
                <!-- Infos utilisateur -->
                <div
                    class="flex items-center gap-4 max-md:gap-6 max-sm:gap-2 max-[930px]:flex-wrap justify-between w-full">
                    <div class="flex items-center justify-center space-x-4">
                        <Avatar 
                            :src="user?.avatar || ''" 
                            :label="user?.name || 'Utilisateur'" 
                            :alt="user?.name || 'Utilisateur'" 
                            size="xl" 
                            rounded="full" 
                        />
                        <div>
                            <h2 class="text-2xl font-bold text-primary-100">{{ user?.name || 'Utilisateur' }}</h2>
                            <p class="text-primary-200">{{ user?.email || 'email@example.com' }}</p>
                            <div class="mt-2">
                                <BadgeRole :role="user?.role_name || 'user'" />
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end max-[930px]:w-full">
                        <Tooltip content="Modifier mon profil" placement="top">
                            <Route route="user.edit">
                                <Btn color="primary" size="sm">Éditer</Btn>
                            </Route>
                        </Tooltip>
                    </div>
                </div>
                <div v-if="user && !user.is_verified">
                    <VerifyMailAlert />
                </div>
            </div>
            <hr class="border-gray-300 dark:border-gray-700 my-4" />

            <!-- Section Campagnes -->
            <div class="flex flex-col items-start gap-4 my-5">
                <div class="flex justify-between gap-4 items-center w-full">
                    <h3 class="text-lg font-bold text-primary-100">
                        Mes Campagnes
                        <Badge class="ml-2 uppercase" color="primary">Campagne</Badge>
                    </h3>
                    <div>
                        <Tooltip content="Aucune campagne en cours">
                            <Route route="">
                                <Btn color="neutral" variant="glass" size="sm">Voir mes campagnes</Btn>
                            </Route>
                        </Tooltip>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 max-sm:grid-cols-1 max-sm:gap-2 justify-items-center items-center">
                    <EntityCard class="my-4" title="Ma Campagne"
                        image="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fexternal-preview.redd.it%2FgqAwVxC2dXU-5xVfOELCvNRYBotyqQH5I6QoLqQNOdE.jpg%3Fauto%3Dwebp%26s%3Deb300cd46e5373d222ef549427621df6aa44c31a&f=1&nofb=1&ipt=566b85f79c1f044372650b7fd3c0371313b4b9f8c60045bafa2f7773ba1dcb3d&ipo=images"
                        :type="{ name: 'campagne', color: 'primary' }"
                        :actions="['pin', 'favorite', 'view', 'edit', 'share']">
                        <template #properties>
                            <Badge size="sm" color="primary">Test</Badge>
                        </template>
                        <template #content>
                            <p>Description de la campagne</p>
                        </template>
                        <template #hoverContent>
                            <p class="text-primary-100">Description détaillée de la campagne. Lorem ipsum dolor sit amet
                                consectetur adipisicing elit.</p>
                        </template>
                    </EntityCard>
                    <Tooltip content="Créer une campagne">
                        <Route route="">
                            <Btn color="secondary" circle>
                                <i class="text-2xl fa-solid fa-plus"></i>
                            </Btn>
                        </Route>
                    </Tooltip>
                </div>
            </div>

            <!-- Section Scénarios -->
            <div class="flex flex-col items-start gap-4 my-5">
                <div class="flex justify-between gap-4 items-center w-full">
                    <h3 class="text-lg font-bold text-primary-100">
                        Mes Scénarios
                        <Badge class="ml-2 uppercase" color="primary">Scénario</Badge>
                    </h3>
                    <div>
                        <Tooltip content="Aucun scénario en cours">
                            <Route route="">
                                <Btn color="neutral" variant="glass" size="sm">Voir mes scénarios</Btn>
                            </Route>
                        </Tooltip>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 max-sm:grid-cols-1 max-sm:gap-2 justify-items-center items-center">
                    <EntityCard class="my-4" title="Mon Scénario"
                        image="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Ftse1.mm.bing.net%2Fth%3Fid%3DOIP.KsyY2uAKnTa1N6HXbpg5swHaEI%26pid%3DApi&f=1&ipt=4d00f059f254b63c38cc6a12030cfb466843587d98f288fc5a1bfa5fd99a36bd&ipo=images"
                        :type="{ name: 'scenario', color: 'primary' }"
                        :actions="['pin', 'favorite', 'view', 'edit', 'share']">
                        <template #properties>
                            <Badge size="sm" color="primary">Test</Badge>
                        </template>
                        <template #content>
                            <p class="text-primary-200">Description du scénario</p>
                        </template>
                        <template #hoverContent>
                            <p class="text-primary-100">Description détaillée du scénario. Lorem ipsum dolor sit amet
                                consectetur adipisicing elit.</p>
                        </template>
                    </EntityCard>
                    <Tooltip content="Créer un scénario">
                        <Route route="">
                            <Btn color="secondary" circle>
                                <i class="text-2xl fa-solid fa-plus"></i>
                            </Btn>
                        </Route>
                    </Tooltip>
                </div>
            </div>

            <!-- Section PNJ -->
            <div class="flex flex-col items-start gap-4 my-5">
                <div class="flex justify-between gap-4 items-center w-full">
                    <h3 class="text-lg font-bold text-primary-100">
                        Mes PNJ
                        <Badge class="ml-2 uppercase" color="primary">PNJ</Badge>
                    </h3>
                    <div>
                        <Tooltip content="Aucun PNJ créé">
                            <Route route="">
                                <Btn color="neutral" variant="glass" size="sm">Voir mes PNJ</Btn>
                            </Route>
                        </Tooltip>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 max-sm:grid-cols-1 max-sm:gap-2 justify-items-center items-center">
                    <EntityCard class="my-4" title="Mon PNJ"
                        image="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fi.pinimg.com%2Foriginals%2Fa7%2F4e%2F83%2Fa74e8393aa3abe1b4fd079e18517724d.jpg&f=1&nofb=1&ipt=61d0e34410ac05733ee0161fb2a1a6d767bc47b37f4b4542e8c5c8748c607d57&ipo=images"
                        :type="{ name: 'npc', color: 'primary' }"
                        :actions="['pin', 'favorite', 'view', 'edit', 'share']">
                        <template #properties>
                            <Badge size="sm" color="primary">Test</Badge>
                        </template>
                        <template #content>
                            <p class="text-primary-200">Description du PNJ</p>
                        </template>
                        <template #hoverContent>
                            <p class="text-primary-100">Description détaillée du PNJ. Lorem ipsum dolor sit amet
                                consectetur adipisicing elit.</p>
                        </template>
                    </EntityCard>
                    <Tooltip content="Créer un PNJ">
                        <Route route="">
                            <Btn color="secondary" circle>
                                <i class="text-2xl fa-solid fa-plus"></i>
                            </Btn>
                        </Route>
                    </Tooltip>
                </div>
            </div>
        </div>
    </Container>
</template>
