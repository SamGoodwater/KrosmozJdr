<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

// Composants existants
import Avatar from "@/Pages/Atoms/images/Avatar.vue";
import Btn from "@/Pages/Atoms/actions/Btn.vue";
import Route from "@/Pages/Atoms/text/Route.vue";
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
  <div class="container mx-auto p-4">
    <!-- Card pour le dashboard -->
    <Card rounded="lg" bgColor="bg-secondary-500/10" blur="lg">
        <template>
            <!-- Informations utilisateur -->
            <div class="flex items-center space-x-4">
                <!-- Avatar : on passe la source (image) et le texte alternatif (nom) -->
                <Avatar rounded="full" :source="user.image" :altText="user.name" size="xl" />

                <div>
                <!-- Nom/Pseudo -->
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ user.name }}
                </h2>
                <!-- Adresse mail -->
                <p class="text-gray-600 dark:text-gray-300">{{ user.email }}</p>
                <!-- Rôle affiché dans un badge -->
                <div class="mt-2">
                    <Badge theme="outline" color="primary" size="sm">
                    {{ user.role }}
                    </Badge>
                </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="mt-6 flex flex-col sm:flex-row gap-4">
                <!-- Bouton pour éditer le dashboard -->
                <!-- Ici, on suppose que vous avez une route nommée "user.edit" pour l'édition -->
                <Btn theme="sm primary glass">
                    <Template #label>
                        <Route route="" >Éditer</Route>
                    </Template>
                </Btn>

                <!-- Bouton pour se déconnecter -->
                <!-- La déconnexion s'effectue généralement via une requête POST vers /logout -->
                <Route route="logout" method="post">
                    <Btn theme="simple link md" label="Se déconnecter" />
                </Route>
            </div>
        </template>
    </Card>
  </div>
</template>
