<script setup>
/**
 * SectionParamsModal Component
 * 
 * @description
 * Modal pour configurer les paramètres d'une section selon son template.
 * - Génère dynamiquement les champs selon SectionType::expectedParams()
 * - Valide les paramètres avant de les retourner
 * - Gère tous les templates de sections (text, image, gallery, video, entity_table)
 * - Sépare settings (configuration) et data (contenu)
 * 
 * @props {Boolean} open - Contrôle l'ouverture du modal
 * @props {String} sectionTemplate - Template de section (text, image, gallery, video, entity_table)
 * @props {Object} initialSettings - Settings initiaux (optionnel)
 * @props {Object} initialData - Data initiaux (optionnel)
 * @emits close - Événement émis quand le modal se ferme
 * @emits validated - Événement émis avec { settings, data } validés
 */
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import TextareaField from '@/Pages/Molecules/data-input/TextareaField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import ToggleField from '@/Pages/Molecules/data-input/ToggleField.vue';
import RichTextEditorField from '@/Pages/Molecules/data-input/RichTextEditorField.vue';
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
    initialData: {
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

// Settings et data du formulaire
const settings = ref({ ...props.initialSettings });
const data = ref({ ...props.initialData });

// État pour le modal de confirmation de suppression
const showDeleteConfirm = ref(false);

// Réinitialiser settings et data quand le template change
watch(() => props.sectionTemplate, (newTemplate) => {
    const defaults = getDefaultSettingsForTemplate(newTemplate);
    settings.value = defaults.settings;
    data.value = defaults.data;
}, { immediate: true });

// Réinitialiser settings et data quand initialSettings/initialData changent
watch(() => [props.initialSettings, props.initialData], ([newSettings, newData]) => {
    if (Object.keys(newSettings).length > 0 || Object.keys(newData).length > 0) {
        settings.value = { ...newSettings };
        data.value = { ...newData };
    } else {
        const defaults = getDefaultSettingsForTemplate(props.sectionTemplate);
        settings.value = defaults.settings;
        data.value = defaults.data;
    }
}, { deep: true });

/**
 * Retourne les settings et data par défaut selon le template
 * Utilise le composable useSectionDefaults pour éviter la duplication
 */
function getDefaultSettingsForTemplate(template) {
    return getDefaults(template);
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

const imageSizeOptions = computed(() => [
    ...sizeOptions.value,
    { value: 'full', label: 'Plein écran' }
]);

const columnsOptions = computed(() => [
    { value: 2, label: '2 colonnes' },
    { value: 3, label: '3 colonnes' },
    { value: 4, label: '4 colonnes' }
]);

const gapOptions = computed(() => [
    { value: 'sm', label: 'Petit' },
    { value: 'md', label: 'Moyen' },
    { value: 'lg', label: 'Grand' }
]);

const videoTypeOptions = computed(() => [
    { value: 'youtube', label: 'YouTube' },
    { value: 'vimeo', label: 'Vimeo' },
    { value: 'direct', label: 'Lien direct' }
]);

/**
 * Validation des paramètres
 * 
 * Utilise une logique générique basée sur les données.
 * Pour des validations spécifiques par template, celles-ci pourraient être
 * définies dans les configs des templates à l'avenir.
 */
const isValid = computed(() => {
    const dataObj = data.value || {};
    
    // Validation générique : vérifier que les données ne sont pas complètement vides
    if (!dataObj || Object.keys(dataObj).length === 0) {
        return false;
    }
    
    // Vérifier si au moins une valeur non-null/non-empty existe
    for (const key in dataObj) {
        const value = dataObj[key];
        if (value !== null && value !== undefined && value !== '') {
            // Si c'est un tableau, vérifier qu'il n'est pas vide
            if (Array.isArray(value)) {
                if (value.length > 0) {
                    return true;
                }
            } else if (typeof value === 'string') {
                // Si c'est une chaîne, vérifier qu'elle n'est pas vide après trim
                if (value.trim().length > 0) {
                    return true;
                }
            } else if (typeof value === 'object') {
                // Si c'est un objet, vérifier qu'il n'est pas vide
                if (Object.keys(value).length > 0) {
                    return true;
                }
            } else {
                // Autres types (number, boolean, etc.)
                return true;
            }
        }
    }
    
    return false;
});

/**
 * Gère la validation et l'émission des paramètres
 */
const handleValidate = () => {
    if (!isValid.value) {
        return;
    }
    emit('validated', { 
        settings: { ...settings.value },
        data: { ...data.value }
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
            <!-- Classes CSS personnalisées (settings) -->
            <InputField
                v-model="settings.classes"
                label="Classes CSS (optionnel)"
                placeholder="ex: my-custom-class another-class"
                helper="Classes CSS à ajouter au conteneur de la section"
            />

            <!-- Template TEXT -->
            <template v-if="sectionTemplate === 'text'">
                <RichTextEditorField
                    v-model="data.content"
                    label="Contenu"
                    required
                    helper="Contenu de la section (formatage riche disponible)"
                />
                <SelectField
                    v-model="settings.align"
                    label="Alignement"
                    :options="alignOptions"
                />
                <SelectField
                    v-model="settings.size"
                    label="Taille du texte"
                    :options="sizeOptions"
                />
            </template>

            <!-- Template IMAGE -->
            <template v-if="sectionTemplate === 'image'">
                <InputField
                    v-model="data.src"
                    label="URL de l'image"
                    type="url"
                    required
                    placeholder="https://example.com/image.jpg"
                />
                <InputField
                    v-model="data.alt"
                    label="Texte alternatif"
                    required
                    placeholder="Description de l'image"
                />
                <TextareaField
                    v-model="data.caption"
                    label="Légende (optionnel)"
                    placeholder="Légende de l'image"
                />
                <SelectField
                    v-model="settings.align"
                    label="Alignement"
                    :options="alignOptions"
                />
                <SelectField
                    v-model="settings.size"
                    label="Taille"
                    :options="imageSizeOptions"
                />
            </template>

            <!-- Template GALLERY -->
            <template v-if="sectionTemplate === 'gallery'">
                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle"></i>
                    <span class="text-sm">La gestion des images de la galerie sera implémentée prochainement.</span>
                </div>
                <SelectField
                    v-model="settings.columns"
                    label="Nombre de colonnes"
                    :options="columnsOptions"
                />
                <SelectField
                    v-model="settings.gap"
                    label="Espacement"
                    :options="gapOptions"
                />
            </template>

            <!-- Template VIDEO -->
            <template v-if="sectionTemplate === 'video'">
                <SelectField
                    v-model="data.type"
                    label="Type de vidéo"
                    :options="videoTypeOptions"
                    required
                />
                <InputField
                    v-model="data.src"
                    label="URL de la vidéo"
                    type="url"
                    required
                    placeholder="https://www.youtube.com/watch?v=..."
                />
                <ToggleField
                    v-model="settings.autoplay"
                    label="Lecture automatique"
                />
                <ToggleField
                    v-model="settings.controls"
                    label="Afficher les contrôles"
                />
            </template>

            <!-- Template ENTITY_TABLE -->
            <template v-if="sectionTemplate === 'entity_table'">
                <InputField
                    v-model="data.entity"
                    label="Type d'entité"
                    required
                    placeholder="item, spell, npc, etc."
                    helper="Type d'entité à afficher dans le tableau"
                />
                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle"></i>
                    <span class="text-sm">Les filtres et colonnes personnalisées seront configurables prochainement.</span>
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

