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
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
</script>

<template>
    <div class="flex flex-col text-right">
        <Route route="login" class="pt-1 px-4" target="_self">
            <Tooltip content="S'identifier à l'aide de son compte" placement="left">
                <Btn
                    color="neutral"
                    size="xs"
                    content="Se connecter"
                />
            </Tooltip>
        </Route>

        <Route route="register" class="pb-1 px-4" target="_self">
            <Tooltip content="Créer un compte" placement="left">
                <Btn
                    color="neutral"
                    variant="link"
                    size="xs"
                    content="S'inscrire"
                />
            </Tooltip>
        </Route>
    </div>
</template>
