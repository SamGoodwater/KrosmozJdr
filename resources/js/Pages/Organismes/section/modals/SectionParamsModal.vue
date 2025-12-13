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
 * **Flux de données :**
 * - La section est déjà chargée depuis le backend (via PageController)
 * - Elle est normalisée en modèle Section via useSectionUI
 * - Le modal reçoit sectionModel (instance Section) avec tous les getters disponibles
 * - Pas de requête supplémentaire au backend à l'ouverture
 * 
 * @props {Boolean} open - Contrôle l'ouverture du modal
 * @props {String} sectionTemplate - Template de section
 * @props {Object} section - Instance Section normalisée (depuis useSectionUI)
 * @emits close - Événement émis quand le modal se ferme
 * @emits validated - Événement émis avec { title, slug, order, is_visible, can_edit_role, state, settings } validés
 */
import { ref, computed, watch, nextTick } from 'vue';
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
import { useTemplateRegistry } from '../composables/useTemplateRegistry';
import { TransformService, SectionParameterService, SectionMapper } from '@/Utils/Services';
import { Section } from '@/Models';

const props = defineProps({
    open: {
        type: Boolean,
        default: false
    },
    sectionTemplate: {
        type: String,
        required: true,
    },
    section: {
        type: Object,
        required: true,
        validator: (value) => {
            // Vérifier que c'est bien une instance Section (avec _data)
            return value && (value._data || value.id);
        }
    }
});

const emit = defineEmits(['close', 'validated', 'deleted']);

// Composables
const { deleteSection } = useSectionAPI();

// Registry de templates
const registry = useTemplateRegistry();

// Configuration du template (depuis le registry)
const templateConfig = computed(() => {
    return registry.getConfig(props.sectionTemplate);
});

// Paramètres du template
const templateParameters = computed(() => {
    if (!templateConfig.value || !templateConfig.value.parameters) {
        return [];
    }
    return SectionParameterService.getParameterFields(templateConfig.value.parameters);
});

// Options pour les selects
const visibilityOptions = computed(() => SectionParameterService.getVisibilityOptions());
const stateOptions = computed(() => SectionParameterService.getStateOptions());

/**
 * Normalise la section en utilisant SectionMapper pour garantir un accès correct
 */
const sectionModel = computed(() => {
    if (!props.section) return null;
    
    // Si c'est déjà une instance Section, la retourner
    if (props.section instanceof Section) {
        return props.section;
    }
    
    // Sinon, utiliser le mapper pour normaliser
    return SectionMapper.mapToModel(props.section);
});

/**
 * Extrait une valeur depuis la section normalisée (utilise les getters du modèle Section)
 */
const getSectionValue = (key) => {
    const model = sectionModel.value;
    if (!model) return null;
    
    // Mapping des clés vers les getters du modèle Section
    const getterMap = {
        'id': () => model.id,
        'title': () => model.title,
        'slug': () => model.slug,
        'order': () => model.order,
        'is_visible': () => model.isVisible,
        'can_edit_role': () => model.canEditRole || 'admin',
        'state': () => model.state,
        'settings': () => model.settings,
        'data': () => model.data,
        'template': () => model.template,
    };
    
    // Si un getter existe pour cette clé, l'utiliser
    if (getterMap[key]) {
        try {
            return getterMap[key]();
        } catch (e) {
            console.warn(`Erreur lors de l'accès au getter pour "${key}":`, e);
            // Fallback sur _data
            return model._data?.[key] ?? null;
        }
    }
    
    // Sinon, accéder directement à _data
    return model._data?.[key] ?? null;
};

/**
 * Génère un slug depuis le titre ou l'ID
 * Utilise TransformService pour la génération
 */
const generateSlug = (title, sectionId) => {
    return TransformService.generateSlug(title, sectionId, {
        prefix: 'section'
    });
};

/**
 * Initialise les données du formulaire depuis la section
 */
const initializeFormData = () => {
    const sectionId = getSectionValue('id');
    const title = getSectionValue('title') || '';
    const existingSlug = getSectionValue('slug') || '';
    const sectionSettings = getSectionValue('settings') || {};
    
    // Générer le slug si vide (depuis le titre ou l'ID)
    const slug = existingSlug || generateSlug(title, sectionId);
    
    // Debug en développement
    if (import.meta.env.DEV) {
        console.log('SectionParamsModal - initializeFormData:', {
            sectionId,
            title,
            existingSlug,
            slug,
            section: props.section,
            sectionSettings,
            isVisible: getSectionValue('is_visible'),
            canEditRole: getSectionValue('can_edit_role'),
            state: getSectionValue('state'),
            order: getSectionValue('order'),
        });
    }
    
    const formData = {
        // Paramètres communs
        title: title,
        slug: slug,
        order: getSectionValue('order') || 0,
        is_visible: getSectionValue('is_visible') || 'guest',
        can_edit_role: getSectionValue('can_edit_role') || 'admin',
        state: getSectionValue('state') || 'draft',
        // Settings (inclut classes et customCss)
        settings: {
            classes: sectionSettings.classes || '',
            customCss: sectionSettings.customCss || '',
            // Inclure tous les autres settings de la section
            ...sectionSettings,
        },
    };
    
    // Appliquer les valeurs par défaut des paramètres du template
    if (templateConfig.value?.parameters) {
        templateConfig.value.parameters.forEach(param => {
            if (formData.settings[param.key] === undefined && param.default !== undefined) {
                formData.settings[param.key] = param.default;
            }
        });
    }
    
    return formData;
};

// Données du formulaire - initialisées vides au départ
const formData = ref({
    title: '',
    slug: '',
    order: 0,
    is_visible: 'guest',
    can_edit_role: 'admin',
    state: 'draft',
    settings: {
        classes: '',
        customCss: ''
    }
});

// Compteur de version pour forcer la réactivité des champs
const formDataVersion = ref(0);

// Computed réactifs pour les champs title et slug (comme pour les selects dans EditPageModal)
const formTitle = computed({
    get: () => {
        formDataVersion.value; // Toucher pour forcer la réactivité
        return formData.value.title || '';
    },
    set: (value) => {
        formData.value.title = value;
        formDataVersion.value++;
    }
});

const formSlug = computed({
    get: () => {
        formDataVersion.value; // Toucher pour forcer la réactivité
        return formData.value.slug || '';
    },
    set: (value) => {
        formData.value.slug = value;
        formDataVersion.value++;
        slugManuallyEdited.value = true;
    }
});

// Flag pour savoir si le slug a été modifié manuellement
const slugManuallyEdited = ref(false);

// Réinitialiser les données quand le modal s'ouvre ou quand la section change
watch(() => [props.open, props.section], ([isOpen]) => {
    if (isOpen && sectionModel.value && sectionModel.value.id) {
        // Utiliser double nextTick pour s'assurer que le DOM est rendu
        nextTick(() => {
            nextTick(() => {
                const newData = initializeFormData();
                
                if (import.meta.env.DEV) {
                    console.log('SectionParamsModal - FormData initialized:', {
                        newData,
                        title: newData.title,
                        slug: newData.slug,
                        sectionModel: sectionModel.value,
                        sectionId: sectionModel.value?.id,
                        formDataBefore: formData.value
                    });
                }
                
                // Mettre à jour formData de manière réactive
                // Réassigner complètement l'objet pour forcer la réactivité
                formData.value = { ...newData };
                slugManuallyEdited.value = false;
                
                // Attendre un peu pour que Vue traite la mise à jour
                nextTick(() => {
                    // Incrémenter le compteur de version pour forcer la réactivité des computed
                    // Cela déclenchera la réévaluation de formTitle et formSlug
                    formDataVersion.value++;
                    
                    // Forcer la mise à jour des computed en les touchant
                    // Cela garantit que les InputField reçoivent les nouvelles valeurs
                    if (import.meta.env.DEV) {
                        console.log('SectionParamsModal - Computed values after update:', {
                            formTitleValue: formTitle.value,
                            formSlugValue: formSlug.value,
                            formDataTitle: formData.value.title,
                            formDataSlug: formData.value.slug,
                            formDataVersion: formDataVersion.value
                        });
                    }
                });
                
                if (import.meta.env.DEV) {
                    console.log('SectionParamsModal - FormData after update:', {
                        formDataAfter: formData.value,
                        title: formData.value.title,
                        slug: formData.value.slug
                    });
                }
            });
        });
    }
}, { immediate: true, deep: true });

// Watcher pour générer automatiquement le slug depuis le titre
watch(() => formTitle.value, (newTitle) => {
    if (!slugManuallyEdited.value) {
        const sectionId = getSectionValue('id');
        const currentSlug = formSlug.value || '';
        const generatedSlug = generateSlug(newTitle, sectionId);
        
        // Si le slug actuel est vide ou correspond au slug généré précédemment, le mettre à jour
        if (!currentSlug || currentSlug === `section-${sectionId}` || currentSlug.startsWith('section-')) {
            formSlug.value = generatedSlug;
            slugManuallyEdited.value = false; // Réinitialiser car c'est auto-généré
        }
    }
});

// État pour le modal de confirmation de suppression
const showDeleteConfirm = ref(false);

/**
 * Validation des paramètres
 */
const isValid = computed(() => {
    return true; // Les settings sont toujours valides
});

/**
 * Gère la validation et l'émission des paramètres
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
        slug: slug || null,
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
    const sectionId = getSectionValue('id');
    if (!sectionId) return;
    
    closeDeleteConfirm();
    
    try {
        await deleteSection(sectionId, {
            onSuccess: () => {
                emit('deleted');
                emit('close');
                router.reload({ only: ['page'] });
            }
        });
    } catch (errors) {
        console.error('Erreur lors de la suppression de la section:', errors);
    }
};

/**
 * Titre du modal selon le template
 */
const modalTitle = computed(() => {
    const config = templateConfig.value;
    if (config && config.name) {
        return `Paramètres de la section ${config.name.toLowerCase()}`;
    }
    
    return 'Paramètres de la section';
});

// ID et titre de la section pour l'affichage
const sectionId = computed(() => getSectionValue('id'));
const sectionTitle = computed(() => getSectionValue('title') || '');
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
                    v-model="formTitle"
                    label="Titre"
                    helper="Titre de la section (optionnel)"
                    placeholder="Titre de la section"
                />
                
                <!-- Slug -->
                <InputField
                    v-model="formSlug"
                    label="Slug"
                    helper="Identifiant unique pour l'ancre de la section (généré automatiquement si vide)"
                    placeholder="mon-ancre"
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
                    :options="visibilityOptions"
                />
                
                <!-- Rôle d'édition -->
                <SelectField
                    v-model="formData.can_edit_role"
                    label="Rôle d'édition"
                    helper="Rôle minimum requis pour modifier la section"
                    :options="visibilityOptions"
                />
                
                <!-- État -->
                <SelectField
                    v-model="formData.state"
                    label="État"
                    helper="État de publication de la section"
                    :options="stateOptions"
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
