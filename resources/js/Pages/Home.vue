<script setup>
/**
 * Home Page
 *
 * @description
 * Fallback statique lorsque la page CMS « accueil » est absente.
 * Contenu aligné sur {@see Database\Seeders\CriticalPagesSeeder::seedAccueilSections}.
 */
import { onMounted } from "vue";
import { Link } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";

const { setPageTitle } = usePageTitle();

onMounted(() => {
    setPageTitle("Accueil");
});
</script>

<template>
    <main class="px-4 py-8 md:px-8 md:py-12">
        <article class="home-article mx-auto max-w-4xl space-y-10 bg-base-100/40 p-6 md:p-10">
            <header class="space-y-3 text-center">
                <p class="text-xs uppercase tracking-[0.18em] text-base-content/70">Krosmoz JDR</p>
                <h1 class="text-3xl font-bold md:text-4xl">Le Monde des Douze, autour de la table</h1>
                <p class="mx-auto max-w-2xl text-base leading-relaxed text-base-content/90 md:text-lg">
                    Dofus rencontre le JdR tactique : tu annonces, tu lances, tu dépenses tes PA — et tu
                    racontes ta légende.
                </p>
            </header>

            <section aria-labelledby="home-beta-heading" class="space-y-3">
                <div class="alert alert-info shadow-sm">
                    <i class="fa-solid fa-flask" aria-hidden="true"></i>
                    <div class="space-y-3 text-left">
                        <h2 id="home-beta-heading" class="text-lg font-semibold">
                            Krosmoz JDR est en bêta
                        </h2>
                        <p>
                            Les règles sont jouables et le site évolue vite — mais tout n’est pas encore au
                            catalogue.
                        </p>
                        <ul class="list-disc space-y-1 pl-5">
                            <li>
                                <strong>En cours</strong> : peu de sorts, monstres et classes pleinement
                                disponibles dans les bibliothèques.
                            </li>
                            <li>
                                <strong>En progression</strong> : règles, L’Essentiel, outils du site, contenu
                                qui grossit version après version.
                            </li>
                        </ul>
                        <p>
                            Les bibliothèques se remplissent — la bêta, c’est jouer et construire en même
                            temps.
                        </p>
                    </div>
                </div>
            </section>

            <section aria-labelledby="home-game-heading" class="space-y-3">
                <h2 id="home-game-heading" class="text-2xl font-semibold">Le jeu</h2>
                <ul class="list-disc space-y-2 pl-5 leading-relaxed text-base-content/90">
                    <li>
                        <strong>Univers Dofus / Wakfu</strong> — classes iconiques, humour léger, Monde des
                        Douze.
                    </li>
                    <li>
                        <strong>Tactique au tour par tour</strong> — PA, PM, grille, choix qui comptent.
                    </li>
                    <li>
                        <strong>JdR classique</strong> — d20, progression, narration à table avec ton groupe.
                    </li>
                    <li>
                        <strong>Pas besoin de tout lire</strong> — L’Essentiel pour démarrer, le détail dans
                        les Règles.
                    </li>
                </ul>
            </section>

            <section aria-labelledby="home-platform-heading" class="space-y-3">
                <h2 id="home-platform-heading" class="text-2xl font-semibold">La plateforme</h2>
                <p class="leading-relaxed text-base-content/90">
                    Ce site, c’est ton camp de base pour jouer et préparer :
                </p>
                <ul class="list-disc space-y-2 pl-5 leading-relaxed text-base-content/90">
                    <li><strong>Règles</strong> — corpus structuré par chapitres.</li>
                    <li>
                        <strong>L’Essentiel</strong> — résumés pour jouer et trucs pour le MJ.
                    </li>
                    <li>
                        <strong>Bibliothèques</strong> — classes, sorts, monstres, équipements… catalogue en
                        cours de remplissage.
                    </li>
                    <li>
                        <strong>Recherche rapide</strong> —
                        <kbd class="kbd kbd-sm">Alt</kbd> + <kbd class="kbd kbd-sm">K</kbd> pour retrouver
                        une page ou une entité.
                    </li>
                    <li>
                        <strong>Compte</strong> — favoris, notifications, contribution selon ton rôle.
                    </li>
                </ul>
            </section>

            <section aria-labelledby="home-start-heading" class="space-y-4">
                <h2 id="home-start-heading" class="text-2xl font-semibold">Par où commencer ?</h2>

                <div class="space-y-2">
                    <h3 class="font-semibold">Nouveau·elle en JdR ou sur Krosmoz</h3>
                    <ul class="list-disc space-y-1 pl-5 leading-relaxed text-base-content/90">
                        <li>L’Essentiel — Bien démarrer</li>
                        <li>Création de personnage</li>
                        <li>Parcourir une classe en bibliothèque</li>
                    </ul>
                </div>

                <div class="space-y-2">
                    <h3 class="font-semibold">Déjà à l’aise en JdR</h3>
                    <ul class="list-disc space-y-1 pl-5 leading-relaxed text-base-content/90">
                        <li>Règles ch. 1–3 + Essentiel combat</li>
                    </ul>
                </div>

                <div class="space-y-2">
                    <h3 class="font-semibold">Meneuse ou meneur de jeu (MJ)</h3>
                    <ul class="list-disc space-y-1 pl-5 leading-relaxed text-base-content/90">
                        <li>L’Essentiel pour les rappels de table</li>
                        <li>Règles comme référence + bibliothèques pour préparer les rencontres</li>
                        <li>
                            <kbd class="kbd kbd-sm">Alt</kbd> + <kbd class="kbd kbd-sm">K</kbd> en session
                            pour retrouver une règle ou une fiche
                        </li>
                    </ul>
                </div>
            </section>

            <section aria-labelledby="home-contribute-heading" class="space-y-3">
                <h2 id="home-contribute-heading" class="text-2xl font-semibold">Contribuer</h2>
                <p class="leading-relaxed text-base-content/90">
                    La bêta avance grâce aux tables et aux contributeur·rice·s. Tu peux aider de plusieurs
                    façons :
                </p>
                <ul class="list-disc space-y-2 pl-5 leading-relaxed text-base-content/90">
                    <li><strong>Retours de jeu</strong> — playtest, relecture, signalement de coquilles</li>
                    <li>
                        <strong>Contenu</strong> — sorts, monstres, classes, textes (selon les droits sur ton
                        compte)
                    </li>
                    <li>
                        <strong>Communauté</strong> —
                        <Link href="/pages/nous-rejoindre" class="link link-primary">Nous rejoindre</Link>
                        pour Discord, GitHub et la demande d’accès éditeur
                    </li>
                </ul>
                <p class="leading-relaxed text-base-content/90">
                    Tu peux aussi passer par la page
                    <Link href="/contribuer" class="link link-primary">Contribuer</Link> — elle te redirige
                    vers les bonnes ressources.
                </p>
            </section>
        </article>
    </main>
</template>

<style scoped lang="scss">
.home-article {
    border-radius: var(--radius-box, 0.1rem);
}
</style>
