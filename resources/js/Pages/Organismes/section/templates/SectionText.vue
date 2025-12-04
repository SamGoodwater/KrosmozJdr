<script setup>
/**
 * SectionText Template
 * 
 * @description
 * Template de section pour afficher du texte riche.
 * - Supporte le HTML via v-html
 * - Gère l'alignement (left, center, right)
 * - Gère la taille (sm, md, lg, xl)
 * 
 * @props {Object} params - Paramètres de la section
 * @props {String} params.content - Contenu HTML de la section (requis)
 * @props {String} params.align - Alignement du texte (left|center|right, optionnel)
 * @props {String} params.size - Taille du texte (sm|md|lg|xl, optionnel)
 * @props {Object} section - Données complètes de la section
 * @props {Object|null} user - Utilisateur connecté (optionnel)
 * 
 * @example
 * <SectionText 
 *   :params="{ content: '<p>Hello</p>', align: 'center', size: 'lg' }"
 *   :section="section"
 * />
 */
import { computed } from 'vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';

const props = defineProps({
    params: {
        type: Object,
        required: true,
        default: () => ({})
    },
    section: {
        type: Object,
        required: true
    },
    user: {
        type: Object,
        default: null
    }
});

/**
 * Classes d'alignement
 */
const alignClasses = computed(() => {
    const align = props.params.align || 'left';
    const alignMap = {
        'left': 'text-left',
        'center': 'text-center',
        'right': 'text-right'
    };
    return alignMap[align] || 'text-left';
});

/**
 * Classes de taille
 */
const sizeClasses = computed(() => {
    const size = props.params.size || 'md';
    const sizeMap = {
        'sm': 'text-sm',
        'md': 'text-base',
        'lg': 'text-lg',
        'xl': 'text-xl'
    };
    return sizeMap[size] || 'text-base';
});

/**
 * Contenu HTML sécurisé
 */
const content = computed(() => {
    return props.params.content || '';
});
</script>

<template>
    <div class="section-text" :class="[alignClasses, sizeClasses]">
        <div 
            v-if="content"
            class="prose prose-invert max-w-none"
            v-html="content"
        />
        <p v-else class="text-base-content/50 italic">
            Aucun contenu disponible.
        </p>
    </div>
</template>

<style scoped lang="scss">
.section-text {
    // Styles par défaut pour le texte
    :deep(p) {
        margin-bottom: 1rem;
        
        &:last-child {
            margin-bottom: 0;
        }
    }
    
    :deep(h1, h2, h3, h4, h5, h6) {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        font-weight: bold;
    }
    
    :deep(ul, ol) {
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }
    
    :deep(li) {
        margin-bottom: 0.5rem;
    }
    
    :deep(a) {
        color: hsl(var(--p));
        text-decoration: underline;
        
        &:hover {
            color: hsl(var(--pf));
        }
    }
    
    :deep(img) {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
    }
}
</style>

