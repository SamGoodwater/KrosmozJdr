<script setup>
/**
 * SpellViewLarge — Vue Large pour Spell
 * 
 * @description
 * Vue complète d'un sort avec toutes les informations affichées.
 * Utilisée dans les grandes modals ou directement dans le main.
 * 
 * @props {Spell} spell - Instance du modèle Spell
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Image from '@/Pages/Atoms/data-display/Image.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
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

const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf('spell');

// Champs à afficher dans la vue large
const extendedFields = computed(() => {
    const fields = [
        'level',
        'pa',
        'po',
        'area',
        'element',
        'category',
        'cast_per_turn',
        'cast_per_target',
        'sight_line',
        'number_between_two_cast',
        'is_magic',
        'powerful',
        'usable',
        'is_visible',
        'auto_update',
        'dofusdb_id',
        'official_id',
        'effect',
        'spell_types',
    ];
    
    if (props.spell.canView) {
        fields.push('created_by', 'created_at', 'updated_at');
    }
    
    return fields;
});

const getFieldLabel = (fieldKey) => {
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
        number_between_two_cast: 'Entre deux lancers',
        is_magic: 'Magique',
        powerful: 'Puissant',
        usable: 'Utilisable',
        is_visible: 'Visibilité',
        auto_update: 'Mise à jour auto',
        dofusdb_id: 'ID DofusDB',
        official_id: 'ID Officiel',
        effect: 'Effet',
        spell_types: 'Types',
        created_by: 'Créé par',
        created_at: 'Créé le',
        updated_at: 'Modifié le',
    };
    return labels[fieldKey] || fieldKey;
};

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
        number_between_two_cast: 'fa-solid fa-clock',
        is_magic: 'fa-solid fa-wand-magic-sparkles',
        powerful: 'fa-solid fa-star',
        usable: 'fa-solid fa-check-circle',
        is_visible: 'fa-solid fa-eye',
        auto_update: 'fa-solid fa-sync',
        dofusdb_id: 'fa-solid fa-database',
        official_id: 'fa-solid fa-id-card',
        effect: 'fa-solid fa-magic',
        spell_types: 'fa-solid fa-tags',
        created_by: 'fa-solid fa-user',
        created_at: 'fa-solid fa-calendar',
        updated_at: 'fa-solid fa-clock',
    };
    return icons[fieldKey] || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.spell.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
    });
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
        case 'quick-edit':
            emit('quick-edit', props.spell);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('spell');
            const url = resolveEntityRouteUrl('spell', 'show', spellId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien du sort copié !");
            }
            emit('copy-link', props.spell);
            break;
        }
        case 'download-pdf':
            await downloadPdf(spellId);
            emit('download-pdf', props.spell);
            break;
        case 'refresh':
            router.reload({ only: ['spells'] });
            emit('refresh', props.spell);
            break;
        case 'delete':
            emit('delete', props.spell);
            break;
    }
};
</script>

<template>
    <div class="space-y-6">
        <!-- En-tête avec image, nom et actions -->
        <div class="flex flex-col md:flex-row gap-4 items-start">
            <!-- Image à gauche -->
            <div class="flex-shrink-0">
                <div v-if="spell.image" class="w-32 h-32 md:w-40 md:h-40">
                    <Image :source="spell.image" :alt="spell.name || 'Image'" size="lg" rounded="lg" class="w-full h-full object-cover" />
                </div>
                <div v-else class="w-32 h-32 md:w-40 md:h-40 flex items-center justify-center bg-base-200 rounded-lg">
                    <Icon source="fa-solid fa-wand-magic-sparkles" :alt="spell.name" size="xl" />
                </div>
            </div>
            
            <!-- Informations principales à droite -->
            <div class="flex-1 w-full">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl font-bold text-primary-100 break-words">{{ spell.name }}</h2>
                        <p v-if="spell.description" class="text-primary-300 mt-2 break-words">{{ spell.description }}</p>
                    </div>
                    
                    <!-- Actions en haut à droite -->
                    <div v-if="showActions" class="flex-shrink-0">
                        <EntityActions
                            entity-type="spell"
                            :entity="spell"
                            format="buttons"
                            display="icon-only"
                            size="sm"
                            color="primary"
                            :context="{ inPanel: false, inPage: true }"
                            @action="handleAction"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
                v-for="fieldKey in extendedFields"
                :key="fieldKey"
                class="p-3 bg-base-200 rounded-lg"
            >
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <Icon
                            :source="getFieldIcon(fieldKey)"
                            :alt="getFieldLabel(fieldKey)"
                            size="xs"
                            class="text-primary-400"
                        />
                        <span class="text-xs text-primary-400 uppercase font-semibold">
                            {{ getFieldLabel(fieldKey) }}
                        </span>
                    </div>
                    <div class="text-primary-100 break-words">
                        <CellRenderer
                            :cell="getCell(fieldKey)"
                            ui-color="primary"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
