<script setup>
/**
 * Marqueur visuel « Boss » (icône dédiée) pour les profils monstre boss.
 *
 * @description
 * Affiche `public/images/entity/monsters/boss.webp` (à placer côté déploiement).
 * En cas d’absence / erreur de chargement, repli sur une icône Font Awesome.
 *
 * @props {string} [tooltip] - Texte du survol (règle boss + PA bonus)
 * @props {string} [sizeClass] - Classes Tailwind pour la taille du pictogramme
 */
import { ref } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";

/** Fichier public : placez `boss.webp` à cet emplacement. */
const MONSTER_BOSS_ICON_PUBLIC_PATH = "/images/entity/monsters/boss.webp";

defineProps({
    tooltip: {
        type: String,
        default:
            "Boss : ce monstre dispose de PA supplémentaires utilisables entre ses tours, à tout moment du round.",
    },
    sizeClass: {
        type: String,
        default: "h-7 w-7",
    },
});

const imgError = ref(false);
</script>

<template>
    <Tooltip :content="tooltip" placement="top">
        <span
            class="monster-boss-mark inline-flex shrink-0 items-center justify-center rounded-md border border-warning/35 bg-base-100/95 p-0.5 shadow-sm ring-1 ring-warning/25"
            role="img"
            aria-label="Boss"
        >
            <img
                v-if="!imgError"
                :src="MONSTER_BOSS_ICON_PUBLIC_PATH"
                alt=""
                :class="[sizeClass, 'object-contain select-none']"
                draggable="false"
                @error="imgError = true"
            />
            <Icon
                v-else
                source="fa-solid fa-skull"
                size="sm"
                class="text-warning"
                alt=""
            />
        </span>
    </Tooltip>
</template>
