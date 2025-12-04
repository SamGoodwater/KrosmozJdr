<script setup>
/**
 * SectionParamsModal Component
 * 
 * @description
 * Modal pour configurer les paramètres d'une section selon son type.
 * - Génère dynamiquement les champs selon SectionType::expectedParams()
 * - Valide les paramètres avant de les retourner
 * - Gère tous les types de sections (text, image, gallery, video, entity_table)
 * 
 * @props {Boolean} open - Contrôle l'ouverture du modal
 * @props {String} sectionType - Type de section (text, image, gallery, video, entity_table)
 * @props {Object} initialParams - Paramètres initiaux (optionnel)
 * @emits close - Événement émis quand le modal se ferme
 * @emits validated - Événement émis avec les paramètres validés
 */
import { ref, computed, watch } from 'vue';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import TextareaField from '@/Pages/Molecules/data-input/TextareaField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import ToggleField from '@/Pages/Molecules/data-input/ToggleField.vue';
import RichTextEditorField from '@/Pages/Molecules/data-input/RichTextEditorField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';

const props = defineProps({
    open: {
        type: Boolean,
        default: false
    },
    sectionType: {
        type: String,
        required: true,
        validator: (v) => ['text', 'image', 'gallery', 'video', 'entity_table'].includes(v)
    },
    initialParams: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['close', 'validated']);

// Paramètres du formulaire
const params = ref({ ...props.initialParams });

// Réinitialiser les params quand le type change
watch(() => props.sectionType, (newType) => {
    params.value = getDefaultParamsForType(newType);
}, { immediate: true });

// Réinitialiser les params quand initialParams change
watch(() => props.initialParams, (newParams) => {
    if (Object.keys(newParams).length > 0) {
        params.value = { ...newParams };
    } else {
        params.value = getDefaultParamsForType(props.sectionType);
    }
}, { deep: true });

/**
 * Retourne les paramètres par défaut selon le type
 */
function getDefaultParamsForType(type) {
    switch (type) {
        case 'text':
            return { content: '', align: 'left', size: 'md' };
        case 'image':
            return { src: '', alt: '', caption: '', align: 'center', size: 'md' };
        case 'gallery':
            return { images: [], columns: 3, gap: 'md' };
        case 'video':
            return { src: '', type: 'youtube', autoplay: false, controls: true };
        case 'entity_table':
            return { entity: '', filters: {}, columns: [] };
        default:
            return {};
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
    switch (props.sectionType) {
        case 'text':
            return !!params.value.content;
        case 'image':
            return !!params.value.src && !!params.value.alt;
        case 'gallery':
            return Array.isArray(params.value.images) && params.value.images.length > 0;
        case 'video':
            return !!params.value.src && !!params.value.type;
        case 'entity_table':
            return !!params.value.entity;
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
    emit('validated', { ...params.value });
};

/**
 * Gère la fermeture du modal
 */
const handleClose = () => {
    emit('close');
};

/**
 * Titre du modal selon le type
 */
const modalTitle = computed(() => {
    const titles = {
        text: 'Paramètres de la section texte',
        image: 'Paramètres de l\'image',
        gallery: 'Paramètres de la galerie',
        video: 'Paramètres de la vidéo',
        entity_table: 'Paramètres du tableau d\'entités'
    };
    return titles[props.sectionType] || 'Paramètres de la section';
});
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
                {{ modalTitle }}
            </h3>
        </template>

        <div class="space-y-4">
            <!-- Type TEXT -->
            <template v-if="sectionType === 'text'">
                <RichTextEditorField
                    v-model="params.content"
                    label="Contenu"
                    required
                    helper="Contenu de la section (formatage riche disponible)"
                />
                <SelectField
                    v-model="params.align"
                    label="Alignement"
                    :options="alignOptions"
                />
                <SelectField
                    v-model="params.size"
                    label="Taille du texte"
                    :options="sizeOptions"
                />
            </template>

            <!-- Type IMAGE -->
            <template v-if="sectionType === 'image'">
                <InputField
                    v-model="params.src"
                    label="URL de l'image"
                    type="url"
                    required
                    placeholder="https://example.com/image.jpg"
                />
                <InputField
                    v-model="params.alt"
                    label="Texte alternatif"
                    required
                    placeholder="Description de l'image"
                />
                <TextareaField
                    v-model="params.caption"
                    label="Légende (optionnel)"
                    placeholder="Légende de l'image"
                />
                <SelectField
                    v-model="params.align"
                    label="Alignement"
                    :options="alignOptions"
                />
                <SelectField
                    v-model="params.size"
                    label="Taille"
                    :options="imageSizeOptions"
                />
            </template>

            <!-- Type GALLERY -->
            <template v-if="sectionType === 'gallery'">
                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle"></i>
                    <span class="text-sm">La gestion des images de la galerie sera implémentée prochainement.</span>
                </div>
                <SelectField
                    v-model="params.columns"
                    label="Nombre de colonnes"
                    :options="columnsOptions"
                />
                <SelectField
                    v-model="params.gap"
                    label="Espacement"
                    :options="gapOptions"
                />
            </template>

            <!-- Type VIDEO -->
            <template v-if="sectionType === 'video'">
                <SelectField
                    v-model="params.type"
                    label="Type de vidéo"
                    :options="videoTypeOptions"
                    required
                />
                <InputField
                    v-model="params.src"
                    label="URL de la vidéo"
                    type="url"
                    required
                    placeholder="https://www.youtube.com/watch?v=..."
                />
                <ToggleField
                    v-model="params.autoplay"
                    label="Lecture automatique"
                />
                <ToggleField
                    v-model="params.controls"
                    label="Afficher les contrôles"
                />
            </template>

            <!-- Type ENTITY_TABLE -->
            <template v-if="sectionType === 'entity_table'">
                <InputField
                    v-model="params.entity"
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

