<script setup>
/**
 * CapabilityViewText — Vue Texte pour Capability
 *
 * @description
 * Délègue à `EntityViewTextLink` (aperçu `CapabilityViewMinimal`).
 * `appearance="inline"` aligne la puce sur les krefs CMS ; `trigger="hover"` pour le survol.
 *
 * @props {Capability} capability - Instance du modèle Capability
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import CapabilityViewMinimal from "./CapabilityViewMinimal.vue";

defineProps({
  capability: { type: Object, required: true },
  /** @type {'chip'|'inline'} */
  appearance: {
    type: String,
    default: "chip",
    validator: (v) => ["chip", "inline"].includes(v),
  },
  /** @type {''|'hover'|'click'} */
  trigger: {
    type: String,
    default: "",
  },
  hydrateType: {
    type: String,
    default: "capabilities",
  },
});
</script>

<template>
  <EntityViewTextLink
    :entity="capability"
    entity-prop="capability"
    :minimal-component="CapabilityViewMinimal"
    fallback-icon="fa-solid fa-magic"
    :appearance="appearance"
    :trigger="trigger"
    :hydrate-type="hydrateType"
  />
</template>
