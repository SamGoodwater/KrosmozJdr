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
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { getSectionTypeOptions } from '@/Utils/enums/SectionType';
import SectionParamsModal from './SectionParamsModal.vue';

const props = defineProps({
    open: {
        type: Boolean,
        default: false
    },
    pageId: {
        type: Number,
        required: true
    }
});

const emit = defineEmits(['close', 'created']);

// Options des types de sections
const sectionTypes = computed(() => getSectionTypeOptions());

// Formulaire
const form = useForm({
    page_id: props.pageId,
    order: 0, // Sera calculé automatiquement côté backend
    type: null,
    title: '', // Optionnel, pour référence
    params: {}
});

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
 * Gère la sélection d'un type de section
 */
const handleSelectType = (type) => {
    form.type = type.value;
    selectedSectionType.value = type;
    
    // Si le type nécessite des paramètres, ouvrir le modal de paramètres
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
    if (!form.type) {
        return;
    }

    // L'ordre sera calculé automatiquement côté backend (dernière section + 1)
    // On peut laisser 0 ou ne pas l'envoyer, le backend s'en chargera
    if (!form.order) {
        form.order = 0;
    }

    form.post(route('sections.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit('created');
            handleClose();
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
    form.reset();
    form.clearErrors();
    selectedSectionType.value = null;
    paramsModalOpen.value = false;
    emit('close');
};

/**
 * Gère la fermeture du modal de paramètres
 */
const handleCloseParamsModal = () => {
    paramsModalOpen.value = false;
    // Réinitialiser le type si l'utilisateur annule
    if (!form.params || Object.keys(form.params).length === 0) {
        form.type = null;
        selectedSectionType.value = null;
    }
};

/**
 * Gère la validation des paramètres
 */
const handleParamsValidated = (params) => {
    form.params = params;
    paramsModalOpen.value = false;
    // Créer la section avec les paramètres
    handleCreateSection();
};
</script>

<template>
    <Modal 
        :open="open" 
        size="lg"
        placement="middle"
        animation="fade"
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
                            'p-4 rounded-lg border-2 transition-all',
                            form.type === type.value
                                ? 'border-primary bg-primary/10'
                                : 'border-base-300 hover:border-primary/50 hover:bg-base-200'
                        ]"
                        type="button"
                    >
                        <div class="flex flex-col items-center gap-2">
                            <Icon 
                                :source="`fa-solid ${type.icon}`" 
                                size="xl"
                                :class="form.type === type.value ? 'text-primary' : 'text-base-content'"
                            />
                            <span class="font-medium">{{ type.label }}</span>
                        </div>
                    </button>
                </div>
                <div v-if="form.errors.type" class="label">
                    <span class="label-text-alt text-error">{{ form.errors.type }}</span>
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
        :section-type="selectedSectionType.value"
        :initial-params="form.params"
        @close="handleCloseParamsModal"
        @validated="handleParamsValidated"
    />
</template>

