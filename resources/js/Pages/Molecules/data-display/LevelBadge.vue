<script setup>
/**
 * LevelBadge Molecule
 *
 * @description
 * Badge réutilisable pour afficher un niveau avec un dégradé de couleurs cohérent
 * dans toute l'application (table, filtres, formulaires, etc.).
 *
 * Règles:
 * - plage définie: 0 → 30 (dégradé)
 * - au-delà: fond noir
 *
 * @example
 * <LevelBadge :level="12" />
 * <LevelBadge :level="42" size="sm" />
 */
import { computed } from "vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import { getLevelColor } from "@/Utils/Entity/SharedConstants.js";

const props = defineProps({
  /**
   * Niveau à afficher. Accepte number/string (ex: "12").
   */
  level: { type: [Number, String], default: null },
  /**
   * Taille du badge (DaisyUI).
   */
  size: { type: String, default: "sm" },
  /**
   * Variant du badge (DaisyUI).
   */
  variant: { type: String, default: "soft" },
  /**
   * Afficher le préfixe "N".
   */
  prefix: { type: String, default: "N" },
  /**
   * Tooltip optionnel (fallback auto).
   */
  tooltip: { type: String, default: "" },
  /**
   * Glassy (optionnel).
   */
  glassy: { type: Boolean, default: false },
});

const parsed = computed(() => {
  const raw = props.level;
  if (raw === null || typeof raw === "undefined" || raw === "") return null;
  const n = typeof raw === "number" ? raw : Number.parseInt(String(raw), 10);
  if (!Number.isFinite(n)) return null;
  return n;
});

const label = computed(() => {
  if (parsed.value === null) return "—";
  return `${props.prefix}${parsed.value}`;
});

const color = computed(() => {
  if (parsed.value === null) return "neutral";
  return getLevelColor(parsed.value);
});

const effectiveTooltip = computed(() => {
  if (props.tooltip) return props.tooltip;
  if (parsed.value === null) return "";
  return `Niveau ${parsed.value}`;
});
</script>

<template>
  <Badge
    :color="String(color)"
    :variant="variant"
    :size="size"
    :glassy="glassy"
    :content="label"
    :title="effectiveTooltip || undefined"
  />
</template>

