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
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import TextareaField from '@/Pages/Molecules/data-input/TextareaField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import FileField from '@/Pages/Molecules/data-input/FileField.vue';
import ToggleCore from '@/Pages/Atoms/data-input/ToggleCore.vue';
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
    },
    differentFields: {
        type: Array,
        default: () => []
    },
    /**
     * Override optionnel pour les routes (utile pour les "types" ou routes non standards).
     *
     * @example
     * routeNameBase="entities.resource-types"
     * routeParamKey="resourceType"
     */
    routeNameBase: {
        type: String,
        default: null
    },
    routeParamKey: {
        type: String,
        default: null
    }
});

const emit = defineEmits(['submit', 'cancel', 'update:viewMode']);

const notificationStore = useNotificationStore();

// Mode d'affichage local (peut être modifié par l'utilisateur)
const localViewMode = ref(props.viewMode);

/**
 * Multi-edit quand on n'a pas d'ID et qu'on est en mode update.
 * Dans ce mode, on ne doit envoyer que les champs explicitement modifiés.
 */
const isMultiEdit = computed(() => {
    const entityId = props.entity?.id ?? null;
    return !entityId && Boolean(props.isUpdating);
});

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
    // Si c'est une création (pas d'ID), utiliser les valeurs par défaut
    if (!props.isUpdating || !props.entity?.id) {
        const formData = {};
        Object.keys(fieldsConfig.value).forEach(key => {
            // En multi-edit: si le champ fait partie des champs différents, valeur neutre
            // => affiche "Valeurs différentes" et évite de pré-remplir une valeur arbitraire.
            if (isMultiEdit.value && props.differentFields.includes(key)) {
                formData[key] = getDefaultValue(fieldsConfig.value[key].type);
                return;
            }
            // Utiliser la valeur de l'entité si fournie, sinon valeur par défaut
            formData[key] = props.entity[key] !== undefined 
                ? props.entity[key] 
                : getDefaultValue(fieldsConfig.value[key].type);
        });
        return formData;
    }
    
    // Si l'entité est une instance de modèle avec toFormData(), l'utiliser
    if (props.entity && typeof props.entity.toFormData === 'function') {
        const modelFormData = props.entity.toFormData();
        const formData = {};
        Object.keys(fieldsConfig.value).forEach(key => {
            // Utiliser les données du modèle si disponibles, sinon valeur par défaut
            formData[key] = modelFormData[key] !== undefined 
                ? modelFormData[key] 
                : (props.entity[key] !== undefined ? props.entity[key] : getDefaultValue(fieldsConfig.value[key].type));
        });
        return formData;
    }
    
    // Sinon, utiliser l'accès direct aux propriétés (compatibilité avec objets bruts)
    // Pour l'édition multiple, l'entité peut être un objet simple avec les valeurs communes
    const formData = {};
    Object.keys(fieldsConfig.value).forEach(key => {
        formData[key] = props.entity[key] !== undefined 
            ? props.entity[key] 
            : getDefaultValue(fieldsConfig.value[key].type);
    });
    return formData;
};

const getDefaultValue = (type) => {
    switch (type) {
        case 'number': return null;
        case 'checkbox': return false;
        case 'select': return null;
        case 'file': return null;
        default: return '';
    }
};

const form = useForm(initializeForm());
/**
 * Snapshot des valeurs "initiales" du formulaire (au moment de l'ouverture / dernière synchro).
 * Utilisé par le bouton Reset.
 */
const initialSnapshot = ref(initializeForm());

/**
 * Track des bools modifiés en UI (utile pour gérer un état indeterminate en mode multi-edit).
 * @type {Record<string, boolean>}
 */
const checkboxDirty = ref({});

/**
 * Track des champs non-bool modifiés en multi-edit.
 * @type {Record<string, boolean>}
 */
const fieldDirty = ref({});

/**
 * Marque un champ comme modifié (multi-edit).
 * @param {string} key
 */
const markDirty = (key) => {
    if (!isMultiEdit.value) return;
    if (!props.differentFields.includes(key)) return;
    fieldDirty.value = { ...(fieldDirty.value || {}), [key]: true };
};

/**
 * Annule la modification d'un champ (multi-edit) => ne pas modifier.
 * @param {string} key
 * @param {string} type
 */
const resetFieldMultiEdit = (key, type) => {
    if (!isMultiEdit.value) return;
    if (!props.differentFields.includes(key)) return;
    fieldDirty.value = { ...(fieldDirty.value || {}), [key]: false };
    form[key] = getDefaultValue(type);
};

/**
 * Reset d'un champ bool en mode multi-edit : revient à l'état indeterminate (non modifié).
 *
 * @param {string} key
 * @returns {void}
 */
const resetBoolMultiEdit = (key) => {
    if (!key) return;
    checkboxDirty.value = { ...(checkboxDirty.value || {}), [key]: false };
    // Important: en mode "valeurs différentes", `form[key]` n'est pas une valeur fiable.
    // L'état indeterminate (= ne pas modifier) est piloté par `checkboxDirty`.
    form[key] = false;
};

/**
 * Reset global du formulaire :
 * - revient aux valeurs chargées au moment de l'ouverture (ou dernière synchro entity->form)
 * - remet les champs multi-edit en mode "ne pas modifier" (dirty=false)
 */
const resetForm = () => {
    const snap = initialSnapshot.value || {};

    for (const key of Object.keys(fieldsConfig.value || {})) {
        if (Object.prototype.hasOwnProperty.call(snap, key)) {
            form[key] = snap[key];
        } else {
            form[key] = getDefaultValue(fieldsConfig.value?.[key]?.type);
        }
    }

    checkboxDirty.value = {};
    fieldDirty.value = {};
    form.clearErrors?.();
};

// Mise à jour du formulaire quand l'entité change
watch(() => props.entity, () => {
    const formData = initializeForm();
    initialSnapshot.value = formData;
    Object.keys(formData).forEach(key => {
        form[key] = formData[key];
    });
}, { deep: true });

/**
 * Raccourcis clavier
 */
const handleKeydown = (event) => {
    // Ctrl+S / Cmd+S : Sauvegarder
    if ((event.ctrlKey || event.metaKey) && event.key === 's') {
        event.preventDefault();
        if (!form.processing) {
            submit();
        }
        return;
    }

    // Esc : Annuler / Fermer
    if (event.key === 'Escape') {
        event.preventDefault();
        emit('cancel');
        return;
    }

    // Ctrl+Z / Cmd+Z : Réinitialiser (si possible)
    if ((event.ctrlKey || event.metaKey) && event.key === 'z' && !event.shiftKey) {
        // Ne pas intercepter si on est dans un input/textarea (laisser le navigateur gérer)
        const target = event.target;
        if (target && (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA')) {
            return;
        }
        event.preventDefault();
        resetForm();
        return;
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
});

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
    // Si l'entité n'a pas d'ID (édition multiple), émettre directement les données
    const entityId = props.entity?.id ?? null;
    if (!entityId && props.isUpdating) {
        // Mode édition multiple : n'émettre que les champs explicitement modifiés.
        // (tri-state => indeterminate = ne pas modifier)
        const data = form.data();
        for (const key of (props.differentFields || [])) {
            const type = fieldsConfig.value?.[key]?.type;
            if (type === 'checkbox') {
                if (!checkboxDirty.value?.[key]) delete data[key];
            } else {
                if (!fieldDirty.value?.[key]) delete data[key];
            }
        }
        emit('submit', data);
        return;
    }

    // Construction du nom de route selon le type d'entité
    // Note: Par défaut, les routes utilisent le pluriel (items, spells, monsters, panoplies)
    const entityTypePlural = props.entityType === 'panoply' ? 'panoplies' : `${props.entityType}s`;
    const base = props.routeNameBase || `entities.${entityTypePlural}`;
    const routeName = props.isUpdating ? `${base}.update` : `${base}.store`;
    
    // Paramètres de route selon le type d'entité
    const routeParamKey = props.routeParamKey || props.entityType;
    const routeParams = props.isUpdating ? { [routeParamKey]: entityId } : {};

    const method = props.isUpdating ? 'patch' : 'post';
    
    form[method](route(routeName, routeParams), {
        preserveScroll: true,
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
    // Ne pas rediriger si c'est dans un modal (le modal gère la fermeture)
    // Seulement rediriger si c'est une page d'édition
    if (props.isUpdating) {
        const entityTypePlural = props.entityType === 'panoply' ? 'panoplies' : `${props.entityType}s`;
        const entityId = props.entity?.id ?? null;
        if (entityId) {
            router.visit(route(`entities.${entityTypePlural}.show`, { [props.entityType]: entityId }));
        }
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
                    <div :class="{ 'opacity-60': props.differentFields.includes(fieldKey) }">
                        <Tooltip 
                            :content="props.differentFields.includes(fieldKey) ? `${fieldConfig.label} (valeurs différentes)` : fieldConfig.label" 
                            placement="top"
                        >
                            <!-- InputField -->
                            <InputField
                                v-if="fieldConfig.type === 'text' || !['textarea', 'select', 'file', 'number', 'checkbox'].includes(fieldConfig.type)"
                                v-model="form[fieldKey]"
                                @update:modelValue="() => markDirty(fieldKey)"
                                :label="fieldConfig.label"
                                :type="fieldConfig.type || 'text'"
                                :required="fieldConfig.required"
                                :validation="getFieldValidation(fieldKey)"
                                :placeholder="props.differentFields.includes(fieldKey) ? 'Valeurs différentes' : undefined"
                            >
                                <template
                                    v-if="isMultiEdit && props.differentFields.includes(fieldKey) && fieldDirty.value?.[fieldKey]"
                                    #overEnd
                                >
                                    <Btn
                                        size="xs"
                                        variant="ghost"
                                        title="Annuler la modification (ne pas modifier ce champ)"
                                        @click.stop="resetFieldMultiEdit(fieldKey, fieldConfig.type || 'text')"
                                    >
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </Btn>
                                </template>
                            </InputField>
                            
                            <!-- TextareaField -->
                            <TextareaField
                                v-else-if="fieldConfig.type === 'textarea'"
                                v-model="form[fieldKey]"
                                @update:modelValue="() => markDirty(fieldKey)"
                                :label="fieldConfig.label"
                                :required="fieldConfig.required"
                                :validation="getFieldValidation(fieldKey)"
                                :placeholder="props.differentFields.includes(fieldKey) ? 'Valeurs différentes' : undefined"
                            >
                                <template
                                    v-if="isMultiEdit && props.differentFields.includes(fieldKey) && fieldDirty.value?.[fieldKey]"
                                    #overEnd
                                >
                                    <Btn
                                        size="xs"
                                        variant="ghost"
                                        title="Annuler la modification (ne pas modifier ce champ)"
                                        @click.stop="resetFieldMultiEdit(fieldKey, 'textarea')"
                                    >
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </Btn>
                                </template>
                            </TextareaField>
                            
                            <!-- SelectField -->
                            <SelectField
                                v-else-if="fieldConfig.type === 'select'"
                                v-model="form[fieldKey]"
                                @update:modelValue="() => markDirty(fieldKey)"
                                :label="fieldConfig.label"
                                :options="fieldConfig.options || []"
                                :required="fieldConfig.required"
                                :validation="getFieldValidation(fieldKey)"
                                :placeholder="props.differentFields.includes(fieldKey) ? 'Valeurs différentes' : undefined"
                            >
                                <template
                                    v-if="isMultiEdit && props.differentFields.includes(fieldKey) && fieldDirty.value?.[fieldKey]"
                                    #overEnd
                                >
                                    <Btn
                                        size="xs"
                                        variant="ghost"
                                        title="Annuler la modification (ne pas modifier ce champ)"
                                        @click.stop="resetFieldMultiEdit(fieldKey, 'select')"
                                    >
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </Btn>
                                </template>
                            </SelectField>
                            
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
                                @update:modelValue="() => markDirty(fieldKey)"
                                :label="fieldConfig.label"
                                type="number"
                                :required="fieldConfig.required"
                                :validation="getFieldValidation(fieldKey)"
                                :placeholder="props.differentFields.includes(fieldKey) ? 'Valeurs différentes' : undefined"
                            >
                                <template
                                    v-if="isMultiEdit && props.differentFields.includes(fieldKey) && fieldDirty.value?.[fieldKey]"
                                    #overEnd
                                >
                                    <Btn
                                        size="xs"
                                        variant="ghost"
                                        title="Annuler la modification (ne pas modifier ce champ)"
                                        @click.stop="resetFieldMultiEdit(fieldKey, 'number')"
                                    >
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </Btn>
                                </template>
                            </InputField>
                            
                            <!-- Bool (Toggle) -->
                            <div v-else-if="fieldConfig.type === 'checkbox'" class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <ToggleCore
                                        variant="glass"
                                        size="sm"
                                        color="primary"
                                        :model-value="Boolean(form[fieldKey])"
                                        :indeterminate="props.differentFields.includes(fieldKey) && !checkboxDirty.value?.[fieldKey]"
                                        @update:model-value="(v) => { checkboxDirty.value = { ...(checkboxDirty.value || {}), [fieldKey]: true }; form[fieldKey] = Boolean(v); }"
                                    />
                                    <span
                                        class="text-sm transition-colors duration-200 flex items-center gap-1"
                                        :class="{
                                            'opacity-80': !(props.differentFields.includes(fieldKey) && !checkboxDirty.value?.[fieldKey]),
                                            'text-warning font-semibold': props.differentFields.includes(fieldKey) && !checkboxDirty.value?.[fieldKey],
                                        }"
                                    >
                                        <template v-if="props.differentFields.includes(fieldKey) && !checkboxDirty.value?.[fieldKey]">
                                            <Icon source="fa-solid fa-exclamation-triangle" alt="Valeurs différentes" size="xs" />
                                            Valeurs différentes
                                        </template>
                                        <template v-else>
                                            {{ Boolean(form[fieldKey]) ? "Oui" : "Non" }}
                                        </template>
                                    </span>
                                </div>

                                <Btn
                                    v-if="props.differentFields.includes(fieldKey) && checkboxDirty.value?.[fieldKey]"
                                    size="xs"
                                    variant="ghost"
                                    title="Annuler la modification (ne pas modifier ce champ)"
                                    @click.stop="resetBoolMultiEdit(fieldKey)"
                                >
                                    <i class="fa-solid fa-rotate-left"></i>
                                </Btn>
                            </div>
                        </Tooltip>
                    </div>
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

                <Tooltip
                    content="Réinitialise le formulaire : revient aux valeurs chargées au moment de l’ouverture (ou dernière synchro). En multi‑édition, remet les champs ‘valeurs différentes’ en mode ‘ne pas modifier’."
                    placement="top"
                >
                    <Btn
                        type="button"
                        variant="ghost"
                        @click="resetForm"
                    >
                        <i class="fa-solid fa-arrow-rotate-left mr-2"></i>
                        Reset
                    </Btn>
                </Tooltip>
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

