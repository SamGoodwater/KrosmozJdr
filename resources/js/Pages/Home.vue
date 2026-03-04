<script setup>
/**
 * Home Page
 *
 * @description
 * Version de production de la page d'accueil :
 * uniquement du contenu éditorial, sans composants de démonstration.
 */
import { onMounted } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";

const { setPageTitle } = usePageTitle();

onMounted(() => {
    setPageTitle("Accueil");
});
</script>

<template>
    <main class="px-4 py-8 md:px-8 md:py-12">
        <article class="home-article mx-auto max-w-4xl bg-base-100/40 p-6 md:p-10">
            <header class="mb-8 space-y-3 text-center">
                <p class="text-xs uppercase tracking-[0.18em] text-base-content/70">KrosmozJDR</p>
                <h1 class="text-3xl font-bold md:text-4xl">Bienvenue dans le Monde des Douze</h1>
                <p class="mx-auto max-w-2xl text-base leading-relaxed text-base-content/90 md:text-lg">
                    KrosmozJDR est un projet de jeu de role inspire de l'univers de Dofus, pense pour offrir
                    des campagnes immersives, tactiques et accessibles a tous les profils de joueurs.
                </p>
            </header>

            <section class="space-y-6 text-base leading-8 text-base-content/90">
                <p>
                    Explorez des territoires mythiques, traversez des zones dangereuses et ecrivez votre propre
                    legende. Chaque session met en avant la cooperation, la strategie et l'interpretation de role,
                    avec un systeme de progression qui valorise autant la narration que le gameplay.
                </p>

                <p>
                    Le projet s'appuie sur un bestiaire riche, des classes iconiques et des mecaniques adaptees
                    au jeu de table. Que vous soyez meneur de jeu ou aventurier, vous trouverez des outils clairs
                    pour preparer des scenarii, gerer vos personnages et faire evoluer vos parties.
                </p>

                <p>
                    Cette plateforme est en evolution continue. L'objectif est de proposer une base solide,
                    elegante et coherente, afin de soutenir des campagnes longues et memorables dans
                    l'univers du Krosmoz.
                </p>
            </section>
        </article>
    </main>
</template>

<style scoped>
.home-article {
    border-radius: var(--radius-box, 0.1rem);
}
</style>
