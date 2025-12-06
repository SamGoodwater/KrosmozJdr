<script setup>
/**
 * CreateSectionModal Component
 * 
 * @description
 * Modal pour créer une nouvelle section sur une page.
 * - Affiche les différents templates disponibles
 * - Permet de choisir un titre optionnel
 * - Ouvre automatiquement le modal de paramètres si nécessaire
 * 
 * @props {Boolean} open - Contrôle l'ouverture du modal
 * @props {Number} pageId - ID de la page sur laquelle créer la section
 * @emits close - Événement émis quand le modal se ferme
 * @emits created - Événement émis quand la section est créée
 */
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { getTemplateOptions, getTemplateByValue } from '../templates';
import SectionParamsModal from './SectionParamsModal.vue';

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

// Options des types de sections (auto-discovery)
const sectionTypes = computed(() => getTemplateOptions());

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

// Settings et data gérés séparément
const sectionSettings = ref({});
const sectionData = ref({});

// Modal de paramètres
const paramsModalOpen = ref(false);
const selectedSectionType = ref(null);

/**
 * Vérifie si un type de section nécessite des paramètres
 */
const needsParams = (type) => {
    // Tous les types nécessitent des paramètres selon SectionType::expectedParams()
    return true;
};

/**
 * Obtient les paramètres requis pour un type
 */
const getRequiredParams = (type) => {
    // Cette logique sera dans SectionParamsModal
    return [];
};

/**
 * Gère la sélection d'un template de section
 */
const handleSelectType = (type) => {
    form.template = type.value;
    selectedSectionType.value = type;
    
    // Si le template nécessite des paramètres, ouvrir le modal de paramètres
    if (needsParams(type.value)) {
        paramsModalOpen.value = true;
    } else {
        // Sinon, créer directement la section
        handleCreateSection();
    }
};

/**
 * Gère la création de la section
 */
const handleCreateSection = () => {
    if (!form.template) {
        return;
    }
    
    // Vérifier que pageId est défini
    if (!props.pageId) {
        console.error('Page ID is required to create a section');
        return;
    }

    // L'ordre sera calculé automatiquement côté backend (dernière section + 1)
    // On peut laisser 0 ou ne pas l'envoyer, le backend s'en chargera
    if (!form.order) {
        form.order = 0;
    }

    // Le backend retourne JSON pour les requêtes AJAX
    // Ajouter settings et data aux données du formulaire via transform
    form.transform((formData) => ({
        ...formData,
        settings: sectionSettings.value,
        data: sectionData.value
    })).post(route('sections.store'), {
        preserveScroll: true,
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        onSuccess: () => {
            // Pour les requêtes JSON, Inertia ne recharge pas la page
            // On doit recharger manuellement pour obtenir la nouvelle section
            router.reload({ 
                only: ['page'],
                onSuccess: () => {
                    // Après le rechargement, la section devrait être dans les sections de la page
                    // On émet l'événement created pour que le parent gère l'affichage
                    const template = form.template;
                    const templateConfig = getTemplateByValue(template);
                    
                    emit('created', { 
                        openEdit: templateConfig?.supportsAutoSave || false 
                    });
                    
                    handleClose();
                }
            });
        },
        onError: (errors) => {
            console.error('Erreur lors de la création de la section:', errors);
        }
    });
};

/**
 * Gère la fermeture du modal
 */
const handleClose = () => {
    // Réinitialiser manuellement le formulaire
    form.page_id = props.pageId || null;
    form.title = '';
    form.slug = '';
    form.order = 0;
    form.template = null;
    form.clearErrors();
    // Réinitialiser settings et data
    sectionSettings.value = {};
    sectionData.value = {};
    selectedSectionType.value = null;
    paramsModalOpen.value = false;
    emit('close');
};

/**
 * Gère la fermeture du modal de paramètres
 */
const handleCloseParamsModal = () => {
    paramsModalOpen.value = false;
    // Réinitialiser le template si l'utilisateur annule
    if ((!sectionSettings.value || Object.keys(sectionSettings.value).length === 0) && 
        (!sectionData.value || Object.keys(sectionData.value).length === 0)) {
        form.template = null;
        selectedSectionType.value = null;
    }
};

/**
 * Gère la validation des paramètres (settings et data)
 */
const handleParamsValidated = ({ settings, data }) => {
    sectionSettings.value = settings || {};
    sectionData.value = data || {};
    paramsModalOpen.value = false;
    // Créer la section avec les paramètres
    handleCreateSection();
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
            <!-- Titre optionnel -->
            <InputField
                v-model="form.title"
                label="Titre de la section (optionnel)"
                placeholder="Ex: Introduction, Description, etc."
                :error="form.errors.title"
            />

            <!-- Sélection du type de section -->
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

    <!-- Modal de paramètres -->
    <SectionParamsModal
        v-if="selectedSectionType"
        :open="paramsModalOpen"
        :section-template="selectedSectionType.value"
        :initial-settings="sectionSettings"
        :initial-data="sectionData"
        @close="handleCloseParamsModal"
        @validated="handleParamsValidated"
    />
</template>

