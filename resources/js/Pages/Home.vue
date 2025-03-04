<script setup>
import { ref, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import Btn from "@/Pages/Atoms/actions/Btn.vue";
import { MediaManager } from "@/Utils/MediaManager";
import Icons from "@/Pages/Atoms/images/Icon.vue";

const page = usePage();

// Variable pour suivre l'état du cache
const isCacheInitialized = ref(false);

onMounted(async () => {
    if (!isCacheInitialized.value) {
        try {
            await MediaManager.refreshCache();
            await MediaManager.preload('image');
            isCacheInitialized.value = true;
        } catch (error) {
            console.error("Erreur lors de l'initialisation du cache:", error);
        }
    }
});

const convertStability = {
    alpha: "α",
    beta: "β",
    rc: "rc",
    stable: "",
};

const appDescription = ref(import.meta.env.VITE_APP_DESCRIPTION);
const appKeywords = ref(import.meta.env.VITE_APP_KEYWORDS);

const form = ref({
    email: "",
});
</script>

<template>
    <section
        class="prose prose-state prose-a:text-content max-sm:prose-sm lg:prose-lg"
    >
        <h1 class="text-amber-800 text-center">Site en construction</h1>

        <!-- TESTS BUTTONS -->
        <div class="text-center">
            <div class="flex gap-4 justify-content-around flex-wrap">
                <Btn theme="primary glass sm" label="Primaire" />
                <Btn theme="secondary glass sm" label="Secondaire" />
                <Btn theme="success glass sm" label="Succès" />
                <Btn theme="error glass sm" label="Erreur" />
                <Btn theme="neutral glass sm" label="Simple" />
            </div>
            <div class="flex mt-4 gap-4 justify-content-around flex-wrap">
                <Btn theme="outline sm primary">
                     Principale
                </Btn>

                <Btn theme="outline sm secondary">
                     Mineure
                </Btn>
                <Btn theme="outline sm success">
                     Valider
                </Btn>
                <Btn theme="outline sm error">
                     Annuler
                </Btn>
                <Btn theme="outline sm neutral">
                     Simple
                </Btn>
            </div>
            <div class="flex mt-4 gap-4 justify-content-around flex-wrap">
                <Btn theme="link sm primary">
                     Principale
                </Btn>
                <Btn theme="link sm secondary">
                     Mineure
                </Btn>
                <Btn theme="link sm success">
                     Valider
                </Btn>
                <Btn theme="link sm error">
                     Annuler
                </Btn>
                <Btn theme="link sm neutral">
                     Simple
                </Btn>
            </div>

            <!-- Tests icones -->
            <p>
                <Icons theme="xs" source="icons/modules/pa" /> XS,
                <Icons theme="sm" source="icons/modules/pm" /> SM,
                <Icons theme="md" source="icons/modules/po" /> MD,
                <Icons theme="lg" source="icons/modules/tacle" /> LG,
                <Icons theme="xl" source="icons/modules/res_terre" /> XL
            </p>
            <p>
                <Icons theme="md" source="icons/modules/po" /> MD,
                <Icons theme="lg" source="icons/modules/tacle" /> LG,
                <Icons theme="xl" source="icons/modules/res_terre" /> XL
            </p>
        </div>

        <h3
            class="text-title"
            id="bienvenue-dans-krosmozjdr-l-aventure-pique-dans-l-univers-du-monde-des-douze-"
        >
            Bienvenue dans <strong>KrosmozJDR</strong>, l'aventure épique dans
            l'univers du monde des Douze !
        </h3>
        <p>
            Plongez dans le monde des Douze, un univers riche et vibrant issu de
            l'imaginaire de <em>Dofus</em>, où l'aventure, la stratégie et la
            magie s'entrelacent pour créer une expérience unique de jeu de rôle.
            Ici, chaque partie est une porte ouverte vers des terres
            fascinantes, des créatures captivantes et des combats épiques.
        </p>
        <h4 class="text-subtitle" id="explorez-des-lieux-mythiques">
            Explorez des lieux mythiques
        </h4>
        <p>
            De la cité lumineuse de Bonta aux mystères d'Astrub, en passant par
            les plaines sauvages des Craqueleurs et les secrets d'Amakna,
            découvrez un monde vaste et varié où chaque région est le théâtre
            d'histoires légendaires. Préparez-vous à croiser la route des
            Wabbits malicieux, des Bouftous sauvages, des Chafeurs redoutables,
            et à affronter les puissants maîtres des donjons comme Kardorim,
            Groloum ou encore le Comte Harebourg.
        </p>
        <h4 class="text-subtitle" id="incarnez-une-classe-iconique">
            Incarnez une classe iconique
        </h4>
        <p>
            Choisissez parmi une grande variété de classes emblématiques :
            serez-vous un Crâ précis et implacable, un Iop téméraire et
            valeureux, un Osamodas lié aux créatures, ou encore un Eliotrope
            maître des portails ? Que vous soyez un soigneur Eniripsa, un
            stratège Roublard ou un manipulateur du temps Xélor, chaque classe
            offre des mécaniques uniques et des sorts adaptés à l'univers du jeu
            de rôle.
        </p>
        <h4 class="text-subtitle" id="d-veloppez-votre-personnage">
            Développez votre personnage
        </h4>
        <p>
            Grâce à un système de compétences et d'aptitudes inspiré de D&amp;D,
            personnalisez votre héros pour qu'il reflète votre style de jeu.
            Enrichissez vos stratégies avec des spécialisations uniques,
            inspirées des subtilités des races de D&amp;D, et explorez des
            gameplays toujours plus variés et profonds.
        </p>
        <h4 class="text-subtitle" id="un-gameplay-enrichi-et-immersif">
            Un gameplay enrichi et immersif
        </h4>
        <p>
            Notre jeu de rôle fusionne les règles classiques du JdR avec
            l'univers riche de Dofus, pour offrir une expérience immersive et
            accessible à tous, débutants comme vétérans. Préparez-vous à vivre
            des quêtes épiques, à forger des alliances mémorables, et à écrire
            votre légende dans un monde en perpétuelle évolution.
        </p>
        <hr />
        <h3 class="text-title" id="-tes-vous-pr-t-rejoindre-l-aventure-">
            Êtes-vous prêt à rejoindre l'aventure ?
        </h3>
        <p>
            Rassemblez vos compagnons, lancez vos dés et partez à la découverte
            du monde des Douze. Votre destin est entre vos mains !
        </p>
    </section>

</template>
