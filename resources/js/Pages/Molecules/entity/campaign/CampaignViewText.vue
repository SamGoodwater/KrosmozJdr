<script setup>
/**
 * CampaignViewText — Vue Texte pour Campaign
 *
 * @description
 * Délègue l’implémentation à `EntityViewTextLink` (hover → CampaignViewMinimal).
 *
 * @props {Campaign} campaign - Instance du modèle Campaign
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import CampaignViewMinimal from "./CampaignViewMinimal.vue";

defineProps({
  campaign: { type: Object, required: true },
});
</script>

<template>
  <EntityViewTextLink
    :entity="campaign"
    entity-prop="campaign"
    :minimal-component="CampaignViewMinimal"
    fallback-icon="fa-solid fa-book"
  />
</template>
