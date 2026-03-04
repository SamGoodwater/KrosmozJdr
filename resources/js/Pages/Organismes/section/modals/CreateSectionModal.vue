<script setup>
/**
 * CreateSectionModal Component
 *
 * Modal pour créer une nouvelle section sur une page.
 * - Étape 1 : choix du type de section (et titre optionnel).
 * - Étape 2 (si le template fournit paramsComponent) : vue dédiée des paramètres, puis création.
 * - Sinon : création directe avec valeurs par défaut.
 */
import { computed, ref, defineAsyncComponent } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { useTemplateRegistry } from '../composables/useTemplateRegistry';
import { useSectionAPI } from '../composables/useSectionAPI';
import { logDev } from '@/Utils/dev-logger';

const props = defineProps({
    open: {
        type: Boolean,
        default: false
    },
    pageId: {
        type: [Number, String],
        default: null
    }
});

const emit = defineEmits(['close', 'created']);

// Registry de templates
const registry = useTemplateRegistry();

// Options des types de sections (depuis le registry)
const sectionTypes = computed(() => registry.getOptions());

// Étape de création : 'type' (choix) ou 'params' (vue paramètres du template)
const createStep = ref('type');
const pendingSettings = ref({});
const pendingData = ref({});

const selectedTemplateConfig = computed(() =>
  form.template ? registry.getConfig(form.template) : null
);
const templateParamsComponent = computed(() => {
  const fn = selectedTemplateConfig.value?.paramsComponent;
  if (typeof fn !== 'function') return null;
  return defineAsyncComponent(fn);
});

/**
 * Parse une icône au format "fa-solid fa-xxx" ou "fa-xxx" et retourne { source, pack }
 */
const parseIcon = (iconString) => {
    if (!iconString) return { source: '', pack: 'solid' };
    
    // Si c'est déjà au format "fa-xxx", on l'utilise tel quel
    if (iconString.startsWith('fa-') && !iconString.startsWith('fa-solid') && !iconString.startsWith('fa-regular') && !iconString.startsWith('fa-brands') && !iconString.startsWith('fa-duotone')) {
        return { source: iconString, pack: 'solid' };
    }
    
    // Parser "fa-solid fa-xxx", "fa-regular fa-xxx", etc.
    const parts = iconString.split(' ');
    if (parts.length >= 2) {
        const pack = parts[0].replace('fa-', '');
        const source = parts[1];
        return { source, pack };
    }
    
    // Fallback
    return { source: iconString.replace(/^fa-(solid|regular|brands|duotone)\s+/, ''), pack: 'solid' };
};

// Formulaire (sans settings et data pour éviter les conflits avec Inertia)
const form = useForm({
    page_id: props.pageId || null,
    title: '', // Optionnel, pour référence
    slug: '', // Optionnel, généré automatiquement si vide
    order: 0, // Sera calculé automatiquement côté backend
    template: null,
});

// Composables
const { createSection } = useSectionAPI();

/**
 * Gère la sélection d'un template de section.
 * Si le template a une vue paramètres dédiée, on passe à l'étape "params" ; sinon on crée directement.
 */
const handleSelectType = async (type) => {
    form.template = type.value;
    const config = registry.getConfig(type.value);
    const defaults = registry.getDefaults(type.value);

    if (config?.paramsComponent) {
        createStep.value = 'params';
        pendingSettings.value = { ...defaults.settings };
        pendingData.value = { ...defaults.data };
        return;
    }
    await handleCreateSection(type.value);
};

/**
 * Création à partir de l'étape paramètres (valeurs saisies dans la vue dédiée).
 */
const handleCreateFromParams = async () => {
    await handleCreateSection(form.template, {
        settings: { ...pendingSettings.value },
        data: { ...pendingData.value },
    });
};

const handleBackToType = () => {
    createStep.value = 'type';
    form.template = null;
    pendingSettings.value = {};
    pendingData.value = {};
};

/**
 * Gère la création de la section.
 *
 * @param {String} template - Type de template (optionnel, utilise form.template si non fourni)
 * @param {Object} overrides - Optionnel : { settings, data } pour remplacer les valeurs par défaut
 */
const handleCreateSection = async (template = null, overrides = {}) => {
    const sectionTemplate = template || form.template;

    if (!sectionTemplate) {
        return;
    }

    if (!props.pageId) {
        console.error('Page ID is required to create a section');
        return;
    }

    const defaults = registry.getDefaults(sectionTemplate);
    const sectionPayload = {
        page_id: form.page_id,
        title: form.title || null,
        slug: form.slug || null,
        order: 0,
        template: sectionTemplate,
        settings: overrides.settings ?? defaults.settings,
        data: overrides.data ?? defaults.data,
    };

    logDev('CreateSectionModal - Creating section with payload:', sectionPayload);
    
    try {
        await createSection(sectionPayload, {
            onSuccess: (page) => {
                logDev('CreateSectionModal - Section created successfully, page response:', page);
                
                // Après la redirection, les props sont mises à jour via usePage()
                // Mais onSuccess est appelé avant que les props soient mises à jour
                // On utilise un petit délai pour attendre que les props soient disponibles
                // OU on émet simplement le template et le parent attendra que les sections soient disponibles
                logDev('CreateSectionModal - Emitting created event with template:', sectionTemplate);
                emit('created', { 
                    template: sectionTemplate,
                    openEdit: true // Toujours ouvrir en mode édition
                });
                
                logDev('CreateSectionModal - Closing modal');
                handleClose();
            },
            onError: (errors) => {
                console.error('CreateSectionModal - Erreur lors de la création de la section:', errors);
                // Afficher les erreurs dans le formulaire
                if (errors) {
                    Object.keys(errors).forEach(key => {
                        form.setError(key, errors[key]);
                    });
                }
            }
        });
    } catch (errors) {
        console.error('CreateSectionModal - Exception lors de la création de la section:', errors);
    }
};

const handleClose = () => {
    form.page_id = props.pageId || null;
    form.title = '';
    form.slug = '';
    form.order = 0;
    form.template = null;
    form.clearErrors();
    createStep.value = 'type';
    pendingSettings.value = {};
    pendingData.value = {};
    emit('close');
};

function onParamsUpdateSettings(v) {
    pendingSettings.value = { ...pendingSettings.value, ...v };
}
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
            <h3 class="text-lg font-bold text-primary-100">
                Ajouter une section
            </h3>
        </template>

        <div class="space-y-6">
            <!-- Étape 1 : type + titre -->
            <template v-if="createStep === 'type'">
                <InputField
                    v-model="form.title"
                    label="Titre de la section (optionnel)"
                    placeholder="Ex: Introduction, Description, etc."
                    :error="form.errors.title"
                />
                <div>
                    <label class="label">
                        <span class="label-text font-semibold">Type de section</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                        <button
                            v-for="type in sectionTypes"
                            :key="type.value"
                            @click="handleSelectType(type)"
                            :class="[
                                'p-4 rounded-lg border-2 transition-all text-left',
                                form.template === type.value
                                    ? 'border-primary bg-primary/10'
                                    : 'border-base-300 hover:border-primary/50 hover:bg-base-200'
                            ]"
                            type="button"
                        >
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-3">
                                    <Icon
                                        :source="parseIcon(type.icon).source"
                                        :pack="parseIcon(type.icon).pack"
                                        :alt="type.label"
                                        size="lg"
                                        :class="form.template === type.value ? 'text-primary' : 'text-base-content'"
                                    />
                                    <span class="font-medium">{{ type.label }}</span>
                                </div>
                                <p v-if="type.description" class="text-sm text-base-content/70">
                                    {{ type.description }}
                                </p>
                            </div>
                        </button>
                    </div>
                    <div v-if="form.errors.template" class="label">
                        <span class="label-text-alt text-error">{{ form.errors.template }}</span>
                    </div>
                </div>
            </template>

            <!-- Étape 2 : vue paramètres dédiée du template -->
            <template v-else-if="createStep === 'params' && templateParamsComponent">
                <p class="text-sm text-base-content/70">
                    Configurez les paramètres de la section « {{ selectedTemplateConfig?.name ?? form.template }} ».
                </p>
                <component
                    :is="templateParamsComponent"
                    :section="null"
                    :settings="pendingSettings"
                    mode="create"
                    @update:settings="onParamsUpdateSettings"
                />
            </template>
        </div>

        <template #actions>
            <template v-if="createStep === 'type'">
                <Btn variant="ghost" @click="handleClose">Annuler</Btn>
            </template>
            <template v-else-if="createStep === 'params'">
                <Btn variant="ghost" @click="handleBackToType">
                    <Icon source="fa-arrow-left" pack="solid" class="mr-2" />
                    Retour
                </Btn>
                <Btn color="primary" @click="handleCreateFromParams">
                    Créer la section
                </Btn>
            </template>
        </template>
    </Modal>
</template>

