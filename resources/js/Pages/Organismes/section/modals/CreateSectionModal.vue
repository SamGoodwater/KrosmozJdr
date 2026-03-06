<script setup>
/**
 * CreateSectionModal Component
 *
 * Modal pour créer une nouvelle section sur une page.
 * - Choix du type de section (et titre optionnel).
 * - Création immédiate avec les valeurs par défaut du template.
 * - La configuration détaillée se fait ensuite en édition inline.
 */
import { computed, ref } from 'vue';
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

const submitError = ref('');

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
 * Gère la sélection d'un template de section et crée immédiatement la section
 * avec les valeurs par défaut du registry.
 */
const handleSelectType = async (type) => {
    submitError.value = '';
    form.template = type.value;
    await handleCreateSection(type.value);
};

/**
 * Gère la création de la section.
 *
 * @param {String} template - Type de template (optionnel, utilise form.template si non fourni)
 * @param {Object} overrides - Optionnel : { settings, data } pour remplacer les valeurs par défaut
 */
const handleCreateSection = async (template = null, overrides = {}) => {
    const sectionTemplate = template || form.template;

    submitError.value = '';

    if (!sectionTemplate) {
        submitError.value = 'Aucun template sélectionné.';
        return;
    }

    if (!props.pageId) {
        console.error('Page ID is required to create a section');
        submitError.value = 'Impossible de créer la section: page introuvable.';
        return;
    }

    const defaults = registry.getDefaults(sectionTemplate);
    const sectionPayload = {
        page_id: form.page_id,
        title: form.title || null,
        slug: form.slug || null,
        order: 0,
        template: sectionTemplate,
        type: sectionTemplate, // compat backend legacy
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
                    const firstError = Object.values(errors).flat().find(Boolean);
                    submitError.value = String(firstError || 'La création de la section a échoué.');
                }
            }
        });
    } catch (errors) {
        console.error('CreateSectionModal - Exception lors de la création de la section:', errors);
        submitError.value = 'Erreur inattendue pendant la création de la section.';
    }
};

const handleClose = () => {
    form.page_id = props.pageId || null;
    form.title = '';
    form.slug = '';
    form.order = 0;
    form.template = null;
    form.clearErrors();
    submitError.value = '';
    emit('close');
};
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
            <div v-if="submitError" class="alert alert-error">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>{{ submitError }}</span>
            </div>

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
        </div>

        <template #actions>
            <Btn variant="ghost" @click="handleClose">Annuler</Btn>
        </template>
    </Modal>
</template>

