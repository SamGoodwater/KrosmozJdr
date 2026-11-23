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
 * @emits request-delete - Demander la suppression de la section
 * @emits toggle-collapse - Replier ou déplier le corps de la section
 */
import { ref, watch } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';

const props = defineProps({
  title: {
    type: String,
    default: ''
  },
  /** Affiche le libellé / input titre (false = actions seules, fiches immersives). */
  showTitle: {
    type: Boolean,
    default: true,
  },
  isEditing: {
    type: Boolean,
    default: false
  },
  canEdit: {
    type: Boolean,
    default: false
  },
  canDelete: {
    type: Boolean,
    default: false
  },
  isHovered: {
    type: Boolean,
    default: false
  },
  /** Corps masqué : seul le libellé reste visible. */
  collapsed: {
    type: Boolean,
    default: false,
  },
  /**
   * Taille du titre en lecture.
   * `compact` : h3/h4 densifiés (fiches classe, niveaux).
   */
  titleSize: {
    type: String,
    default: "default",
    validator: (v) => ["default", "compact"].includes(v),
  },
});

const emit = defineEmits(['update:title', 'toggle-edit', 'open-params', 'copy-link', 'request-delete', 'toggle-collapse']);

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
  <div
    class="section-header flex w-full items-center gap-2 py-2"
    :class="showTitle || isEditing ? 'border-b border-base-300/45' : 'border-b-0 py-1'"
  >
    <!-- Titre à gauche -->
    <div
      v-if="showTitle || isEditing"
      class="section-header__title flex min-w-0 max-w-[70%] items-start gap-2"
    >
      <span class="mt-1.5 hidden h-8 w-1 shrink-0 rounded-full bg-primary/70 sm:block" aria-hidden="true" />
      <input
        v-if="isEditing"
        v-model="localTitle"
        @blur="handleTitleBlur"
        @keydown="handleTitleKeydown"
        type="text"
        class="input input-sm input-ghost w-full"
        placeholder="Titre"
      />
      <h3
        v-else
        class="truncate font-semibold tracking-tight text-base-content m-0"
        :class="
          titleSize === 'compact'
            ? 'text-sm md:text-base'
            : 'text-xl font-bold md:text-2xl'
        "
      >
        {{ title }}
      </h3>
    </div>

    <div class="section-header__tools">
    <button
      v-if="!isEditing"
      type="button"
      class="section-header__collapse"
      :aria-expanded="collapsed ? 'false' : 'true'"
      :title="collapsed ? 'Déplier la section' : 'Replier la section'"
      @click.stop="emit('toggle-collapse')"
    />
    <span v-if="!isEditing" class="section-header__collapse-icon" aria-hidden="true">
      <Icon
        :source="collapsed ? 'fa-chevron-down' : 'fa-chevron-up'"
        pack="solid"
        alt=""
        size="sm"
      />
    </span>

    <!-- Icônes à droite (hover). Au-dessus de la zone de repli, sans la recouvrir au clic. -->
    <div class="section-header__actions" :class="{ 'section-header__actions--visible': isHovered }">
      <!-- Copier lien (toujours visible) -->
      <button
        @click="$emit('copy-link')"
        class="btn btn-ghost btn-sm btn-square"
        title="Copier le lien de la section"
        type="button"
      >
        <Icon source="fa-link" pack="solid" alt="Copier le lien" size="sm" />
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
          :source="isEditing ? 'fa-eye' : 'fa-edit'" 
          pack="solid"
          :alt="isEditing ? 'Passer en mode lecture' : 'Passer en mode édition'"
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
        <Icon source="fa-gear" pack="solid" alt="Paramètres" size="sm" />
      </button>

      <!-- Suppression rapide (si droits) -->
      <button
        v-if="canDelete"
        @click="$emit('request-delete')"
        class="btn btn-ghost btn-sm btn-square text-error"
        title="Supprimer la section"
        type="button"
      >
        <Icon source="fa-trash-can" pack="solid" alt="Supprimer la section" size="sm" />
      </button>
    </div>
    </div>
  </div>
</template>

<style scoped lang="scss">
.section-header {
  transition: all 0.2s ease;

  &__tools {
    position: relative;
    display: flex;
    flex: 1 1 auto;
    align-items: center;
    justify-content: flex-end;
    min-width: 0;
    min-height: 2.5rem;
  }

  &__collapse {
    position: absolute;
    inset: 0;
    margin: 0;
    padding-left: 2.75rem;
    border: 0;
    background: transparent;
    cursor: pointer;
  }

  &__collapse-icon {
    position: relative;
    z-index: 1;
    display: inline-flex;
    align-items: center;
    padding-right: 0.15rem;
    color: color-mix(in srgb, hsl(var(--bc)) 70%, transparent);
    pointer-events: none;
  }

  &__actions {
    position: relative;
    z-index: 2;
    display: flex;
    flex-shrink: 0;
    align-items: center;
    gap: 0.25rem;
    opacity: 0;
    transition: opacity 0.2s ease;
    pointer-events: none;
    
    &--visible {
      opacity: 1;
      pointer-events: auto;
    }
  }
}

// Afficher les actions au hover du parent (géré par SectionRenderer)
// Utiliser aussi group-hover comme fallback
:deep(.group:hover) .section-header__actions {
  opacity: 1;
  pointer-events: auto;
}
</style>

