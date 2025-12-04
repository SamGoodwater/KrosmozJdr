<script setup>
/**
 * EntityEditForm Organism
 * 
 * @description
 * Composant réutilisable pour l'édition des entités avec deux modes d'affichage :
 * - Mode Grand : Formulaire complet avec tous les champs visibles
 * - Mode Compact : Formulaire condensé avec champs essentiels uniquement
 * 
 * @props {Object} entity - Données de l'entité à éditer
 * @props {String} entityType - Type d'entité (item, spell, monster, etc.)
 * @props {String} viewMode - Mode d'affichage ('large' | 'compact'), défaut 'large'
 * @props {Object} fieldsConfig - Configuration des champs à afficher
 * @props {Boolean} isUpdating - Mode édition (true) ou création (false)
 */
import { ref, computed, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import TextareaField from '@/Pages/Molecules/data-input/TextareaField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import FileField from '@/Pages/Molecules/data-input/FileField.vue';
import CheckboxField from '@/Pages/Molecules/data-input/CheckboxField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';

const props = defineProps({
    entity: {
        type: Object,
        required: true
    },
    entityType: {
        type: String,
        required: true
    },
    viewMode: {
        type: String,
        default: 'large',
        validator: (value) => ['large', 'compact'].includes(value)
    },
    fieldsConfig: {
        type: Object,
        default: () => ({})
    },
    isUpdating: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['submit', 'cancel', 'update:viewMode']);

const notificationStore = useNotificationStore();

// Mode d'affichage local (peut être modifié par l'utilisateur)
const localViewMode = ref(props.viewMode);

// Configuration par défaut des champs selon le type d'entité
const defaultFieldsConfig = computed(() => {
    const baseFields = {
        name: { type: 'text', label: 'Nom', required: true, showInCompact: true },
        description: { type: 'textarea', label: 'Description', required: false, showInCompact: false },
    };

    // Configuration spécifique par type d'entité
    const entitySpecificFields = {
        item: {
            level: { type: 'number', label: 'Niveau', required: false, showInCompact: true },
            rarity: { type: 'select', label: 'Rareté', required: false, showInCompact: true, options: [
                { value: 'common', label: 'Commun' },
                { value: 'uncommon', label: 'Peu commun' },
                { value: 'rare', label: 'Rare' },
                { value: 'epic', label: 'Épique' },
                { value: 'legendary', label: 'Légendaire' }
            ]},
            image: { type: 'file', label: 'Image', required: false, showInCompact: false }
        },
        spell: {
            level: { type: 'number', label: 'Niveau', required: false, showInCompact: true },
            ap_cost: { type: 'number', label: 'Coût PA', required: false, showInCompact: true },
            range: { type: 'number', label: 'Portée', required: false, showInCompact: false },
            image: { type: 'file', label: 'Image', required: false, showInCompact: false }
        },
        monster: {
            level: { type: 'number', label: 'Niveau', required: false, showInCompact: true },
            life: { type: 'number', label: 'Vie', required: false, showInCompact: true },
            size: { type: 'number', label: 'Taille', required: false, showInCompact: false },
            is_boss: { type: 'checkbox', label: 'Boss', required: false, showInCompact: true }
        }
    };

    return {
        ...baseFields,
        ...(entitySpecificFields[props.entityType] || {})
    };
});

// Fusion de la configuration par défaut avec la configuration personnalisée
const fieldsConfig = computed(() => {
    return { ...defaultFieldsConfig.value, ...props.fieldsConfig };
});

// Filtrage des champs selon le mode d'affichage
const visibleFields = computed(() => {
    const fields = Object.entries(fieldsConfig.value);
    if (localViewMode.value === 'compact') {
        return fields.filter(([_, config]) => config.showInCompact !== false);
    }
    return fields;
});

// Initialisation du formulaire avec les données de l'entité
const initializeForm = () => {
    // Si l'entité est une instance de modèle avec toFormData(), l'utiliser
    if (props.entity && typeof props.entity.toFormData === 'function') {
        const modelFormData = props.entity.toFormData();
        const formData = {};
        Object.keys(fieldsConfig.value).forEach(key => {
            // Utiliser les données du modèle si disponibles, sinon valeur par défaut
            formData[key] = modelFormData[key] !== undefined 
                ? modelFormData[key] 
                : (props.entity[key] || getDefaultValue(fieldsConfig.value[key].type));
        });
        return formData;
    }
    
    // Sinon, utiliser l'accès direct aux propriétés (compatibilité avec objets bruts)
    const formData = {};
    Object.keys(fieldsConfig.value).forEach(key => {
        formData[key] = props.entity[key] || getDefaultValue(fieldsConfig.value[key].type);
    });
    return formData;
};

const getDefaultValue = (type) => {
    switch (type) {
        case 'number': return null;
        case 'checkbox': return false;
        case 'select': return null;
        default: return '';
    }
};

const form = useForm(initializeForm());

// Mise à jour du formulaire quand l'entité change
watch(() => props.entity, () => {
    const formData = initializeForm();
    Object.keys(formData).forEach(key => {
        form[key] = formData[key];
    });
}, { deep: true });

// Validation pour chaque champ
const getFieldValidation = (fieldKey) => {
    if (!form.errors[fieldKey]) return null;
    return {
        state: 'error',
        message: form.errors[fieldKey],
        showNotification: false
    };
};

// Soumission du formulaire
const submit = () => {
    // Construction du nom de route selon le type d'entité
    // Note: Les routes utilisent le pluriel (items, spells, monsters, panoplies)
    const entityTypePlural = props.entityType === 'panoply' ? 'panoplies' : `${props.entityType}s`;
    const routeName = props.isUpdating 
        ? `entities.${entityTypePlural}.update`
        : `entities.${entityTypePlural}.store`;
    
    // Paramètres de route selon le type d'entité
    // Gérer les instances de modèles et les objets bruts
    const entityId = props.entity?.id ?? null;
    const routeParams = props.isUpdating 
        ? { [props.entityType]: entityId }
        : {};

    form[props.isUpdating ? 'patch' : 'post'](route(routeName, routeParams), {
        onSuccess: () => {
            notificationStore.success(
                props.isUpdating ? 'Entité mise à jour avec succès' : 'Entité créée avec succès',
                { duration: 3000, placement: 'top-right' }
            );
            emit('submit', form.data());
        },
        onError: (errors) => {
            notificationStore.error(
                'Erreur lors de la sauvegarde',
                { duration: 5000, placement: 'top-right' }
            );
            console.error('Erreurs de validation:', errors);
        }
    });
};

// Annulation
const cancel = () => {
    emit('cancel');
    const entityTypePlural = props.entityType === 'panoply' ? 'panoplies' : `${props.entityType}s`;
    // Gérer les instances de modèles et les objets bruts
    const entityId = props.entity?.id ?? null;
    if (props.isUpdating) {
        router.visit(route(`entities.${entityTypePlural}.show`, { [props.entityType]: entityId }));
    } else {
        router.visit(route(`entities.${entityTypePlural}.index`));
    }
};

// Toggle du mode d'affichage
const toggleViewMode = () => {
    localViewMode.value = localViewMode.value === 'large' ? 'compact' : 'large';
    emit('update:viewMode', localViewMode.value);
};

</script>

<template>
    <Container :class="[
        'entity-edit-form',
        viewMode === 'compact' ? 'compact-mode' : 'large-mode'
    ]">
        <!-- En-tête avec toggle du mode -->
        <div class="flex flex-col gap-4 mb-6">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold">
                    {{ isUpdating ? 'Modifier' : 'Créer' }} {{ entityType }}
                </h2>
                <Btn
                    @click="toggleViewMode"
                    variant="ghost"
                    size="sm"
                    :title="localViewMode === 'large' ? 'Mode compact' : 'Mode grand'"
                >
                    <i :class="localViewMode === 'large' ? 'fa-solid fa-compress' : 'fa-solid fa-expand'"></i>
                    {{ localViewMode === 'large' ? 'Compact' : 'Grand' }}
                </Btn>
            </div>
        </div>

        <!-- Formulaire -->
        <form @submit.prevent="submit" class="space-y-6">
            <div :class="[
                'grid gap-6',
                localViewMode === 'compact' ? 'grid-cols-1 md:grid-cols-2' : 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3'
            ]">
                <template v-for="[fieldKey, fieldConfig] in visibleFields" :key="fieldKey">
                    <Tooltip :content="fieldConfig.label" placement="top">
                        <!-- InputField -->
                        <InputField
                            v-if="fieldConfig.type === 'text' || !['textarea', 'select', 'file', 'number', 'checkbox'].includes(fieldConfig.type)"
                            v-model="form[fieldKey]"
                            :label="fieldConfig.label"
                            :type="fieldConfig.type || 'text'"
                            :required="fieldConfig.required"
                            :validation="getFieldValidation(fieldKey)"
                        />
                        
                        <!-- TextareaField -->
                        <TextareaField
                            v-else-if="fieldConfig.type === 'textarea'"
                            v-model="form[fieldKey]"
                            :label="fieldConfig.label"
                            :required="fieldConfig.required"
                            :validation="getFieldValidation(fieldKey)"
                        />
                        
                        <!-- SelectField -->
                        <SelectField
                            v-else-if="fieldConfig.type === 'select'"
                            v-model="form[fieldKey]"
                            :label="fieldConfig.label"
                            :options="fieldConfig.options || []"
                            :required="fieldConfig.required"
                            :validation="getFieldValidation(fieldKey)"
                        />
                        
                        <!-- FileField -->
                        <FileField
                            v-else-if="fieldConfig.type === 'file'"
                            v-model="form[fieldKey]"
                            :label="fieldConfig.label"
                            :required="fieldConfig.required"
                            :validation="getFieldValidation(fieldKey)"
                        />
                        
                        <!-- NumberField (using InputField with type number) -->
                        <InputField
                            v-else-if="fieldConfig.type === 'number'"
                            v-model="form[fieldKey]"
                            :label="fieldConfig.label"
                            type="number"
                            :required="fieldConfig.required"
                            :validation="getFieldValidation(fieldKey)"
                        />
                        
                        <!-- CheckboxField -->
                        <CheckboxField
                            v-else-if="fieldConfig.type === 'checkbox'"
                            v-model="form[fieldKey]"
                            :label="fieldConfig.label"
                            :required="fieldConfig.required"
                            :validation="getFieldValidation(fieldKey)"
                        />
                    </Tooltip>
                </template>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-end pt-6 border-t">
                <Btn
                    type="button"
                    variant="outline"
                    @click="cancel"
                >
                    Annuler
                </Btn>
                <Btn
                    type="submit"
                    color="primary"
                    :disabled="form.processing"
                >
                    <i class="fa-solid fa-save mr-2"></i>
                    {{ form.processing ? 'Enregistrement...' : (isUpdating ? 'Mettre à jour' : 'Créer') }}
                </Btn>
            </div>
        </form>
    </Container>
</template>

<style scoped lang="scss">
.entity-edit-form {
    &.compact-mode {
        // Styles pour le mode compact
        .grid {
            gap: 1rem;
        }
    }

    &.large-mode {
        // Styles pour le mode grand
        .grid {
            gap: 1.5rem;
        }
    }
}
</style>

