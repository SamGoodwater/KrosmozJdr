<script setup>
/**
 * EntityViewHeader — Header commun pour les vues d'entités
 *
 * @description
 * Implémente la structure du header selon la doc ENTITY_VIEWS :
 * - full: fiche page — desktop image à gauche / titre+infos à droite ;
 *   mobile (&lt; md) : titre puis image puis infos (évite le titre écrasé à droite).
 * - compact: modal full — même empilement mobile ; desktop dense image | contenu.
 * - minimal: title + actions sur la même ligne, main infos à droite du titre (icône-only).
 *
 * @props {'full'|'compact'|'minimal'} mode - Mode de rendu du header.
 *
 * @slot dot - Indicateur optionnel positionné en absolute par le parent.
 * @slot media - Image/icone
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

/**
 * Grille responsive : mobile = titre → media → infos ; md+ = media | contenu.
 * Underscores Tailwind = espaces dans `grid-template-areas`.
 */
const stackLayoutClass = computed(() =>
  [
    "grid w-full min-w-0 items-start",
    isCompact.value ? "gap-3" : "gap-4",
    "[grid-template-areas:'title'_'media'_'infos'_'subtitle']",
    "md:grid-cols-[auto_minmax(0,1fr)]",
    "md:[grid-template-areas:'media_title'_'media_infos'_'media_subtitle']",
  ].join(" "),
);
</script>

<template>
  <div class="relative">
    <slot v-if="!isMinimal" name="dot" />

    <!-- Minimal -->
    <div v-if="isMinimal" class="flex w-full min-w-0 items-start gap-2">
      <div class="flex min-w-0 items-center gap-2">
        <div class="relative flex-shrink-0">
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

      <div class="ml-auto flex min-w-8 flex-1 items-start justify-end gap-2">
        <div class="shrink-0">
          <slot name="mainInfosRight" />
        </div>
        <div class="ml-auto flex min-w-8 flex-1 justify-end">
          <slot name="actions" />
        </div>
      </div>
    </div>

    <!-- Compact (modal) + Full (page) : même empilement mobile -->
    <div v-else :class="stackLayoutClass">
      <div class="[grid-area:media] mx-auto w-fit max-w-full shrink-0 justify-self-center md:mx-0 md:justify-self-start">
        <slot name="media" />
      </div>

      <div class="[grid-area:title] flex w-full min-w-0 items-start gap-3 md:gap-4">
        <div class="min-w-0 flex-1">
          <slot name="title" />
        </div>
        <div class="ml-auto flex min-w-8 shrink-0 justify-end">
          <slot name="actions" />
        </div>
      </div>

      <div class="[grid-area:infos] min-w-0">
        <slot name="mainInfos" />
      </div>

      <div class="[grid-area:subtitle] min-w-0">
        <slot name="subtitle" />
      </div>
    </div>
  </div>
</template>
