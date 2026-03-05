<script setup>
/**
 * User Create Page
 * 
 * @description
 * Page de création d'utilisateur (admin uniquement)
 * 
 * @props {Object} roles - Liste des rôles disponibles
 * @props {Array} notificationChannels - Canaux de notification disponibles
 */
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Route from '@/Pages/Atoms/action/Route.vue';
</script>
<template>
    <div class="container mx-auto p-4">
        <div class="mb-3">
            <Route route="user.index">
                <Btn color="neutral" variant="ghost" size="sm" class="gap-2">
                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                    Retour à la liste
                </Btn>
            </Route>
        </div>
        <h1 class="text-2xl font-bold mb-6">Créer un utilisateur</h1>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <p class="text-center text-gray-500">
                    Formulaire de création d'utilisateur en cours de développement...
                </p>
            </div>
        </div>
    </div>
</template>
<style scoped lang="scss">

</style>

