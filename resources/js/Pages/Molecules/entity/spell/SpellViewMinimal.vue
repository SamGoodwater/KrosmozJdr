<script setup>
/**
 * SpellViewMinimal — Vue Minimal pour Spell
 * 
 * @description
 * Petite carte qui s'étend au survol.
 * Utilisée dans des grilles, petites modals ou hovers.
 * 
 * @props {Spell} spell - Instance du modèle Spell
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';

const props = defineProps({
    spell: {
        type: Object,
        required: true
    },
    showActions: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'quick-edit', 'delete', 'action']);

const isHovered = ref(false);
const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf('spell');

// Champs importants à afficher
const importantFields = computed(() => ['level', 'pa', 'po', 'element', 'category', 'usable', 'is_visible']);

// Champs supplémentaires à afficher au hover
const expandedFields = computed(() => [
    'area',
    'cast_per_turn',
    'cast_per_target',
    'sight_line',
    'is_magic',
    'auto_update',
]);

const getFieldIcon = (fieldKey) => {
    const icons = {
        level: 'fa-solid fa-level-up-alt',
        pa: 'fa-solid fa-bolt',
        po: 'fa-solid fa-crosshairs',
        area: 'fa-solid fa-expand',
        element: 'fa-solid fa-fire',
        category: 'fa-solid fa-tag',
        cast_per_turn: 'fa-solid fa-repeat',
        cast_per_target: 'fa-solid fa-bullseye',
        sight_line: 'fa-solid fa-eye',
        is_magic: 'fa-solid fa-wand-magic-sparkles',
        usable: 'fa-solid fa-check-circle',
        is_visible: 'fa-solid fa-eye',
        auto_update: 'fa-solid fa-sync',
    };
    return icons[fieldKey] || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.spell.toCell(fieldKey, {
        size: 'sm',
        context: 'minimal',
    });
};

const tooltipForField = (fieldKey, cell) => {
    const labels = {
        level: 'Niveau',
        pa: 'PA',
        po: 'PO',
        area: 'Zone',
        element: 'Élément',
        category: 'Catégorie',
        cast_per_turn: 'Lancers/tour',
        cast_per_target: 'Lancers/cible',
        sight_line: 'Ligne de vue',
        is_magic: 'Magique',
        usable: 'Utilisable',
        is_visible: 'Visibilité',
        auto_update: 'Mise à jour auto',
    };
    const label = labels[fieldKey] || fieldKey;
    const value = cell?.value || '-';
    return `${label} : ${value}`;
};

const handleAction = async (actionKey) => {
    const spellId = props.spell.id;
    if (!spellId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.spells.show', { spell: spellId }));
            emit('view', props.spell);
            break;
        case 'edit':
            router.visit(route('entities.spells.edit', { spell: spellId }));
            emit('edit', props.spell);
            break;
        case 'delete':
            emit('delete', props.spell);
            break;
    }
};
</script>

<template>
    <div 
        class="relative rounded-lg border border-base-300 transition-all duration-300 overflow-hidden"
        :class="{ 
            'bg-base-200 shadow-lg': isHovered,
            'bg-base-100': !isHovered
        }"
        :style="{ 
            width: isHovered ? 'auto' : '150px',
            minWidth: '150px',
            maxWidth: isHovered ? '300px' : '200px',
            height: isHovered ? 'auto' : '100px',
            minHeight: '80px'
        }"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false">
        
        <div class="p-3">
            <!-- En-tête avec nom et actions -->
            <div class="flex items-start justify-between gap-2 mb-2">
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <Icon source="fa-solid fa-wand-magic-sparkles" :alt="spell.name" size="sm" class="flex-shrink-0" />
                    <Tooltip :content="spell.name" placement="top">
                        <span class="font-semibold text-primary-100 text-sm truncate block">{{ spell.name }}</span>
                    </Tooltip>
                </div>
                
                <div v-if="showActions && isHovered" class="flex-shrink-0">
                    <EntityActions
                        entity-type="spell"
                        :entity="spell"
                        format="buttons"
                        display="icon-only"
                        size="xs"
                        color="primary"
                        :context="{ inPanel: false }"
                        @action="handleAction"
                    />
                </div>
            </div>

            <!-- Infos importantes en icônes avec tooltips -->
            <div class="flex gap-2 flex-wrap">
                <template v-for="field in importantFields" :key="field">
                    <Tooltip
                        :content="tooltipForField(field, getCell(field))"
                        placement="top"
                    >
                        <div class="flex items-center gap-1 px-2 py-1 bg-base-200 rounded">
                            <Icon
                                :source="getFieldIcon(field)"
                                size="xs"
                                class="text-primary-400"
                            />
                            <span class="text-xs text-primary-300 font-medium">
                                <CellRenderer
                                    :cell="getCell(field)"
                                    ui-color="primary"
                                />
                            </span>
                        </div>
                    </Tooltip>
                </template>
            </div>

            <!-- Contenu supplémentaire au hover -->
            <div 
                v-if="isHovered" 
                class="mt-2 pt-2 border-t border-base-300 space-y-1 text-xs text-primary-300 animate-fade-in">
                <div
                    v-for="key in expandedFields"
                    :key="key"
                    class="flex items-start gap-2"
                >
                    <Tooltip
                        :content="tooltipForField(key, getCell(key))"
                        placement="left"
                    >
                        <div class="flex items-start gap-2 w-full">
                            <Icon
                                :source="getFieldIcon(key)"
                                size="xs"
                                class="text-primary-400 flex-shrink-0 mt-0.5"
                            />
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-primary-400">
                                    {{ key }}:
                                </div>
                                <div class="text-primary-200 truncate">
                                    <CellRenderer
                                        :cell="getCell(key)"
                                        ui-color="primary"
                                    />
                                </div>
                            </div>
                        </div>
                    </Tooltip>
                </div>
            </div>
        </div>
    </div>
</template>
