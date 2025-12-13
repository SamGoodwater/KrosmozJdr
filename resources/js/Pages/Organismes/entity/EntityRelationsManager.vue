<script setup>
/**
 * EntityRelationsManager Organism
 * 
 * @description
 * Composant générique pour gérer les relations many-to-many d'une entité
 * (ajout/retrait d'éléments liés)
 * 
 * @props {Array} relations - Liste des éléments actuellement liés
 * @props {Array} availableItems - Liste de tous les éléments disponibles (pour recherche)
 * @props {Number} entityId - ID de l'entité principale
 * @props {String} entityType - Type d'entité (panoply, creature, scenario, etc.)
 * @props {String} relationType - Type de relation (items, spells, resources, etc.)
 * @props {String} relationName - Nom de la relation (pour les labels et routes)
 * @props {Object} config - Configuration optionnelle (displayFields, searchFields, etc.)
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
    relations: {
        type: Array,
        default: () => []
    },
    availableItems: {
        type: Array,
        default: () => []
    },
    entityId: {
        type: Number,
        required: true
    },
    entityType: {
        type: String,
        required: true
    },
    relationType: {
        type: String,
        required: true
    },
    relationName: {
        type: String,
        required: true
    },
    config: {
        type: Object,
        default: () => ({
            displayFields: ['name', 'description', 'level'],
            searchFields: ['name', 'description'],
            routeName: null, // Si null, construit automatiquement : entities.{entityType}.update{RelationType}
            itemLabel: 'élément',
            itemLabelPlural: 'éléments',
            pivotFields: null // Array de champs de pivot (ex: ['quantity'] ou ['quantity', 'price', 'comment'])
        })
    }
});

const emit = defineEmits(['update:relations']);

const notificationStore = useNotificationStore();

// Relations locales (copie pour modification)
const localRelations = ref([...props.relations]);

// Valeurs de pivot pour chaque relation (item_id => { field: value })
const pivotValues = ref({});

// Initialiser les valeurs de pivot depuis les relations existantes
const initializePivotValues = () => {
    if (!props.config.pivotFields || props.config.pivotFields.length === 0) {
        return;
    }
    
    const pivots = {};
    props.relations.forEach(item => {
        if (item.pivot) {
            pivots[item.id] = {};
            props.config.pivotFields.forEach(field => {
                pivots[item.id][field] = item.pivot[field] || '';
            });
        } else {
            // Initialiser avec des valeurs vides
            pivots[item.id] = {};
            props.config.pivotFields.forEach(field => {
                pivots[item.id][field] = '';
            });
        }
    });
    pivotValues.value = pivots;
};

// Initialiser au montage
initializePivotValues();

// Recherche
const searchQuery = ref('');

// Éléments filtrés selon la recherche
const filteredAvailableItems = computed(() => {
    if (!searchQuery.value || !props.availableItems.length) {
        return [];
    }
    const query = searchQuery.value.toLowerCase();
    const searchFields = props.config.searchFields || ['name', 'description'];
    
    return props.availableItems.filter(item => {
        const isAlreadySelected = localRelations.value.some(
            selectedItem => selectedItem.id === item.id
        );
        if (isAlreadySelected) return false;
        
        return searchFields.some(field => {
            const value = item[field];
            return value && String(value).toLowerCase().includes(query);
        });
    });
});

// Construire le nom de la route
const routeName = computed(() => {
    if (props.config.routeName) {
        return props.config.routeName;
    }
    // Construire automatiquement : entities.panoplies.updateItems
    // Pour "items" -> "Items", pour "classes" -> "Classes", etc.
    // Gérer les cas spéciaux comme "spellTypes" -> "SpellTypes"
    let relationTypeCamel = props.relationType;
    // Si le type contient plusieurs mots (camelCase), capitaliser chaque mot
    if (relationTypeCamel.includes(/([a-z])([A-Z])/)) {
        // Déjà en camelCase, juste capitaliser la première lettre
        relationTypeCamel = relationTypeCamel.charAt(0).toUpperCase() + relationTypeCamel.slice(1);
    } else {
        // Simple, juste capitaliser la première lettre
        relationTypeCamel = relationTypeCamel.charAt(0).toUpperCase() + relationTypeCamel.slice(1);
    }
    
    // Par convention dans ce projet, `entityType` est déjà au pluriel dans les usages
    // (ex: shops, scenarios, campaigns). On garde un fallback si quelqu'un passe le singulier.
    const entityTypePlural = props.entityType.endsWith('s')
        ? props.entityType
        : (props.entityType.endsWith('y') ? props.entityType.slice(0, -1) + 'ies' : props.entityType + 's');

    return `entities.${entityTypePlural}.update${relationTypeCamel}`;
});

// Ajouter un élément à la relation
const addItem = (item) => {
    if (!localRelations.value.some(i => i.id === item.id)) {
        localRelations.value.push(item);
        
        // Initialiser les valeurs de pivot si nécessaire
        if (props.config.pivotFields && props.config.pivotFields.length > 0) {
            if (!pivotValues.value[item.id]) {
                pivotValues.value[item.id] = {};
                props.config.pivotFields.forEach(field => {
                    pivotValues.value[item.id][field] = '';
                });
            }
        }
        
        emit('update:relations', localRelations.value);
        searchQuery.value = '';
        notificationStore.success(
            `${props.config.itemLabel.charAt(0).toUpperCase() + props.config.itemLabel.slice(1)} "${item.name || item.id}" ajouté`,
            {
                duration: 3000,
                placement: 'top-center'
            }
        );
    }
};

// Retirer un élément de la relation
const removeItem = (itemId) => {
    const item = localRelations.value.find(i => i.id === itemId);
    if (item) {
        localRelations.value = localRelations.value.filter(i => i.id !== itemId);
        
        // Supprimer les valeurs de pivot
        if (pivotValues.value[itemId]) {
            delete pivotValues.value[itemId];
        }
        
        emit('update:relations', localRelations.value);
        notificationStore.success(
            `${props.config.itemLabel.charAt(0).toUpperCase() + props.config.itemLabel.slice(1)} "${item.name || item.id}" retiré`,
            {
                duration: 3000,
                placement: 'top-center'
            }
        );
    }
};

// Mettre à jour les relations locales quand les props changent
watch(() => props.relations, (newRelations) => {
    localRelations.value = [...newRelations];
    initializePivotValues();
}, { deep: true });

// Formulaire pour la sauvegarde
const relationsForm = useForm({
    [props.relationType]: []
});

// Sauvegarder les changements
const saveRelations = () => {
    const itemIds = localRelations.value.map(item => item.id);
    
    // Si des pivots sont définis, envoyer les données avec les pivots
    if (props.config.pivotFields && props.config.pivotFields.length > 0) {
        // Format: { item_id: { pivot_field: value } }
        const dataWithPivots = {};
        itemIds.forEach(itemId => {
            dataWithPivots[itemId] = {};
            props.config.pivotFields.forEach(field => {
                const value = pivotValues.value[itemId]?.[field] || '';
                // Convertir en nombre si c'est quantity ou price
                if ((field === 'quantity' || field === 'price') && value !== '') {
                    dataWithPivots[itemId][field] = Number(value) || 0;
                } else {
                    dataWithPivots[itemId][field] = value;
                }
            });
        });
        relationsForm[props.relationType] = dataWithPivots;
    } else {
        // Pas de pivot, format simple (array d'IDs)
        relationsForm[props.relationType] = itemIds;
    }
    
    // Construire les paramètres de route (panoply pour panoplies, creature pour creatures, etc.)
    const entityParamName = props.entityType.endsWith('ies') 
        ? props.entityType.slice(0, -3) + 'y' 
        : props.entityType.replace(/s$/, '');
    const routeParams = { [entityParamName]: props.entityId };
    
    relationsForm.patch(route(routeName.value, routeParams), {
        preserveScroll: true,
        onSuccess: () => {
            notificationStore.success(
                `${props.config.itemLabelPlural.charAt(0).toUpperCase() + props.config.itemLabelPlural.slice(1)} mis à jour avec succès`,
                {
                    duration: 3000,
                    placement: 'top-right'
                }
            );
        },
        onError: (errors) => {
            notificationStore.error('Erreur lors de la mise à jour', {
                duration: 5000,
                placement: 'top-center'
            });
            console.error('Erreurs:', errors);
        }
    });
};

// Afficher un champ d'un élément
const displayField = (item, field) => {
    if (field.includes('.')) {
        // Support pour les relations imbriquées (ex: 'itemType.name')
        const parts = field.split('.');
        let value = item;
        for (const part of parts) {
            value = value?.[part];
        }
        return value;
    }
    return item[field];
};
</script>

<template>
    <Container class="space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold">{{ relationName }}</h3>
            <Badge :content="localRelations.length.toString()" color="primary" />
        </div>

        <!-- Liste des éléments actuels -->
        <div v-if="localRelations.length > 0" class="space-y-2">
            <div
                v-for="item in localRelations"
                :key="item.id"
                class="p-3 bg-base-200 rounded-lg hover:bg-base-300 transition-colors"
            >
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="font-medium">
                            {{ displayField(item, config.displayFields[0] || 'name') }}
                        </div>
                        <div 
                            v-if="config.displayFields[1] && displayField(item, config.displayFields[1])" 
                            class="text-sm text-base-content/70 line-clamp-1"
                        >
                            {{ displayField(item, config.displayFields[1]) }}
                        </div>
                        <div 
                            v-if="config.displayFields[2] && displayField(item, config.displayFields[2])" 
                            class="text-xs text-base-content/50 mt-1"
                        >
                            {{ config.displayFields[2] === 'level' ? `Niveau ${displayField(item, config.displayFields[2])}` : displayField(item, config.displayFields[2]) }}
                        </div>
                        
                        <!-- Champs de pivot -->
                        <div v-if="config.pivotFields && config.pivotFields.length > 0" class="mt-3 space-y-2 pt-3 border-t border-base-300">
                            <div 
                                v-for="pivotField in config.pivotFields" 
                                :key="pivotField"
                                class="flex items-center gap-2"
                            >
                                <label class="text-xs font-medium text-base-content/70 w-20">
                                    {{ pivotField === 'quantity' ? 'Quantité' : pivotField === 'price' ? 'Prix' : pivotField === 'comment' ? 'Commentaire' : pivotField }}
                                </label>
                                <InputField
                                    v-if="pivotField !== 'comment'"
                                    v-model="pivotValues[item.id][pivotField]"
                                    :type="pivotField === 'quantity' || pivotField === 'price' ? 'number' : 'text'"
                                    :placeholder="pivotField === 'quantity' ? '1' : pivotField === 'price' ? '0' : ''"
                                    size="sm"
                                    class="flex-1"
                                />
                                <InputField
                                    v-else
                                    v-model="pivotValues[item.id][pivotField]"
                                    type="text"
                                    placeholder="Commentaire..."
                                    size="sm"
                                    class="flex-1"
                                />
                            </div>
                        </div>
                    </div>
                    <Tooltip :content="`Retirer cet ${config.itemLabel}`" placement="top">
                        <Btn
                            @click="removeItem(item.id)"
                            variant="ghost"
                            size="sm"
                            color="error"
                            class="flex-shrink-0"
                        >
                            <i class="fa-solid fa-times"></i>
                        </Btn>
                    </Tooltip>
                </div>
            </div>
        </div>
        <div v-else class="text-center py-8 text-base-content/50">
            <p>Aucun {{ config.itemLabel }} dans cette relation</p>
        </div>

        <!-- Recherche et ajout d'éléments -->
        <div class="border-t pt-4">
            <div class="space-y-2">
                <InputField
                    v-model="searchQuery"
                    :label="`Rechercher un ${config.itemLabel} à ajouter`"
                    :placeholder="`Tapez le nom d'un ${config.itemLabel}...`"
                >
                    <template #helper>
                        <span class="text-xs text-base-content/60">
                            Recherchez parmi les {{ config.itemLabelPlural }} disponibles
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
                        <div class="font-medium">
                            {{ displayField(item, config.displayFields[0] || 'name') }}
                        </div>
                        <div 
                            v-if="config.displayFields[1] && displayField(item, config.displayFields[1])" 
                            class="text-sm text-base-content/70 line-clamp-2 mt-1"
                        >
                            {{ displayField(item, config.displayFields[1]) }}
                        </div>
                        <div 
                            v-if="config.displayFields[2] && displayField(item, config.displayFields[2])" 
                            class="text-xs text-base-content/50 mt-1"
                        >
                            {{ config.displayFields[2] === 'level' ? `Niveau ${displayField(item, config.displayFields[2])}` : displayField(item, config.displayFields[2]) }}
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
                    Aucun {{ config.itemLabel }} trouvé ou tous les {{ config.itemLabelPlural }} sont déjà liés
                </div>
            </div>
        </div>

        <!-- Bouton de sauvegarde -->
        <div class="flex justify-end pt-4 border-t">
            <Btn
                @click="saveRelations"
                color="primary"
                :disabled="relationsForm.processing || JSON.stringify(localRelations.map(i => i.id).sort()) === JSON.stringify(props.relations.map(i => i.id).sort())"
            >
                <i class="fa-solid fa-save mr-2"></i>
                {{ relationsForm.processing ? 'Sauvegarde...' : 'Sauvegarder les modifications' }}
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

