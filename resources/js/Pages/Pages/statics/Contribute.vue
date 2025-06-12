/**
* Contribute component (Atomic Design refonte)
* Affiche les moyens de contribuer au projet (Nextcloud, Discord, GitHub).
* Utilise les atoms à jour pour la cohérence du design system.
*/
<script setup>
import { onMounted } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
// Atoms à jour
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Image from '@/Pages/Atoms/data-display/Image.vue';

const { setPageTitle } = usePageTitle();

onMounted(() => {
    setPageTitle('Contribuer');
});
</script>

<template>
    <Container>
        <div class="mx-auto prose prose-state prose-a:text-content max-sm:prose-sm lg:prose-lg">
            <div
                class="absolute inset-0 bg-[url('/storage/logos/logo_project.png')] bg-no-repeat bg-center opacity-10 blur-lg">
            </div>

            <!-- Introduction -->
            <section class="mb-10 relative">
                <div class="relative">
                    <h2 class="text-xl font-bold text-title mb-4">
                        Rejoignez l'aventure collaborative
                    </h2>
                    <p class="text-content">
                        Le projet Krosmoz JDR est porté par une communauté de passionnés, et votre contribution peut
                        faire toute la différence. Que vous souhaitiez partager vos idées, participer au développement
                        ou simplement suivre l'évolution du projet, plusieurs options s'offrent à vous.
                    </p>
                </div>
            </section>

            <!-- Point 1: Nextcloud -->
            <section
                class="mb-10 flex flex-col md:flex-row items-center relative transition-all duration-200 hover:scale-[1.01]">
                <div class="relative md:mr-8">
                    <Image src="/storage/logos/logo_project.png" alt="Logo Nextcloud" size="md"
                        class="w-20 md:w-40 max-sm:hidden mx-auto md:mx-0 drop-shadow-lg transition-transform duration-200 hover:scale-105" />
                </div>
                <div class="relative">
                    <h3 class="text-lg font-bold text-title mb-4">
                        1. Plateforme collaborative : Projet Krosmoz JDR
                    </h3>
                    <p class="mb-4 text-content">
                        Nous avons mis en place une plateforme collaborative auto-hébergée basée sur
                        <a href="https://project.krosmoz-jdr.fr" target="_blank"
                            class="hover:text-primary-200 text-primary-300 no-underline transition-colors duration-200"
                            rel="noopener noreferrer">Nextcloud</a>. Cette plateforme permet :
                    </p>
                    <ul class="list-disc list-inside mb-4 text-content">
                        <li>De partager des fichiers liés au projet.</li>
                        <li>De discuter des règles et des systèmes de jeu.</li>
                        <li>De consulter et d'éditer les règles sur un document commun.</li>
                        <li>Etc.</li>
                    </ul>
                    <p class="text-content">
                        Vos données sont protégées : elles ne sont ni divulguées ni utilisées à d'autres fins. Pour
                        accéder à la plateforme, il y a besoin d'une inscription.
                    </p>
                    <Btn color="primary" variant="glass" class="mt-4" as="a" href="https://project.krosmoz-jdr.fr"
                        target="_blank" rel="noopener noreferrer">
                        Rejoindre Projet Krosmoz JDR
                    </Btn>
                </div>
            </section>

            <!-- Point 2: Discord -->
            <section
                class="mb-10 flex flex-col md:flex-row items-center relative transition-all duration-200 hover:scale-[1.01]">
                <div class="relative md:mr-8">
                    <Image src="/storage/logos/autres/discord.png" alt="Logo Discord" size="md"
                        class="w-20 md:w-40 max-sm:hidden mx-auto md:mx-0 drop-shadow-lg transition-transform duration-200 hover:scale-105" />
                </div>
                <div class="relative">
                    <h3 class="text-lg font-bold text-title mb-4">
                        2. Rejoignez-nous sur Discord
                    </h3>
                    <p class="text-content">
                        Pour suivre les nouveautés du projet et échanger avec d'autres passionnés du jeu de rôle dans
                        l'univers de Dofus, rendez-vous sur notre serveur Discord.
                    </p>
                    <Btn color="primary" variant="glass" class="mt-4" as="a"
                        href="https://discord.com/invite/XVu4VWFskj" target="_blank" rel="noopener noreferrer">
                        Rejoindre le Discord
                    </Btn>
                </div>
            </section>

            <!-- Point 3: Code source -->
            <section
                class="mb-10 flex flex-col md:flex-row items-center relative transition-all duration-200 hover:scale-[1.01]">
                <div class="relative md:mr-8">
                    <Image src="/storage/logos/autres/github.png" alt="Logo GitHub" size="md"
                        class="w-20 md:w-40 max-sm:hidden mx-auto md:mx-0 drop-shadow-lg transition-transform duration-200 hover:scale-105" />
                </div>
                <div class="relative">
                    <h3 class="text-lg font-bold text-title mb-4">
                        3. Contribuez au développement
                    </h3>
                    <p class="mb-4 text-content">
                        Le code source du site est libre et accessible à tous sur
                        <a href="https://github.com/SamGoodwater/KrosmozJdr"
                            class="hover:text-primary-200 text-primary-300 no-underline transition-colors duration-200"
                            target="_blank" rel="noopener noreferrer">GitHub</a>. Le projet repose sur :
                    </p>
                    <ul class="list-disc list-inside mb-4 text-content">
                        <li>Un backend en Laravel.</li>
                        <li>Un frontend en VueJS.</li>
                    </ul>
                    <p class="text-content">
                        Nous accueillons avec plaisir toutes les contributions, qu'elles concernent des fonctionnalités,
                        des corrections ou des idées d'amélioration. Voir le projet sur GitHub :
                        <a href="https://github.com/SamGoodwater/KrosmozJdr"
                            class="hover:text-primary-200 text-primary-300 no-underline transition-colors duration-200"
                            target="_blank" rel="noopener noreferrer">Consulter le dépôt</a>.
                    </p>
                    <Btn color="primary" variant="glass" class="mt-4" as="a"
                        href="https://github.com/SamGoodwater/KrosmozJdr" target="_blank" rel="noopener noreferrer">
                        Se diriger vers GitHub
                    </Btn>
                </div>
            </section>

            <!-- Closing -->
            <section class="relative">
                <div class="relative">
                    <h3 class="text-lg font-bold text-title mb-4">
                        Votre contribution compte
                    </h3>
                    <p class="text-content">
                        Chaque geste, qu'il soit petit ou grand, nous aide à faire grandir Krosmoz JDR. Merci de faire
                        partie de cette aventure !
                    </p>
                </div>
            </section>
        </div>
    </Container>
</template>

<style scoped></style>
