<script setup>
import Btn from "@/Pages/Atoms/actions/Btn.vue";
import Route from "@/Pages/Atoms/text/Route.vue";
</script>

<template>
    <div class="flex flex-col text-right">
        <Route route="login" class="pt-1 px-4">
            <Btn
                theme="neutral link md"
                label="Se connecter"
                tooltipPosition="left"
                tooltip="S'identifier à l'aide de son compte"
            />
        </Route>

        <Route route="register" class="pb-1 px-4">
            <Btn
                theme="neutral link md"
                label="S'inscrire"
                tooltipPosition="left"
                tooltip="Créer un compte"
            />
        </Route>
    </div>
</template>
