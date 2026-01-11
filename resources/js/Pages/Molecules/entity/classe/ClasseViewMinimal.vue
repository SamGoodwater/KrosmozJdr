<script setup>
/**
 * ClasseViewMinimal — Vue Minimal pour Classe
 * 
 * @description
 * Petite carte qui s'étend au survol.
 * Utilisée dans des grilles, petites modals ou hovers.
 * 
 * @props {Classe} classe - Instance du modèle Classe
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Image from '@/Pages/Atoms/data-display/Image.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';

const props = defineProps({
    classe: {
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
const { downloadPdf } = useDownloadPdf('classe');

// Champs importants à afficher
const importantFields = computed(() => ['name', 'life', 'life_dice']);

// Champs supplémentaires à afficher au hover
const expandedFields = computed(() => [
    'specificity',
    'usable',
    'is_visible',
]);

const getFieldIcon = (fieldKey) => {
    const icons = {
        name: 'fa-solid fa-font',
        life: 'fa-solid fa-heart',
        life_dice: 'fa-solid fa-dice',
        specificity: 'fa-solid fa-star',
        usable: 'fa-solid fa-check-circle',
        is_visible: 'fa-solid fa-eye',
    };
    return icons[fieldKey] || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.classe.toCell(fieldKey, {
        size: 'sm',
        context: 'minimal',
    });
};

const tooltipForField = (fieldKey, cell) => {
    const labels = {
        name: 'Nom',
        life: 'Vie',
        life_dice: 'Dé de vie',
        specificity: 'Spécificité',
        usable: 'Utilisable',
        is_visible: 'Visible',
    };
    const label = labels[fieldKey] || fieldKey;
    const value = cell?.value || '-';
    return `${label} : ${value}`;
};

const handleAction = async (actionKey) => {
    const classeId = props.classe.id;
    if (!classeId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.classes.show', { classe: classeId }));
            emit('view', props.classe);
            break;
        case 'edit':
            router.visit(route('entities.classes.edit', { classe: classeId }));
            emit('edit', props.classe);
            break;
        case 'delete':
            emit('delete', props.classe);
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
            <!-- En-tête avec image/icône, nom et actions -->
            <div class="flex items-start justify-between gap-2 mb-2">
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <div v-if="classe.image || classe.icon" class="flex-shrink-0">
                        <Image
                            :src="classe.image || classe.icon"
                            :alt="classe.name || 'Classe'"
                            size="xs"
                            class="rounded"
                        />
                    </div>
                    <Tooltip :content="classe.name || 'Classe'" placement="top">
                        <span class="font-semibold text-primary-100 text-sm truncate block">
                            <CellRenderer
                                :cell="getCell('name')"
                                ui-color="primary"
                            />
                        </span>
                    </Tooltip>
                </div>
                
                <div v-if="showActions && isHovered" class="flex-shrink-0">
                    <EntityActions
                        entity-type="classe"
                        :entity="classe"
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
