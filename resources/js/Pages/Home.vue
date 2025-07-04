<script setup>
/**
 * Home component (refonte Atomic Design)
 * Page d'accueil KrosmozJDR : intro, démo composants, présentation projet.
 *
 * - Utilise la molecule Hero pour l'intro
 * - Container pour le contenu principal
 * - Démo boutons/icônes inline (à supprimer plus tard)
 * - Présentation projet en prose
 *
 * @author
 */
import { ref, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";

// Molecules
import Hero from "@/Pages/Molecules/navigation/Hero.vue";
// Atoms
import Container from "@/Pages/Atoms/data-display/Container.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";

// Données partagées
const page = usePage();
const { setPageTitle } = usePageTitle();

onMounted(() => {
    setPageTitle("Accueil");
});

// Démo boutons
const demoButtons = [
    {
        color: "primary",
        size: "sm",
        variant: "glass",
        label: "Primaire",
        tooltip: "Bouton principal",
    },
    {
        color: "secondary",
        size: "sm",
        variant: "glass",
        label: "Secondaire",
        tooltip: "Bouton secondaire",
    },
    {
        color: "success",
        size: "sm",
        variant: "glass",
        label: "Succès",
        tooltip: "Action réussie",
    },
    {
        color: "error",
        size: "sm",
        variant: "glass",
        label: "Erreur",
        tooltip: "Action d'erreur",
    },
    {
        color: "neutral",
        size: "sm",
        variant: "glass",
        label: "Simple",
        tooltip: "Action neutre",
    },
];
const outlineButtons = [
    {
        color: "primary",
        size: "sm",
        variant: "outline",
        label: "Principale",
        tooltip: "Bouton principal en contour",
    },
    {
        color: "secondary",
        size: "sm",
        variant: "outline",
        label: "Mineure",
        tooltip: "Bouton secondaire en contour",
    },
    {
        color: "success",
        size: "sm",
        variant: "outline",
        label: "Valider",
        tooltip: "Bouton de validation en contour",
    },
    {
        color: "error",
        size: "sm",
        variant: "outline",
        label: "Annuler",
        tooltip: "Bouton d'annulation en contour",
    },
    {
        color: "neutral",
        size: "sm",
        variant: "outline",
        label: "Simple",
        tooltip: "Bouton neutre en contour",
    },
];
const linkButtons = [
    {
        color: "primary",
        size: "sm",
        variant: "link",
        label: "Principale",
        tooltip: "Lien principal",
    },
    {
        color: "secondary",
        size: "sm",
        variant: "link",
        label: "Mineure",
        tooltip: "Lien secondaire",
    },
    {
        color: "success",
        size: "sm",
        variant: "link",
        label: "Valider",
        tooltip: "Lien de validation",
    },
    {
        color: "error",
        size: "sm",
        variant: "link",
        label: "Annuler",
        tooltip: "Lien d'annulation",
    },
    {
        color: "neutral",
        size: "sm",
        variant: "link",
        label: "Simple",
        tooltip: "Lien neutre",
    },
];
// Démo icônes
const demoIcons = [
    {
        size: "xs",
        source: "icons/characteristics/pa.png",
        tooltip: "Icône extra-small",
    },
    {
        size: "sm",
        source: "icons/characteristics/pm.png",
        tooltip: "Icône small",
    },
    {
        size: "md",
        source: "icons/characteristics/po.png",
        tooltip: "Icône medium",
    },
    {
        size: "lg",
        source: "icons/characteristics/tacle.png",
        tooltip: "Icône large",
    },
    {
        size: "xl",
        source: "icons/characteristics/res_terre.png",
        tooltip: "Icône extra-large",
    },
];
</script>

<template>
    <div class="container mx-auto">
        <!-- Hero d'intro -->
        <Hero minHeight="min-h-[200px] border-glass-primary-xs">
            <template #content>
                <div class="text-center space-y-4">
                    <h1 class="text-title">
                        Bienvenue sur KrosmozJDR
                    </h1>
                    <p class="text-lg text-subtitle">
                        L'aventure épique dans l'univers du Monde des Douze commence
                        ici !
                    </p>
                    <Btn color="primary" size="md" variant="glass">Rejoindre l'aventure</Btn>
                </div>
            </template>
        </Hero>

        <Container class="space-y-8 mt-8">
            <!-- Section démo boutons (inline, à supprimer plus tard) -->
            <section class="space-y-6">
                <div class="text-center space-y-6">
                    <!-- Boutons glass -->
                    <div class="flex gap-4 justify-center flex-wrap">
                        <Tooltip v-for="button in demoButtons" :key="button.color" :content="button.tooltip">
                            <Btn :color="button.color" :size="button.size" :variant="button.variant">{{ button.label }}
                            </Btn>
                        </Tooltip>
                    </div>
                    <!-- Boutons outline -->
                    <div class="flex gap-4 justify-center flex-wrap">
                        <Tooltip v-for="button in outlineButtons" :key="button.color" :content="button.tooltip">
                            <Btn :color="button.color" :size="button.size" :variant="button.variant">{{ button.label }}
                            </Btn>
                        </Tooltip>
                    </div>
                    <!-- Boutons link -->
                    <div class="flex gap-4 justify-center flex-wrap">
                        <Tooltip v-for="button in linkButtons" :key="button.color" :content="button.tooltip">
                            <Btn :color="button.color" :size="button.size" :variant="button.variant">{{ button.label }}
                            </Btn>
                        </Tooltip>
                    </div>
                    <!-- Icônes de démonstration -->
                    <div class="flex gap-4 justify-center items-center">
                        <Tooltip v-for="icon in demoIcons" :key="icon.source" :content="icon.tooltip">
                            <Icon :size="icon.size" :source="icon.source" alt="Icône de démo" />
                        </Tooltip>
                    </div>
                </div>
            </section>

            <!-- Présentation du projet -->
            <section class="prose text-content max-sm:prose-sm lg:prose-lg">
                <h3 class="text-title">
                    Bienvenue dans <strong>KrosmozJDR</strong>, l'aventure épique
                    dans l'univers du monde des Douze !
                </h3>
                <p>
                    Plongez dans le monde des Douze, un univers riche et vibrant
                    issu de l'imaginaire de <em>Dofus</em>, où l'aventure, la
                    stratégie et la magie s'entrelacent pour créer une expérience
                    unique de jeu de rôle. Ici, chaque partie est une porte ouverte
                    vers des terres fascinantes, des créatures captivantes et des
                    combats épiques.
                </p>
                <h4 class="text-subtitle">Explorez des lieux mythiques</h4>
                <p>
                    De la cité lumineuse de Bonta aux mystères d'Astrub, en passant
                    par les plaines sauvages des Craqueleurs et les secrets
                    d'Amakna, découvrez un monde vaste et varié où chaque région est
                    le théâtre d'histoires légendaires. Préparez-vous à croiser la
                    route des Wabbits malicieux, des Bouftous sauvages, des Chafeurs
                    redoutables, et à affronter les puissants maîtres des donjons
                    comme Kardorim, Groloum ou encore le Comte Harebourg.
                </p>
                <h4 class="text-subtitle">Incarnez une classe iconique</h4>
                <p>
                    Choisissez parmi une grande variété de classes emblématiques :
                    serez-vous un Crâ précis et implacable, un Iop téméraire et
                    valeureux, un Osamodas lié aux créatures, ou encore un Eliotrope
                    maître des portails ? Que vous soyez un soigneur Eniripsa, un
                    stratège Roublard ou un manipulateur du temps Xélor, chaque
                    classe offre des mécaniques uniques et des sorts adaptés à
                    l'univers du jeu de rôle.
                </p>
                <h4 class="text-subtitle">Développez votre personnage</h4>
                <p>
                    Grâce à un système de compétences et d'aptitudes inspiré de
                    D&amp;D, personnalisez votre héros pour qu'il reflète votre
                    style de jeu. Enrichissez vos stratégies avec des
                    spécialisations uniques, inspirées des subtilités des races de
                    D&amp;D, et explorez des gameplays toujours plus variés et
                    profonds.
                </p>
                <h4 class="text-subtitle">Un gameplay enrichi et immersif</h4>
                <p>
                    Notre jeu de rôle fusionne les règles classiques du JdR avec
                    l'univers riche de Dofus, pour offrir une expérience immersive
                    et accessible à tous, débutants comme vétérans. Préparez-vous à
                    vivre des quêtes épiques, à forger des alliances mémorables, et
                    à écrire votre légende dans un monde en perpétuelle évolution.
                </p>
                <hr class="border-primary-700/50" />
                <h3 class="text-title">Êtes-vous prêt à rejoindre l'aventure ?</h3>
                <p>
                    Rassemblez vos compagnons, lancez vos dés et partez à la
                    découverte du monde des Douze. Votre destin est entre vos mains
                    !
                </p>
            </section>
        </Container>
    </div>

</template>
