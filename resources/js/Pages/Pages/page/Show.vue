<script setup>
/**
 * Page Show Component
 * 
 * @description
 * Page d'affichage d'une page dynamique avec ses sections.
 * Utilise PageRenderer pour afficher la page et ses sections.
 * 
 * @props {Object} page - Données de la page (avec sections)
 */
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import PageRenderer from '@/Pages/Organismes/section/PageRenderer.vue';
import { usePageTitle } from '@/Composables/layout/usePageTitle';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    page: {
        type: Object,
        required: true
    },
    pages: {
        type: Array,
        default: () => []
    }
});

/**
 * Utilisateur connecté
 */
const user = computed(() => {
    return page.props.auth?.user || null;
});

/**
 * Titre de la page
 */
const pageTitle = computed(() => {
    return props.page.title || 'Page';
});

// Mettre à jour le titre de la page
setPageTitle(pageTitle.value);
</script>

<template>
    <Head :title="pageTitle" />
    
    <PageRenderer 
        :page="page"
        :user="user"
        :pages="pages"
    />
</template>

<style scoped lang="scss">
// Styles spécifiques à la page si nécessaire
</style>