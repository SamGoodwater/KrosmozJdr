<script setup>
/**
 * ShopViewLarge — Vue Large pour Shop
 * 
 * @description
 * Vue complète d'une boutique avec toutes les informations affichées.
 * Utilisée dans les grandes modals ou directement dans le main.
 * 
 * @props {Shop} shop - Instance du modèle Shop
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
    shop: {
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
const { downloadPdf } = useDownloadPdf('shop');

// Champs à afficher dans la vue large
const extendedFields = computed(() => {
    const fields = [
        'name',
        'description',
        'location',
        'npc_name',
        'items_count',
        'price',
        'usable',
        'is_visible',
    ];
    
    if (props.shop.canView) {
        fields.push('image', 'created_by', 'created_at', 'updated_at');
    }
    
    return fields;
});

const getFieldLabel = (fieldKey) => {
    const labels = {
        name: 'Nom',
        description: 'Description',
        location: 'Localisation',
        npc_name: 'PNJ',
        items_count: 'Nb objets',
        price: 'Prix',
        usable: 'Utilisable',
        is_visible: 'Visible',
        image: 'Image',
        created_by: 'Créé par',
        created_at: 'Créé le',
        updated_at: 'Modifié le',
    };
    return labels[fieldKey] || fieldKey;
};

const getFieldIcon = (fieldKey) => {
    const icons = {
        name: 'fa-solid fa-font',
        description: 'fa-solid fa-align-left',
        location: 'fa-solid fa-map-marker-alt',
        npc_name: 'fa-solid fa-user-ninja',
        items_count: 'fa-solid fa-boxes',
        price: 'fa-solid fa-coins',
        usable: 'fa-solid fa-check-circle',
        is_visible: 'fa-solid fa-eye',
        image: 'fa-solid fa-image',
        created_by: 'fa-solid fa-user',
        created_at: 'fa-solid fa-calendar',
        updated_at: 'fa-solid fa-clock',
    };
    return icons[fieldKey] || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.shop.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
    });
};

const handleAction = async (actionKey) => {
    const shopId = props.shop.id;
    if (!shopId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.shops.show', { shop: shopId }));
            emit('view', props.shop);
            break;
        case 'edit':
            router.visit(route('entities.shops.edit', { shop: shopId }));
            emit('edit', props.shop);
            break;
        case 'quick-edit':
            emit('quick-edit', props.shop);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('shop');
            const url = resolveEntityRouteUrl('shop', 'show', shopId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de la boutique copié !");
            }
            emit('copy-link', props.shop);
            break;
        }
        case 'download-pdf':
            await downloadPdf(shopId);
            emit('download-pdf', props.shop);
            break;
        case 'refresh':
            router.reload({ only: ['shops'] });
            emit('refresh', props.shop);
            break;
        case 'delete':
            emit('delete', props.shop);
            break;
    }
};
</script>

<template>
    <div class="space-y-6">
        <!-- En-tête avec nom et actions -->
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3">
                    <Image
                        v-if="shop.image"
                        :src="shop.image"
                        :alt="shop.name || 'Shop'"
                        class="w-16 h-16 rounded-lg object-cover flex-shrink-0"
                    />
                    <h2 class="text-2xl font-bold text-primary-100 break-words">
                        <CellRenderer
                            :cell="getCell('name')"
                            ui-color="primary"
                        />
                    </h2>
                </div>
                <p v-if="shop.description" class="text-primary-300 mt-2 break-words">
                    {{ shop.description }}
                </p>
            </div>
            
            <!-- Actions en haut à droite -->
            <div v-if="showActions" class="flex-shrink-0">
                <EntityActions
                    entity-type="shop"
                    :entity="shop"
                    format="buttons"
                    display="icon-only"
                    size="sm"
                    color="primary"
                    :context="{ inPanel: false, inPage: true }"
                    @action="handleAction"
                />
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
