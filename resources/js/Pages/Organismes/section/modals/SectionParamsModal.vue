<script setup>
/**
 * SectionParamsModal Component
 * 
 * @description
 * Modal pour configurer tous les paramètres d'une section.
 * - Paramètres communs : title, slug, order, is_visible, can_edit_role, state, classes, customCss
 * - Paramètres spécifiques au template : générés automatiquement depuis config.parameters
 * - Les data (contenu) sont modifiées directement dans le template d'édition
 * 
 * @props {Boolean} open - Contrôle l'ouverture du modal
 * @props {String} sectionTemplate - Template de section
 * @props {Object} section - Section complète (pour récupérer title, slug, etc.)
 * @props {Object} initialSettings - Settings initiaux
 * @emits close - Événement émis quand le modal se ferme
 * @emits validated - Événement émis avec { title, slug, order, is_visible, can_edit_role, state, settings } validés
 */
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import TextareaField from '@/Pages/Molecules/data-input/TextareaField.vue';
import ToggleField from '@/Pages/Molecules/data-input/ToggleField.vue';
import ColorField from '@/Pages/Molecules/data-input/ColorField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { useSectionAPI } from '../composables/useSectionAPI';
import { useSectionParameters } from '../composables/useSectionParameters';
import { getTemplateConfig } from '../templates';

const props = defineProps({
    open: {
        type: Boolean,
        default: false
    },
    sectionTemplate: {
        type: String,
        required: true,
        validator: (v) => ['text', 'image', 'gallery', 'video', 'entity_table'].includes(v)
    },
    section: {
        type: Object,
        default: () => ({})
    },
    initialSettings: {
        type: Object,
        default: () => ({})
    },
    sectionId: {
        type: [Number, String],
        default: null
    },
    sectionTitle: {
        type: String,
        default: ''
    }
});

const emit = defineEmits(['close', 'validated', 'deleted']);

// Composables
const { deleteSection } = useSectionAPI();
const { getCommonFields, getParameterFields } = useSectionParameters();

// Configuration du template
const templateConfig = computed(() => {
    try {
        return getTemplateConfig(props.sectionTemplate);
    } catch (e) {
        console.warn(`Impossible de charger la config du template "${props.sectionTemplate}"`, e);
        return null;
    }
});

// Champs communs et paramètres du template
const commonFields = computed(() => getCommonFields());
const templateParameters = computed(() => {
    if (!templateConfig.value || !templateConfig.value.parameters) {
        return [];
    }
    return getParameterFields(templateConfig.value.parameters);
});

/**
 * Extrait une valeur depuis la section (gère les modèles et les objets bruts)
 */
const getSectionValue = (key) => {
    if (!props.section) return null;
    
    // Si c'est un modèle Section, utiliser _data
    if (props.section._data) {
        return props.section._data[key] ?? null;
    }
    
    // Sinon, accès direct
    return props.section[key] ?? null;
};

/**
 * Génère un slug depuis le titre ou l'ID
 */
const generateSlug = (title, sectionId) => {
    if (title) {
        return title
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
    // Si pas de titre, utiliser l'ID
    if (sectionId) {
        return `section-${sectionId}`;
    }
    return '';
};

/**
 * Initialise les données du formulaire depuis les props
 */
const initializeFormData = () => {
    const sectionId = getSectionValue('id');
    const title = getSectionValue('title') || '';
    const existingSlug = getSectionValue('slug') || '';
    
    // Générer le slug si vide (depuis le titre ou l'ID)
    const slug = existingSlug || generateSlug(title, sectionId);
    
    return {
        // Paramètres communs
        title: title,
        slug: slug,
        order: getSectionValue('order') || 0,
        is_visible: getSectionValue('is_visible') || 'guest',
        can_edit_role: getSectionValue('can_edit_role') || 'admin',
        state: getSectionValue('state') || 'draft',
        // Settings (inclut classes et customCss)
        settings: {
            classes: props.initialSettings?.classes || '',
            customCss: props.initialSettings?.customCss || '',
            ...props.initialSettings,
        },
    };
};

// Données du formulaire
const formData = ref(initializeFormData());

// Réinitialiser les données quand les props changent
watch(() => [props.section, props.initialSettings, props.sectionTemplate], () => {
    const newData = initializeFormData();
    
    // Appliquer les valeurs par défaut des paramètres du template
    if (templateConfig.value?.parameters) {
        templateConfig.value.parameters.forEach(param => {
            if (newData.settings[param.key] === undefined && param.default !== undefined) {
                newData.settings[param.key] = param.default;
            }
        });
    }
    
    formData.value = newData;
    
    // Appliquer les valeurs par défaut des paramètres du template
    if (templateConfig.value?.parameters) {
        templateConfig.value.parameters.forEach(param => {
            if (formData.value.settings[param.key] === undefined && param.default !== undefined) {
                formData.value.settings[param.key] = param.default;
            }
        });
    }
}, { immediate: true, deep: true });

// État pour le modal de confirmation de suppression
const showDeleteConfirm = ref(false);

/**
 * Validation des paramètres
 * 
 * Pour les settings, la validation est toujours valide car on ne modifie que la configuration,
 * pas le contenu. Les settings peuvent être vides ou partiels.
 */
const isValid = computed(() => {
    // Les settings sont toujours valides (même vides)
    // On ne valide pas le contenu ici car les data sont modifiées dans le template d'édition
    return true;
});

/**
 * Gère la validation et l'émission des paramètres
 * 
 * Émet tous les paramètres modifiables (communs + settings).
 * Les data sont modifiées directement dans le template d'édition de la section.
 */
const handleValidate = () => {
    const sectionId = getSectionValue('id');
    
    // Générer le slug si vide (depuis le titre ou l'ID)
    let slug = formData.value.slug || '';
    if (!slug) {
        slug = generateSlug(formData.value.title, sectionId);
    }
    
    // Nettoyer les valeurs vides des settings
    const cleanedSettings = {};
    Object.keys(formData.value.settings).forEach(key => {
        const value = formData.value.settings[key];
        // Garder les valeurs non vides (null, undefined, '' sont considérés comme vides)
        if (value !== null && value !== undefined && value !== '') {
            cleanedSettings[key] = value;
        }
    });
    
    emit('validated', {
        title: formData.value.title || null,
        slug: slug || null, // Toujours envoyer un slug (généré si nécessaire)
        order: formData.value.order,
        is_visible: formData.value.is_visible,
        can_edit_role: formData.value.can_edit_role,
        state: formData.value.state,
        settings: cleanedSettings,
    });
};

/**
 * Gère la fermeture du modal
 */
const handleClose = () => {
    emit('close');
};

/**
 * Ouvre le modal de confirmation de suppression
 */
const openDeleteConfirm = () => {
    showDeleteConfirm.value = true;
};

/**
 * Ferme le modal de confirmation de suppression
 */
const closeDeleteConfirm = () => {
    showDeleteConfirm.value = false;
};

/**
 * Gère la suppression de la section (après confirmation)
 */
const handleDelete = async () => {
    if (!props.sectionId) return;
    
    closeDeleteConfirm();
    
    try {
        await deleteSection(props.sectionId, {
            onSuccess: () => {
                emit('deleted');
                emit('close');
                // Recharger la page pour mettre à jour l'affichage
                router.reload({ only: ['page'] });
            }
        });
    } catch (errors) {
        console.error('Erreur lors de la suppression de la section:', errors);
        // Afficher une notification d'erreur si nécessaire
    }
};

/**
 * Titre du modal selon le template
 */
/**
 * Titre du modal selon le template
 * Utilise la configuration du template pour récupérer le nom.
 */
const modalTitle = computed(() => {
    try {
        const config = getTemplateConfig(props.sectionTemplate);
        if (config && config.name) {
            return `Paramètres de la section ${config.name.toLowerCase()}`;
        }
    } catch (e) {
        console.warn(`Impossible de charger la config du template "${props.sectionTemplate}"`, e);
    }
    return 'Paramètres de la section';
});
</script>

<template>
    <Modal 
        :open="open" 
        size="lg"
        placement="middle-center"
        close-on-esc
        @close="handleClose"
    >
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h3 class="text-lg font-bold text-primary-100">
                    {{ modalTitle }}
                </h3>
                <Btn
                    v-if="sectionId"
                    color="error"
                    size="sm"
                    variant="ghost"
                    @click="openDeleteConfirm"
                    title="Supprimer la section"
                >
                    <Icon source="fa-trash-can" pack="solid" alt="Supprimer la section" size="sm" />
                </Btn>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Section : Paramètres communs -->
            <div class="space-y-4">
                <h4 class="text-md font-semibold text-base-content/80 border-b border-base-300 pb-2">
                    Paramètres généraux
                </h4>
                
                <!-- Titre -->
                <InputField
                    v-model="formData.title"
                    label="Titre"
                    helper="Titre de la section (optionnel)"
                    placeholder="Titre de la section"
                />
                
                <!-- Slug -->
                <InputField
                    v-model="formData.slug"
                    label="Slug"
                    helper="Identifiant unique pour l'ancre de la section (généré automatiquement si vide)"
                    placeholder="mon-ancre"
                    @input="slugManuallyEdited = true"
                />
                
                <!-- Ordre -->
                <InputField
                    v-model.number="formData.order"
                    type="number"
                    label="Ordre"
                    helper="Position de la section dans la page"
                    :min="0"
                    :step="1"
                />
                
                <!-- Visibilité -->
                <SelectField
                    v-model="formData.is_visible"
                    label="Visibilité"
                    helper="Niveau de visibilité minimum pour voir la section"
                    :options="commonFields.find(f => f.key === 'is_visible')?.options || []"
                />
                
                <!-- Rôle d'édition -->
                <SelectField
                    v-model="formData.can_edit_role"
                    label="Rôle d'édition"
                    helper="Rôle minimum requis pour modifier la section"
                    :options="commonFields.find(f => f.key === 'can_edit_role')?.options || []"
                />
                
                <!-- État -->
                <SelectField
                    v-model="formData.state"
                    label="État"
                    helper="État de publication de la section"
                    :options="commonFields.find(f => f.key === 'state')?.options || []"
                />
            </div>
            
            <!-- Section : Style -->
            <div class="space-y-4">
                <h4 class="text-md font-semibold text-base-content/80 border-b border-base-300 pb-2">
                    Style
                </h4>
                
                <!-- Classes CSS -->
                <InputField
                    v-model="formData.settings.classes"
                    label="Classes CSS"
                    helper="Classes CSS personnalisées à ajouter au conteneur (séparées par des espaces)"
                    placeholder="ex: my-custom-class another-class"
                />
                
                <!-- CSS personnalisé -->
                <TextareaField
                    v-model="formData.settings.customCss"
                    label="CSS personnalisé"
                    helper="CSS personnalisé pour la section (sera injecté dans un tag <style>)"
                    placeholder="ex: .section-container { background: red; }"
                    :rows="4"
                />
            </div>
            
            <!-- Section : Paramètres spécifiques au template -->
            <div v-if="templateParameters.length > 0" class="space-y-4">
                <h4 class="text-md font-semibold text-base-content/80 border-b border-base-300 pb-2">
                    Paramètres du template
                </h4>
                
                <!-- Génération automatique des champs depuis les paramètres -->
                <template v-for="param in templateParameters" :key="param.key">
                    <!-- Select -->
                    <SelectField
                        v-if="param.type === 'select'"
                        v-model="formData.settings[param.key]"
                        :label="param.label"
                        :helper="param.description"
                        :options="param.options"
                    />
                    
                    <!-- Number -->
                    <InputField
                        v-else-if="param.type === 'number'"
                        v-model.number="formData.settings[param.key]"
                        type="number"
                        :label="param.label"
                        :helper="param.description"
                        :min="param.min"
                        :max="param.max"
                        :step="param.step"
                    >
                        <template v-if="param.suffix" #overEnd>
                            <span class="text-sm text-base-content/60 px-2">{{ param.suffix }}</span>
                        </template>
                    </InputField>
                    
                    <!-- Toggle -->
                    <ToggleField
                        v-else-if="param.type === 'toggle'"
                        v-model="formData.settings[param.key]"
                        :label="param.label"
                        :helper="param.description"
                    />
                    
                    <!-- Textarea -->
                    <TextareaField
                        v-else-if="param.type === 'textarea'"
                        v-model="formData.settings[param.key]"
                        :label="param.label"
                        :helper="param.description"
                        :placeholder="param.placeholder"
                        :rows="param.rows"
                        :maxlength="param.maxLength"
                    />
                    
                    <!-- Color -->
                    <ColorField
                        v-else-if="param.type === 'color'"
                        v-model="formData.settings[param.key]"
                        :label="param.label"
                        :helper="param.description"
                    />
                    
                    <!-- Text (par défaut) -->
                    <InputField
                        v-else
                        v-model="formData.settings[param.key]"
                        :label="param.label"
                        :helper="param.description"
                        :placeholder="param.placeholder"
                        :maxlength="param.maxLength"
                    />
                </template>
            </div>
        </div>

        <template #actions>
            <Btn variant="ghost" @click="handleClose">Annuler</Btn>
            <Btn 
                color="primary" 
                @click="handleValidate"
                :disabled="!isValid"
            >
                Valider
            </Btn>
        </template>
    </Modal>

    <!-- Modal de confirmation de suppression -->
    <Modal
        :open="showDeleteConfirm"
        size="sm"
        placement="middle-center"
        variant="outline"
        color="error"
        @close="closeDeleteConfirm"
    >
        <template #header>
            <h3 class="text-lg font-bold">Confirmer la suppression</h3>
        </template>

        <div class="space-y-4">
            <div class="flex items-start gap-3">
                <Icon 
                    source="fa-triangle-exclamation" 
                    pack="solid"
                    alt="Avertissement"
                    class="text-error mt-1"
                    size="lg"
                />
                <div>
                    <p class="font-semibold mb-2">
                        Êtes-vous sûr de vouloir supprimer cette section ?
                    </p>
                    <p v-if="sectionTitle" class="text-sm text-base-content/70">
                        Section : <strong>{{ sectionTitle }}</strong>
                    </p>
                    <p class="text-sm text-base-content/70 mt-2">
                        Cette action est irréversible. La section sera supprimée de la page.
                    </p>
                </div>
            </div>
        </div>

        <template #actions>
            <Btn variant="ghost" @click="closeDeleteConfirm">
                Annuler
            </Btn>
            <Btn 
                color="error" 
                @click="handleDelete"
            >
                <Icon source="fa-trash-can" pack="solid" alt="Supprimer" size="sm" class="mr-2" />
                Supprimer
            </Btn>
        </template>
    </Modal>
</template>

