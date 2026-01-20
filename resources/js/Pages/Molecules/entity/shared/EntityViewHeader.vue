<script setup>
/**
 * EntityViewHeader — Header commun pour les vues d'entités
 *
 * @description
 * Implémente la structure du header selon la doc ENTITY_VIEWS :
 * - Large: image à gauche, title + main infos + (subtitle) à droite, actions en haut à droite.
 * - Compact: image à gauche, title + actions sur la même ligne, main infos sous le titre.
 * - Minimal: title + actions sur la même ligne, main infos à droite du titre (icône-only), subtitle optionnelle.
 *
 * Les contenus sont fournis via slots pour rester "descriptors-driven".
 *
 * @props {'large'|'compact'|'minimal'} mode - Mode de rendu du header.
 *
 * @slot dot - Indicateur optionnel (ex: EntityUsableDot) positionné en absolute par le parent.
 * @slot media - Image/icone (à gauche)
 * @slot title - Titre (nom)
 * @slot mainInfos - Infos principales (large/compact)
 * @slot mainInfosRight - Infos principales à droite (minimal)
 * @slot subtitle - Description / sous-texte (optionnel)
 * @slot actions - Barre d'actions (EntityActions)
 *
 * @example
 * <EntityViewHeader mode="large">
 *   <template #media>...</template>
 *   <template #title>...</template>
 *   <template #mainInfos>...</template>
 *   <template #actions>...</template>
 * </EntityViewHeader>
 */
import { computed } from "vue";

const props = defineProps({
  mode: {
    type: String,
    default: "large",
    validator: (v) => ["large", "compact", "minimal"].includes(v),
  },
});

const isMinimal = computed(() => props.mode === "minimal");
const isCompact = computed(() => props.mode === "compact");
</script>

<template>
  <div class="relative">
    <slot v-if="!isMinimal" name="dot" />

    <!-- Minimal -->
    <div v-if="isMinimal" class="flex items-start justify-between gap-2">
      <div class="flex items-center gap-2 flex-1 min-w-0">
        <div class="flex-shrink-0 relative">
          <slot name="media" />
          <div class="absolute top-0 left-0">
            <slot name="dot" />
          </div>
        </div>
        <div class="flex-1 min-w-0">
          <slot name="title" />
          <slot name="subtitle" />
        </div>
      </div>

      <div class="flex items-start gap-2 flex-shrink-0">
        <slot name="mainInfosRight" />
        <slot name="actions" />
      </div>
    </div>

    <!-- Compact -->
    <div v-else-if="isCompact" class="flex items-start gap-3">
      <div class="flex-shrink-0">
        <slot name="media" />
      </div>

      <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between gap-2">
          <div class="flex-1 min-w-0">
            <slot name="title" />
          </div>
          <div class="flex-shrink-0">
            <slot name="actions" />
          </div>
        </div>

        <div class="mt-2">
          <slot name="mainInfos" />
        </div>

        <slot name="subtitle" />
      </div>
    </div>

    <!-- Large -->
    <div v-else class="flex flex-col md:flex-row gap-4 items-start">
      <div class="flex-shrink-0">
        <slot name="media" />
      </div>

      <div class="flex-1 w-full">
        <div class="flex items-start justify-between gap-4">
          <div class="flex-1 min-w-0">
            <slot name="title" />
            <slot name="mainInfos" />
            <slot name="subtitle" />
          </div>

          <div class="flex-shrink-0">
            <slot name="actions" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

