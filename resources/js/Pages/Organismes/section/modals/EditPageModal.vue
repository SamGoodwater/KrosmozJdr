<script setup>
/**
 * EditPageModal Component
 * 
 * @description
 * Modal pour modifier une page dynamique existante.
 * Utilise le composable usePageForm pour réduire la duplication.
 * Conserve les fonctionnalités spécifiques : onglets, suppression, copie URL.
 * 
 * @props {Boolean} open - Contrôle l'ouverture du modal
 * @props {Object} page - Données de la page à modifier
 * @props {Array} pages - Liste des pages disponibles (pour parent_id)
 * @emits close - Événement émis quand le modal se ferme
 * @emits deleted - Événement émis quand la page est supprimée
 */
import { router } from '@inertiajs/vue3';
import { computed, watch, ref, nextTick } from 'vue';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import ToggleField from '@/Pages/Molecules/data-input/ToggleField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Alert from '@/Pages/Atoms/feedback/Alert.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { Page } from '@/Models';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import PageSectionEditor from '../PageSectionEditor.vue';
import { usePageFormOptions } from '@/Composables/pages/usePageFormOptions';
import { usePageForm } from '@/Composables/pages/usePageForm';

const props = defineProps({
    open: {
        type: Boolean,
        default: false
    },
    page: {
        type: Object,
        default: null
    },
    pages: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['close', 'deleted']);

// Onglet actif
const activeTab = ref('general');

// Extraire les données de la page (gérer la structure Resource)
const pageData = computed(() => {
    if (!props.page) return {};
    if (props.page.data && typeof props.page.data === 'object') {
        return props.page.data;
    }
    return props.page;
});

// Modèle Page normalisé
const pageModel = computed(() => {
    if (!pageData.value) return null;
    return new Page(pageData.value);
});

// Options pour les selects
const { stateOptions, roleOptions, parentPageOptions } = usePageFormOptions(
    () => props.pages,
    computed(() => pageModel.value?.id ?? null)
);

// Formulaire via composable (sera initialisé dans le watcher)
let formControls = null;

// Instance du formulaire (réactive)
const formInstance = ref(null);

/**
 * Initialise le formulaire depuis les données de la page
 */
const initializeForm = () => {
    const model = pageModel.value;
    
    if (!model || !model.id) {
        console.warn('EditPageModal - Model not available for initialization');
        return false;
    }
    
    // Préparer les données initiales
    const initialData = {
        title: model.title || '',
        slug: model.slug || '',
        read_level: model.readLevel ?? 0,
        write_level: model.writeLevel ?? 4,
        in_menu: model.inMenu ?? true,
        state: model.state || 'draft',
        parent_id: model.parentId || null,
        menu_order: model.menuOrder || 0
    };
    
    // Créer le formulaire via le composable
    formControls = usePageForm(initialData, { mode: 'edit' });
    formInstance.value = formControls.form;
    
    return true;
};

// Initialiser le formulaire quand le modal s'ouvre
watch(() => [props.open, props.page], ([isOpen, page]) => {
    if (isOpen && page) {
        nextTick(() => {
            nextTick(() => {
                const initialized = initializeForm();
                
                if (!initialized) {
                    // Réessayer après un court délai
                    setTimeout(() => {
                        initializeForm();
                    }, 100);
                }
            });
        });
    }
}, { immediate: true, deep: true });

// Validation computed (via composable si disponible)
const titleValidation = computed(() => {
    if (!formControls) return null;
    return formControls.titleValidation.value;
});

const slugValidation = computed(() => {
    if (!formControls) return null;
    return formControls.slugValidation.value;
});

// Gestion du slug manuel
const handleSlugInput = () => {
    if (formControls) {
        formControls.handleSlugInput();
    }
};

// URL de la page
const pageUrl = computed(() => {
    if (!pageModel.value) return '';
    return pageModel.value.fullUrl;
});

// Copier l'URL dans le presse-papiers
const { copyToClipboard } = useCopyToClipboard();
const copyUrl = async () => {
    if (!pageUrl.value) return;
    await copyToClipboard(pageUrl.value, 'URL de la page copiée !');
};

// Soumettre le formulaire
const submit = () => {
    if (!formInstance.value || !pageModel.value) {
        console.error('EditPageModal - Form or model is missing');
        return;
    }
    
    const pageId = pageModel.value.id;
    
    formInstance.value.patch(route('pages.update', { page: pageId }), {
        preserveScroll: true,
        onSuccess: () => {
            emit('close');
            router.reload({ only: ['page', 'pages'] });
        }
    });
};

// Supprimer la page
const deletePage = () => {
    if (!pageModel.value) {
        console.error('Page model is missing');
        return;
    }
    
    const pageId = pageModel.value.id;
    const pageTitle = pageModel.value.title || 'cette page';
    
    if (confirm(`Êtes-vous sûr de vouloir supprimer la page "${pageTitle}" ?`)) {
        router.delete(route('pages.delete', pageId), {
            preserveScroll: true,
            onSuccess: () => {
                emit('deleted');
                emit('close');
                router.reload({ only: ['pages'] });
            }
        });
    }
};

const handleClose = () => {
    emit('close');
};
</script>

<template>
    <Modal 
        :open="open" 
        size="xl" 
        placement="middle-center"
        close-on-esc
        @close="handleClose"
    >
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h3 class="text-2xl font-bold">Modifier la page : {{ pageModel?.title || 'Page' }}</h3>
                <div class="flex gap-2">
                    <!-- Bouton copier l'URL -->
                    <Btn
                        v-if="pageModel?.slug"
                        variant="ghost"
                        size="sm"
                        @click="copyUrl"
                        title="Copier l'URL de la page"
                    >
                        <Icon source="fa-link" pack="solid" alt="Copier l'URL" size="sm" />
                    </Btn>
                    <!-- Bouton supprimer -->
                    <Btn
                        v-if="pageModel?.canDelete"
                        color="error"
                        size="sm"
                        @click="deletePage"
                        title="Supprimer la page"
                    >
                        <Icon source="fa-trash-can" pack="solid" alt="Supprimer" size="sm" />
                    </Btn>
                </div>
            </div>
        </template>

        <!-- Onglets -->
        <div class="tabs tabs-lifted mb-4">
            <button
                :class="['tab', activeTab === 'general' && 'tab-active']"
                @click="activeTab = 'general'"
                type="button"
            >
                <Icon source="fa-gear" pack="solid" alt="Général" class="mr-2" />
                Général
            </button>
            <button
                :class="['tab', activeTab === 'sections' && 'tab-active']"
                @click="activeTab = 'sections'"
                type="button"
            >
                <Icon source="fa-list" pack="solid" alt="Sections" class="mr-2" />
                Sections
            </button>
        </div>

        <!-- Contenu de l'onglet Général -->
        <form v-if="activeTab === 'general' && formInstance" @submit.prevent="submit" class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
            <!-- Message d'erreur global -->
            <Alert
                v-if="formInstance && Object.keys(formInstance.errors || {}).length > 0"
                type="error"
            >
                <ul class="list-disc list-inside">
                    <li v-for="(error, key) in (formInstance?.errors || {})" :key="key">
                        {{ error }}
                    </li>
                </ul>
            </Alert>
            
            <!-- Titre -->
            <InputField
                v-model="formInstance.title"
                label="Titre"
                type="text"
                required
                :validation="titleValidation"
                placeholder="Titre de la page"
            />
            
            <!-- Slug -->
            <InputField
                v-model="formInstance.slug"
                label="Slug"
                type="text"
                required
                :validation="slugValidation"
                placeholder="url-de-la-page"
                helper="L'URL de la page (généré automatiquement depuis le titre)"
                @input="handleSlugInput"
            />
            
            <!-- Lecture (min.) -->
            <SelectField
                v-model="formInstance.read_level"
                label="Lecture (min.)"
                :options="roleOptions"
                required
                helper="Qui peut voir cette page ?"
            />
            
            <!-- Écriture (min.) -->
            <SelectField
                v-model="formInstance.write_level"
                label="Écriture (min.)"
                :options="roleOptions"
                required
                helper="Rôle minimum requis pour modifier cette page (admin par défaut)"
            />
            
            <!-- État -->
            <SelectField
                v-model="formInstance.state"
                label="État"
                :options="stateOptions"
                required
                helper="Cycle de vie de la page"
            />
            
            <!-- Page parente -->
            <SelectField
                v-model="formInstance.parent_id"
                label="Page parente"
                :options="parentPageOptions"
                helper="Page parente pour créer un menu hiérarchique (optionnel)"
            />
            
            <!-- Dans le menu -->
            <ToggleField
                v-model="formInstance.in_menu"
                label="Afficher dans le menu"
                helper="Si activé, la page apparaîtra dans le menu de navigation"
            />
            
            <!-- Ordre dans le menu -->
            <InputField
                v-model="formInstance.menu_order"
                label="Ordre dans le menu"
                type="number"
                min="0"
                helper="Ordre d'affichage dans le menu (0 = premier)"
            />
            
            <!-- Actions -->
            <div class="flex justify-end gap-2 pt-4 border-t border-base-300">
                <Btn
                    type="button"
                    variant="ghost"
                    @click="handleClose"
                >
                    Annuler
                </Btn>
                <Btn
                    type="submit"
                    color="primary"
                    :disabled="formInstance.processing"
                >
                    <span v-if="formInstance.processing">Enregistrement...</span>
                    <span v-else>Enregistrer les modifications</span>
                </Btn>
            </div>
        </form>

        <!-- Contenu de l'onglet Sections -->
        <div v-if="activeTab === 'sections'" class="max-h-[70vh] overflow-y-auto pr-2">
            <PageSectionEditor
                v-if="pageModel && pageModel.sections && Array.isArray(pageModel.sections)"
                :sections="pageModel.sections"
                :page-id="pageModel.id"
                :can-edit="pageModel.canUpdate"
            />
            <div v-else class="text-center py-8 text-base-content/50">
                <p>Aucune section disponible pour cette page.</p>
            </div>
        </div>
    </Modal>
</template>

<style scoped lang="scss">
// Styles spécifiques si nécessaire
</style>
