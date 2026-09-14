<script setup>
/**
 * Marqueur visuel « Boss » (icône dédiée) pour les profils monstre boss.
 *
 * @description
 * Affiche l’icône caractéristique `boss.webp` (storage public + lien symbolique).
 * En cas d’absence / erreur de chargement, repli sur une icône Font Awesome.
 * Si `legendaryPa` &gt; 0, le nombre de PA légendaires est collé juste après l’icône.
 *
 * @props {string} [tooltip] - Texte du survol (règle des PA légendaires)
 * @props {string} [sizeClass] - Classes Tailwind pour la taille du pictogramme
 * @props {number|string|null} [legendaryPa] - Pool de PA légendaires (masqué si vide ou 0)
 *
 * @example
 * <MonsterBossMark :legendary-pa="3" size-class="h-5 w-5" />
 */
import { computed, ref } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";

/** Icône service caractéristiques (`storage/app/public/images/icons/caracteristics/boss.webp`). */
const MONSTER_BOSS_ICON_PUBLIC_PATH = "/storage/images/icons/caracteristics/boss.webp";

const MONSTER_BOSS_LEGENDARY_PA_TOOLTIP =
    "Boss : PA légendaires utilisables entre deux tours d'autres créatures, autant que le pool restant, en plus du tour. Le pool se recharge à la fin du tour du boss.";

const props = defineProps({
    tooltip: {
        type: String,
        default: MONSTER_BOSS_LEGENDARY_PA_TOOLTIP,
    },
    sizeClass: {
        type: String,
        default: "h-7 w-7",
    },
    legendaryPa: {
        type: [Number, String],
        default: null,
    },
});

const imgError = ref(false);

const displayPa = computed(() => {
    const raw = props.legendaryPa;
    if (raw === null || raw === undefined || raw === "") {
        return null;
    }
    const n = Number(raw);
    if (Number.isNaN(n) || n <= 0) {
        return null;
    }
    return n;
});

const ariaLabel = computed(() =>
    displayPa.value != null ? `Boss, ${displayPa.value} PA légendaires` : "Boss",
);
</script>

<template>
    <Tooltip :content="tooltip" placement="top">
        <span
            class="monster-boss-mark inline-flex shrink-0 items-center gap-1"
            role="img"
            :aria-label="ariaLabel"
        >
            <span
                class="inline-flex items-center justify-center rounded-md border border-warning/35 bg-base-100/95 p-0.5 shadow-sm ring-1 ring-warning/25"
            >
                <img
                    v-if="!imgError"
                    :src="MONSTER_BOSS_ICON_PUBLIC_PATH"
                    alt=""
                    :class="[sizeClass, 'object-contain select-none']"
                    draggable="false"
                    loading="lazy"
                    decoding="async"
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
            <span
                v-if="displayPa != null"
                class="tabular-nums font-semibold leading-none text-warning"
            >
                {{ displayPa }}
            </span>
        </span>
    </Tooltip>
</template>
