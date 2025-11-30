<script setup>
/**
 * PanoplyItemsManager Molecule
 * 
 * @description
 * Composant pour gérer les items d'une panoplie (ajout/retrait)
 * 
 * @props {Array} items - Liste des items actuellement dans la panoplie
 * @props {Array} availableItems - Liste de tous les items disponibles (optionnel, pour recherche)
 */
import { ref, computed, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';

const props = defineProps({
    items: {
        type: Array,
        default: () => []
    },
    availableItems: {
        type: Array,
        default: () => []
    },
    panoplyId: {
        type: Number,
        required: true
    }
});

const emit = defineEmits(['update:items']);

const notificationStore = useNotificationStore();

// Items locaux (copie pour modification)
const localItems = ref([...props.items]);

// Recherche d'items
const searchQuery = ref('');

// Items filtrés selon la recherche
const filteredAvailableItems = computed(() => {
    if (!searchQuery.value || !props.availableItems.length) {
        return [];
    }
    const query = searchQuery.value.toLowerCase();
    return props.availableItems.filter(item => {
        const name = item.name?.toLowerCase() || '';
        const description = item.description?.toLowerCase() || '';
        return (name.includes(query) || description.includes(query)) &&
               !localItems.value.some(selectedItem => selectedItem.id === item.id);
    });
});

// Ajouter un item à la panoplie
const addItem = (item) => {
    if (!localItems.value.some(i => i.id === item.id)) {
        localItems.value.push(item);
        emit('update:items', localItems.value);
        searchQuery.value = '';
        notificationStore.success(`Item "${item.name}" ajouté à la panoplie`, {
            duration: 3000,
            placement: 'top-center'
        });
    }
};

// Retirer un item de la panoplie
const removeItem = (itemId) => {
    const item = localItems.value.find(i => i.id === itemId);
    if (item) {
        localItems.value = localItems.value.filter(i => i.id !== itemId);
        emit('update:items', localItems.value);
        notificationStore.success(`Item "${item.name}" retiré de la panoplie`, {
            duration: 3000,
            placement: 'top-center'
        });
    }
};

// Mettre à jour les items locaux quand les props changent
watch(() => props.items, (newItems) => {
    localItems.value = [...newItems];
}, { deep: true });

// Formulaire pour la sauvegarde
const itemsForm = useForm({
    items: []
});

// Sauvegarder les changements
const saveItems = () => {
    const itemIds = localItems.value.map(item => item.id);
    itemsForm.items = itemIds;
    
    itemsForm.patch(route('entities.panoplies.updateItems', { panoply: props.panoplyId }), {
        preserveScroll: true,
        onSuccess: () => {
            notificationStore.success('Items de la panoplie mis à jour avec succès', {
                duration: 3000,
                placement: 'top-center'
            });
        },
        onError: (errors) => {
            notificationStore.error('Erreur lors de la mise à jour des items', {
                duration: 5000,
                placement: 'top-center'
            });
            console.error('Erreurs:', errors);
        }
    });
};
</script>

<template>
    <Container class="space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold">Items de la panoplie</h3>
            <Badge :content="localItems.length.toString()" color="primary" />
        </div>

        <!-- Liste des items actuels -->
        <div v-if="localItems.length > 0" class="space-y-2">
            <div
                v-for="item in localItems"
                :key="item.id"
                class="flex items-center justify-between p-3 bg-base-200 rounded-lg hover:bg-base-300 transition-colors"
            >
                <div class="flex-1">
                    <div class="font-medium">{{ item.name }}</div>
                    <div v-if="item.description" class="text-sm text-base-content/70 line-clamp-1">
                        {{ item.description }}
                    </div>
                    <div v-if="item.level" class="text-xs text-base-content/50 mt-1">
                        Niveau {{ item.level }}
                    </div>
                </div>
                <Tooltip content="Retirer cet item" placement="top">
                    <Btn
                        @click="removeItem(item.id)"
                        variant="ghost"
                        size="sm"
                        color="error"
                    >
                        <i class="fa-solid fa-times"></i>
                    </Btn>
                </Tooltip>
            </div>
        </div>
        <div v-else class="text-center py-8 text-base-content/50">
            <p>Aucun item dans cette panoplie</p>
        </div>

        <!-- Recherche et ajout d'items -->
        <div class="border-t pt-4">
            <div class="space-y-2">
                <InputField
                    v-model="searchQuery"
                    label="Rechercher un item à ajouter"
                    placeholder="Tapez le nom d'un item..."
                >
                    <template #helper>
                        <span class="text-xs text-base-content/60">
                            Recherchez parmi les items disponibles
                        </span>
                    </template>
                </InputField>

                <!-- Résultats de recherche -->
                <div
                    v-if="searchQuery && filteredAvailableItems.length > 0"
                    class="max-h-60 overflow-y-auto border border-base-300 rounded-lg bg-base-100 shadow-lg z-10"
                >
                    <div
                        v-for="item in filteredAvailableItems.slice(0, 10)"
                        :key="item.id"
                        @mousedown.prevent="addItem(item)"
                        class="p-3 hover:bg-base-200 cursor-pointer transition-colors border-b border-base-300 last:border-b-0"
                    >
                        <div class="font-medium">{{ item.name }}</div>
                        <div v-if="item.description" class="text-sm text-base-content/70 line-clamp-2 mt-1">
                            {{ item.description }}
                        </div>
                        <div v-if="item.level" class="text-xs text-base-content/50 mt-1">
                            Niveau {{ item.level }}
                        </div>
                    </div>
                    <div v-if="filteredAvailableItems.length > 10" class="p-2 text-center text-xs text-base-content/50">
                        {{ filteredAvailableItems.length - 10 }} autres résultats...
                    </div>
                </div>
                <div
                    v-else-if="searchQuery && filteredAvailableItems.length === 0"
                    class="p-4 text-center text-base-content/50 text-sm border border-base-300 rounded-lg"
                >
                    Aucun item trouvé ou tous les items sont déjà dans la panoplie
                </div>
            </div>
        </div>

        <!-- Bouton de sauvegarde -->
        <div class="flex justify-end pt-4 border-t">
            <Btn
                @click="saveItems"
                color="primary"
                :disabled="itemsForm.processing || JSON.stringify(localItems.map(i => i.id).sort()) === JSON.stringify(props.items.map(i => i.id).sort())"
            >
                <i class="fa-solid fa-save mr-2"></i>
                {{ itemsForm.processing ? 'Sauvegarde...' : 'Sauvegarder les modifications' }}
            </Btn>
        </div>
    </Container>
</template>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

