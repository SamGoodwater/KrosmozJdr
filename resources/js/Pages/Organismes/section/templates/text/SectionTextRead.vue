<script setup>
/**
 * SectionTextRead Template
 * 
 * @description
 * Template de section pour afficher du texte riche en mode lecture.
 * - Affiche le contenu HTML
 * - Gère l'alignement et la taille via settings
 * 
 * @props {Object} section - Données complètes de la section
 * @props {Object} data - Données de contenu (section.data)
 * @props {Object} settings - Paramètres (section.settings)
 */
import { computed } from 'vue';
import { useSectionStyles } from '../../composables/useSectionStyles';

const props = defineProps({
  section: {
    type: Object,
    required: true
  },
  data: {
    type: Object,
    default: () => ({})
  },
  settings: {
    type: Object,
    default: () => ({})
  }
});

/**
 * Contenu HTML
 */
const content = computed(() => {
  return props.data?.content || '';
});

/**
 * Classes CSS depuis les settings (utilise le composable)
 */
const { containerClasses } = useSectionStyles(() => props.settings);
</script>

<template>
  <div class="section-text-content" :class="containerClasses">
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
.section-text-content {
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

