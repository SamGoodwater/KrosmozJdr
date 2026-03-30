<script setup>
/**
 * Zone d’effet : icône de forme + libellé court, infobulle avec notation complète et schéma.
 *
 * @description
 * Icônes : `storage/app/public/images/icons/areas/` (via {@link getAreaIcon}).
 */
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import SpellZonePreview from '@/Pages/Molecules/entity/spell/SpellZonePreview.vue';
import {
    getAreaIcon,
    getAreaShape,
    getAreaShapeLabel,
    getAreaShortLabel,
    getAreaSummaryLine,
} from '@/Utils/Entity/Areas';

const props = defineProps({
    area: {
        type: String,
        default: '',
    },
    /** Taille de l’icône de forme */
    iconSize: {
        type: String,
        default: 'xs',
    },
    /** Masquer le texte court (icône seule) */
    iconOnly: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <Tooltip v-if="area && String(area).trim() !== ''" placement="top" color="neutral" class="inline-flex max-w-full">
        <span class="inline-flex items-center gap-1 min-w-0">
            <Icon
                :source="getAreaIcon(area)"
                :alt="getAreaShapeLabel(getAreaShape(area))"
                :size="iconSize"
                class="shrink-0 opacity-90"
            />
            <span
                v-if="!iconOnly"
                class="text-xs tabular-nums text-base-content/90 truncate font-medium"
            >
                {{ getAreaShortLabel(area) }}
            </span>
        </span>
        <template #content>
            <div class="max-w-[min(18rem,85vw)] space-y-2 p-0.5">
                <p class="text-xs font-semibold text-base-content leading-snug">
                    {{ getAreaSummaryLine(area) }}
                </p>
                <p class="font-mono text-[10px] text-base-content/70 break-all">
                    {{ area }}
                </p>
                <SpellZonePreview :area="area" />
            </div>
        </template>
    </Tooltip>
    <span v-else class="text-base-content/40">—</span>
</template>
