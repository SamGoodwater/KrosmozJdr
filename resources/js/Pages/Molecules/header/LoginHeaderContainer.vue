<script setup>
/**
* LoginHeaderContainer Molecule (Header Auth - Utilisateur non connecté)
*
* @description
* Molécule du design system KrosmozJDR pour l'affichage du header utilisateur non connecté.
* - À placer dans Molecules/header/ (atomicité, séparation des responsabilités)
* - Utilisé exclusivement par Layouts/Header.vue pour la section droite du header quand l'utilisateur n'est PAS connecté
* - Gère l'affichage des boutons de connexion/inscription, etc.
* - N'inclut aucune logique métier globale du header (seulement l'affichage auth)
* - Utilise les atoms/molecules du design system (Btn, Dropdown, etc.)
*
* @see Layouts/Header.vue (intégration)
*
* @props {Aucune} (récupère l'état via usePage)
* @slot default - (non utilisé)
*
* @note Ce composant ne doit être utilisé que dans le header principal.
* @note Respecte la philosophie Atomic Design (niveau molecule, composition d'atoms).
*/
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
</script>

<template>
    <div class="flex flex-col text-right">
        <Route route="login" class="pt-1 px-4">
            <Btn color="neutral" variant="link" size="md" content="Se connecter" tooltip_placement="left"
                :tooltip="'S\'identifier à l\'aide de son compte'" />
        </Route>

        <Route route="register" class="pb-1 px-4">
            <Btn color="neutral" variant="link" size="md" content="S'inscrire" tooltip_placement="left"
                :tooltip="'Créer un compte'" />
        </Route>
    </div>
</template>
