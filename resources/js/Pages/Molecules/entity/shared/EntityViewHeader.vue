<script setup>
/**
 * EntityViewHeader — Header commun pour les vues d'entités
 *
 * @description
 * Implémente la structure du header selon la doc ENTITY_VIEWS :
 * - full: image à gauche, title + main infos + (subtitle) à droite, actions en haut à droite (fiche page).
 * - compact: layout header dense (modal full) — pas la vue entité ViewCompact supprimée.
 * - minimal: title + actions sur la même ligne, main infos à droite du titre (icône-only).
 *
 * @props {'full'|'compact'|'minimal'} mode - Mode de rendu du header.
 *
 * @slot dot - Indicateur optionnel positionné en absolute par le parent.
 * @slot media - Image/icone (à gauche)
 * @slot title - Titre (nom)
 * @slot mainInfos - Infos principales (large/compact)
 * @slot mainInfosRight - Infos principales à droite (minimal)
 * @slot subtitle - Description / sous-texte (optionnel)
 * @slot actions - Barre d'actions (EntityActions)
 *
 * @example
 * <EntityViewHeader mode="full">
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
    default: "full",
    validator: (v) => ["full", "compact", "minimal"].includes(v),
  },
});

const isMinimal = computed(() => props.mode === "minimal");
const isCompact = computed(() => props.mode === "compact");
</script>

<template>
  <div class="relative">
    <slot v-if="!isMinimal" name="dot" />

    <!-- Minimal -->
    <div v-if="isMinimal" class="flex min-w-0 items-start gap-2">
      <div class="flex min-w-0 items-center gap-2">
        <div class="flex-shrink-0 relative">
          <slot name="media" />
          <div class="absolute top-0 left-0">
            <slot name="dot" />
          </div>
        </div>
        <div class="min-w-0">
          <slot name="title" />
          <slot name="subtitle" />
        </div>
      </div>

      <div class="flex min-w-8 flex-1 items-start justify-end gap-2">
        <div class="shrink-0">
          <slot name="mainInfosRight" />
        </div>
        <div class="flex min-w-8 flex-1 justify-end">
          <slot name="actions" />
        </div>
      </div>
    </div>

    <!-- Compact -->
    <div v-else-if="isCompact" class="flex items-start gap-3">
      <div class="flex-shrink-0">
        <slot name="media" />
      </div>

      <div class="flex-1 min-w-0">
        <div class="flex min-w-0 items-start gap-2">
          <div class="min-w-0">
            <slot name="title" />
          </div>
          <div class="flex min-w-8 flex-1 justify-end">
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

      <div class="flex-1 w-full min-w-0">
        <div class="flex min-w-0 items-start gap-4">
          <div class="min-w-0">
            <slot name="title" />
          </div>
          <div class="flex min-w-8 flex-1 justify-end">
            <slot name="actions" />
          </div>
        </div>

        <slot name="mainInfos" />
        <slot name="subtitle" />
      </div>
    </div>
  </div>
</template>

