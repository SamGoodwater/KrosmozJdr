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

// Settings et data du formulaire
const settings = ref({ ...props.initialSettings });
const data = ref({ ...props.initialData });

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
 */
function getDefaultSettingsForTemplate(template) {
    switch (template) {
        case 'text':
            return { settings: { align: 'left', size: 'md' }, data: { content: '' } };
        case 'image':
            return { settings: { align: 'center', size: 'md' }, data: { src: '', alt: '', caption: '' } };
        case 'gallery':
            return { settings: { columns: 3, gap: 'md' }, data: { images: [] } };
        case 'video':
            return { settings: { autoplay: false, controls: true }, data: { src: '', type: 'youtube' } };
        case 'entity_table':
            return { settings: {}, data: { entity: '', filters: {}, columns: [] } };
        default:
            return { settings: {}, data: {} };
    }
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
 */
const isValid = computed(() => {
    switch (props.sectionTemplate) {
        case 'text':
            return !!data.value.content;
        case 'image':
            return !!data.value.src && !!data.value.alt;
        case 'gallery':
            return Array.isArray(data.value.images) && data.value.images.length > 0;
        case 'video':
            return !!data.value.src && !!data.value.type;
        case 'entity_table':
            return !!data.value.entity;
        default:
            return false;
    }
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
 * Gère la suppression de la section
 */
const handleDelete = () => {
    if (!props.sectionId) return;
    
    const sectionTitle = props.sectionTitle || 'cette section';
    if (!confirm(`Êtes-vous sûr de vouloir supprimer la section "${sectionTitle}" ?`)) {
        return;
    }
    
    router.delete(route('sections.delete', props.sectionId), {
        preserveScroll: true,
        onSuccess: () => {
            emit('deleted');
            emit('close');
            router.reload({ only: ['page'] });
        },
        onError: (errors) => {
            console.error('Erreur lors de la suppression de la section:', errors);
        }
    });
};

/**
 * Titre du modal selon le template
 */
const modalTitle = computed(() => {
    const titles = {
        text: 'Paramètres de la section texte',
        image: 'Paramètres de l\'image',
        gallery: 'Paramètres de la galerie',
        video: 'Paramètres de la vidéo',
        entity_table: 'Paramètres du tableau d\'entités'
    };
    return titles[props.sectionTemplate] || 'Paramètres de la section';
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
                    @click="handleDelete"
                    title="Supprimer la section"
                >
                    <Icon source="fa-solid fa-trash-can" size="sm" />
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
</template>

