<script setup>
import Btn from '@/Pages/Components/actions/Btn.vue';
import Route from '@/Pages/Components/text/Route.vue';
</script>

<template>
    <div class="flex flex-col text-center">
        <p>Vous êtes connecter</p>
        <Route route='register.show' class="pb-1 px-4">
            <Btn theme="simple link md" label="S'inscrire" tooltipPosition="left" tooltip="Créer un compte" />
        </Route>
    </div>
</template>
