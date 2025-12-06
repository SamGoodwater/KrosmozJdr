<script setup>
/**
 * SectionHeader Molecule
 * 
 * @description
 * Header réutilisable pour toutes les sections.
 * - Titre modifiable en mode édition
 * - Icônes d'action au hover
 * - Gère les actions : copier lien, basculer mode, paramètres
 * 
 * @props {String} title - Titre de la section
 * @props {Boolean} isEditing - Mode édition actif
 * @props {Boolean} canEdit - Droits d'écriture
 * @props {Boolean} isHovered - Hover actif sur la section
 * 
 * @emits update:title - Mise à jour du titre
 * @emits toggle-edit - Basculer mode lecture/écriture
 * @emits open-params - Ouvrir modal paramètres
 * @emits copy-link - Copier le lien de la section
 */
import { ref, watch } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';

const props = defineProps({
  title: {
    type: String,
    default: ''
  },
  isEditing: {
    type: Boolean,
    default: false
  },
  canEdit: {
    type: Boolean,
    default: false
  },
  isHovered: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:title', 'toggle-edit', 'open-params', 'copy-link']);

// Titre local pour l'input
const localTitle = ref(props.title);

// Synchroniser avec le prop title
watch(() => props.title, (newTitle) => {
  localTitle.value = newTitle;
});

/**
 * Gère la mise à jour du titre
 */
const handleTitleBlur = () => {
  if (localTitle.value !== props.title) {
    emit('update:title', localTitle.value);
  }
};

/**
 * Gère la touche Enter pour valider le titre
 */
const handleTitleKeydown = (event) => {
  if (event.key === 'Enter') {
    event.target.blur();
  }
};
</script>

<template>
  <div class="section-header flex items-center justify-between gap-4 py-2 border-b border-base-300/50">
    <!-- Titre à gauche -->
    <div class="section-header__title flex-1 min-w-0">
      <input
        v-if="isEditing"
        v-model="localTitle"
        @blur="handleTitleBlur"
        @keydown="handleTitleKeydown"
        type="text"
        class="input input-sm input-ghost w-full"
        placeholder="Titre de la section"
      />
      <h3 v-else class="text-lg font-semibold truncate">
        {{ title || 'Sans titre' }}
      </h3>
    </div>
    
    <!-- Icônes à droite (hover) -->
    <div v-if="isHovered" class="section-header__actions flex items-center gap-2">
      <!-- Copier lien (toujours visible) -->
      <button
        @click="$emit('copy-link')"
        class="btn btn-ghost btn-sm btn-square"
        title="Copier le lien de la section"
        type="button"
      >
        <Icon source="fa-solid fa-link" size="sm" />
      </button>
      
      <!-- Basculer mode (si droits d'écriture) -->
      <button
        v-if="canEdit"
        @click="$emit('toggle-edit')"
        class="btn btn-ghost btn-sm btn-square"
        :title="isEditing ? 'Passer en mode lecture' : 'Passer en mode édition'"
        type="button"
      >
        <Icon 
          :source="isEditing ? 'fa-solid fa-eye' : 'fa-solid fa-edit'" 
          size="sm" 
        />
      </button>
      
      <!-- Paramètres (si droits d'écriture) -->
      <button
        v-if="canEdit"
        @click="$emit('open-params')"
        class="btn btn-ghost btn-sm btn-square"
        title="Paramètres de la section"
        type="button"
      >
        <Icon source="fa-solid fa-gear" size="sm" />
      </button>
    </div>
  </div>
</template>

<style scoped lang="scss">
.section-header {
  transition: all 0.2s ease;
  
  &__title {
    // Styles pour le titre
  }
  
  &__actions {
    // Styles pour les actions
    opacity: 0;
    transition: opacity 0.2s ease;
  }
}

// Afficher les actions au hover du parent (géré par SectionRenderer)
</style>

