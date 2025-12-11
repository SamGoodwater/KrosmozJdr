<script setup>
/**
 * SectionParamsModal Component
 * 
 * @description
 * Modal pour configurer les paramètres (settings) d'une section.
 * - Ne modifie QUE les settings, pas les data (contenu)
 * - Gère uniquement le template text pour l'instant
 * - Les data sont modifiées directement dans le template d'édition
 * 
 * @props {Boolean} open - Contrôle l'ouverture du modal
 * @props {String} sectionTemplate - Template de section (text uniquement pour l'instant)
 * @props {Object} initialSettings - Settings initiaux
 * @emits close - Événement émis quand le modal se ferme
 * @emits validated - Événement émis avec { settings } validés
 */
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { useSectionAPI } from '../composables/useSectionAPI';
import { useSectionDefaults } from '../composables/useSectionDefaults';
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
const { getDefaults } = useSectionDefaults();

// Settings du formulaire (uniquement les settings, pas les data)
const settings = ref({ ...props.initialSettings });

// État pour le modal de confirmation de suppression
const showDeleteConfirm = ref(false);

// Réinitialiser settings quand le template change
watch(() => props.sectionTemplate, (newTemplate) => {
    const defaults = getDefaultSettingsForTemplate(newTemplate);
    settings.value = { ...defaults.settings, ...props.initialSettings };
}, { immediate: true });

// Réinitialiser settings quand initialSettings change
watch(() => props.initialSettings, (newSettings) => {
    if (newSettings && Object.keys(newSettings).length > 0) {
        settings.value = { ...newSettings };
    } else {
        const defaults = getDefaultSettingsForTemplate(props.sectionTemplate);
        settings.value = { ...defaults.settings };
    }
}, { deep: true });

/**
 * Retourne les settings par défaut selon le template
 */
function getDefaultSettingsForTemplate(template) {
    const defaults = getDefaults(template);
    return {
        settings: defaults.settings || {}
    };
}

/**
 * Options pour les selects
 */
const alignOptions = computed(() => [
    { value: 'left', label: 'Gauche' },
    { value: 'center', label: 'Centre' },
    { value: 'right', label: 'Droite' }
]);

const sizeOptions = computed(() => [
    { value: 'sm', label: 'Petit' },
    { value: 'md', label: 'Moyen' },
    { value: 'lg', label: 'Grand' },
    { value: 'xl', label: 'Très grand' }
]);

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
 * Émet uniquement les settings, pas les data.
 * Les data sont modifiées directement dans le template d'édition de la section.
 */
const handleValidate = () => {
    emit('validated', { 
        settings: { ...settings.value }
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

        <div class="space-y-4">
            <!-- Template TEXT uniquement -->
            <template v-if="sectionTemplate === 'text'">
                <!-- Classes CSS personnalisées -->
                <InputField
                    v-model="settings.classes"
                    label="Classes CSS (optionnel)"
                    placeholder="ex: my-custom-class another-class"
                    helper="Classes CSS à ajouter au conteneur de la section"
                />
                
                <!-- Alignement du texte -->
                <SelectField
                    v-model="settings.align"
                    label="Alignement"
                    :options="alignOptions"
                    helper="Alignement du texte dans la section"
                />
                
                <!-- Taille du texte -->
                <SelectField
                    v-model="settings.size"
                    label="Taille du texte"
                    :options="sizeOptions"
                    helper="Taille d'affichage du texte"
                />
            </template>

            <!-- Autres templates : non supportés pour l'instant -->
            <template v-else>
                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle"></i>
                    <span class="text-sm">
                        La configuration des paramètres pour le template "{{ sectionTemplate }}" n'est pas encore disponible.
                        Seul le template "text" est supporté pour l'instant.
                    </span>
                </div>
            </template>
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

