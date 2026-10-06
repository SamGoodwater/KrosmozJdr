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
import Route from "@/Pages/Atoms/action/Route.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
</script>

<template>
    <div class="flex flex-col items-end gap-2 text-right">
        <Tooltip content="S'identifier à l'aide de son compte" placement="left">
            <Route
                route="login"
                class="btn btn-sm btn-ghost inline-flex min-h-6 min-w-[5.5rem] items-center justify-end px-3"
                target="_self"
                aria-label="Se connecter"
            >
                Se connecter
            </Route>
        </Tooltip>

        <Tooltip content="Créer un compte" placement="left">
            <Route
                route="register"
                class="btn btn-sm btn-link inline-flex min-h-6 min-w-[5.5rem] items-center justify-end px-3"
                target="_self"
                aria-label="S'inscrire"
            >
                S'inscrire
            </Route>
        </Tooltip>
    </div>
</template>
