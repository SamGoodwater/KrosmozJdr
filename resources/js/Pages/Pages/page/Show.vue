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
import { computed, watch } from 'vue';
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
    },
    menuChildIndex: {
        type: Array,
        default: () => [],
    },
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
    return props.page?.title || props.page?.data?.title || 'Page';
});

// Mettre à jour le titre de la page de manière réactive
watch(
    pageTitle,
    (title) => {
        setPageTitle(title || 'Page');
    },
    { immediate: true }
);
</script>

<template>
    <Head :title="pageTitle" />
    
    <PageRenderer 
        :page="props.page"
        :user="user"
        :pages="props.pages"
        :menu-child-index="props.menuChildIndex"
    />
</template>

<style scoped lang="scss">
// Styles spécifiques à la page si nécessaire
</style>