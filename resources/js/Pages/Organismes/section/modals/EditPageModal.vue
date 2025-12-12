<script setup>
/**
 * EditPageModal Component
 * 
 * @description
 * Modal pour modifier une page dynamique existante.
 * - Formulaire complet pour modifier une page
 * - Validation des champs
 * - Gestion des erreurs
 * - Bouton pour supprimer la page
 * - Bouton pour copier l'URL
 * 
 * @props {Boolean} open - Contrôle l'ouverture du modal
 * @props {Object} page - Données de la page à modifier
 * @props {Array} pages - Liste des pages disponibles (pour parent_id)
 * @emits close - Événement émis quand le modal se ferme
 * @emits deleted - Événement émis quand la page est supprimée
 */
import { useForm, router } from '@inertiajs/vue3';
import { computed, watch, ref, nextTick } from 'vue';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import ToggleField from '@/Pages/Molecules/data-input/ToggleField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Alert from '@/Pages/Atoms/feedback/Alert.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Tab from '@/Pages/Molecules/navigation/Tab.vue';
import TabItem from '@/Pages/Atoms/navigation/TabItem.vue';
import { getPageStateOptions } from '@/Utils/enums/PageState';
import { getVisibilityOptions } from '@/Utils/enums/Visibility';
import { Page } from '@/Models';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import PageSectionEditor from '../PageSectionEditor.vue';

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

// ============================================
// 1. CHARGEMENT DU MODÈLE PAGE
// ============================================
// Extraire les données de la page (gérer la structure Resource qui peut être dans data)
const pageData = computed(() => {
    if (!props.page) return {};
    
    // Si props.page a une propriété data, utiliser data (structure Resource)
    if (props.page.data && typeof props.page.data === 'object') {
        return props.page.data;
    }
    
    // Sinon, utiliser props.page directement
    return props.page;
});

// Utiliser le modèle Page pour normaliser l'accès aux données
const pageModel = computed(() => {
    if (!pageData.value) return null;
    return new Page(pageData.value);
});

// ============================================
// 2. COPIE DU MODÈLE POUR POUVOIR REVENIR EN ARRIÈRE
// ============================================
// Copie originale du modèle (pour annulation)
const originalModelData = ref(null);

// Copie de travail du modèle (modifiable)
const workingModelData = ref(null);

// Options pour les selects
const stateOptions = computed(() => getPageStateOptions());
const visibilityOptions = computed(() => getVisibilityOptions());

// ============================================
// COMPUTED POUR FORCER LA RÉACTIVITÉ DES SELECTS
// ============================================
// Computed pour forcer la réactivité des valeurs des selects depuis formInstance
// Computed réactifs pour les selects - incluent formInstanceVersion pour forcer la réactivité
const formIsVisible = computed({
    get: () => {
        // Toucher formInstanceVersion pour forcer la réactivité
        formInstanceVersion.value;
        return formInstance.value?.is_visible || 'guest';
    },
    set: (value) => {
        if (formInstance.value) {
            formInstance.value.is_visible = value;
            formInstanceVersion.value++;
        }
    }
});

const formCanEditRole = computed({
    get: () => {
        // Toucher formInstanceVersion pour forcer la réactivité
        formInstanceVersion.value;
        return formInstance.value?.can_edit_role || 'admin';
    },
    set: (value) => {
        if (formInstance.value) {
            formInstance.value.can_edit_role = value;
            formInstanceVersion.value++;
        }
    }
});

const formState = computed({
    get: () => {
        // Toucher formInstanceVersion pour forcer la réactivité
        formInstanceVersion.value;
        return formInstance.value?.state || 'draft';
    },
    set: (value) => {
        if (formInstance.value) {
            formInstance.value.state = value;
            formInstanceVersion.value++;
        }
    }
});

const formParentId = computed({
    get: () => {
        // Toucher formInstanceVersion pour forcer la réactivité
        formInstanceVersion.value;
        return formInstance.value?.parent_id ?? null;
    },
    set: (value) => {
        if (formInstance.value) {
            formInstance.value.parent_id = value;
            formInstanceVersion.value++;
        }
    }
});

const parentPageOptions = computed(() => {
    const currentPageId = pageModel.value?.id;
    return [
        { value: null, label: 'Aucune (page racine)' },
        ...props.pages
            .filter(p => {
                const page = new Page(p);
                return page.id !== currentPageId;
            })
            .map(page => {
                const pageModel = new Page(page);
                return {
                    value: pageModel.id,
                    label: pageModel.title
                };
            })
    ];
});

// ============================================
// 3. INITIALISATION DU MODÈLE (COPIE)
// ============================================
/**
 * Initialise les copies du modèle depuis le modèle Page
 */
const initializeModel = () => {
    const model = pageModel.value;
    
    if (!model || !model.id) {
        if (import.meta.env.DEV) {
            console.warn('EditPageModal - Model not available for initialization');
        }
        return false;
    }
    
    // Créer une copie profonde des données du modèle
    const modelData = {
        id: model.id,
        title: model.title || '',
        slug: model.slug || '',
        is_visible: model.isVisible || 'guest',
        can_edit_role: model.canEditRole || 'admin',
        in_menu: model.inMenu ?? true,
        state: model.state || 'draft',
        parent_id: model.parentId || null,
        menu_order: model.menuOrder || 0
    };
    
    // Sauvegarder la copie originale
    originalModelData.value = JSON.parse(JSON.stringify(modelData));
    
    // Créer la copie de travail
    workingModelData.value = JSON.parse(JSON.stringify(modelData));
    
    // Créer formInstance immédiatement après avoir créé workingModelData
    // pour éviter les problèmes de timing
    const formData = {
        title: workingModelData.value.title || '',
        slug: workingModelData.value.slug || '',
        is_visible: workingModelData.value.is_visible || 'guest',
        can_edit_role: workingModelData.value.can_edit_role || 'admin',
        in_menu: workingModelData.value.in_menu ?? true,
        state: workingModelData.value.state || 'draft',
        parent_id: workingModelData.value.parent_id !== undefined ? workingModelData.value.parent_id : null,
        menu_order: workingModelData.value.menu_order || 0
    };
    
    formInstance.value = useForm(formData);
    
    // Incrémenter formInstanceVersion pour forcer la réactivité des computed
    formInstanceVersion.value++;
    
    if (import.meta.env.DEV) {
        console.log('EditPageModal - Model initialized:', {
            original: originalModelData.value,
            working: workingModelData.value,
            formData,
            formInstanceValues: {
                is_visible: formInstance.value.is_visible,
                can_edit_role: formInstance.value.can_edit_role,
                state: formInstance.value.state,
                parent_id: formInstance.value.parent_id
            }
        });
    }
    
    return true;
};

// ============================================
// 4. FORMULAIRE INERTIA (LIÉ AU MODÈLE DE TRAVAIL)
// ============================================
// Formulaire Inertia - initialisé depuis workingModelData
const form = computed(() => {
    if (!workingModelData.value) {
        return useForm({
            title: '',
            slug: '',
            is_visible: 'guest',
            can_edit_role: 'admin',
            in_menu: true,
            state: 'draft',
            parent_id: null,
            menu_order: 0
        });
    }
    
    return useForm({
        title: workingModelData.value.title,
        slug: workingModelData.value.slug,
        is_visible: workingModelData.value.is_visible,
        can_edit_role: workingModelData.value.can_edit_role,
        in_menu: workingModelData.value.in_menu,
        state: workingModelData.value.state,
        parent_id: workingModelData.value.parent_id,
        menu_order: workingModelData.value.menu_order
    });
});

// Instance réactive du formulaire
const formInstance = ref(null);

// Compteur de version pour forcer la réactivité des selects
const formInstanceVersion = ref(0);

/**
 * Crée ou met à jour formInstance depuis workingModelData
 */
const createFormInstance = () => {
    if (!workingModelData.value) {
        formInstance.value = null;
        return;
    }
    
    // Créer un nouveau formulaire avec les données actuelles
    // Utiliser des valeurs explicites pour garantir la réactivité
    const formData = {
        title: workingModelData.value.title || '',
        slug: workingModelData.value.slug || '',
        is_visible: workingModelData.value.is_visible || 'guest',
        can_edit_role: workingModelData.value.can_edit_role || 'admin',
        in_menu: workingModelData.value.in_menu ?? true,
        state: workingModelData.value.state || 'draft',
        parent_id: workingModelData.value.parent_id !== undefined ? workingModelData.value.parent_id : null,
        menu_order: workingModelData.value.menu_order || 0
    };
    
    formInstance.value = useForm(formData);
    
    if (import.meta.env.DEV) {
        console.log('EditPageModal - FormInstance created:', {
            formData,
            formInstanceValues: {
                is_visible: formInstance.value.is_visible,
                can_edit_role: formInstance.value.can_edit_role,
                state: formInstance.value.state,
                parent_id: formInstance.value.parent_id
            }
        });
    }
};

// Initialiser formInstance quand workingModelData change
// Note: formInstance est maintenant créé directement dans initializeModel()
// Ce watcher ne sert que de fallback si workingModelData change sans passer par initializeModel()
watch(() => workingModelData.value, (newData) => {
    // Ne créer formInstance que s'il n'existe pas encore
    // (pour éviter de le recréer inutilement si initializeModel() l'a déjà créé)
    if (newData && !formInstance.value) {
        createFormInstance();
    }
}, { immediate: false, deep: true });

// ============================================
// 5. LIAISON DES CHAMPS AU MODÈLE DE TRAVAIL
// ============================================
// Watchers pour synchroniser les modifications du formulaire avec workingModelData
watch(() => formInstance.value?.title, (newValue) => {
    if (workingModelData.value && newValue !== undefined) {
        workingModelData.value.title = newValue;
    }
});

watch(() => formInstance.value?.slug, (newValue) => {
    if (workingModelData.value && newValue !== undefined) {
        workingModelData.value.slug = newValue;
    }
});

// Utiliser les computed pour la synchronisation des selects
watch(() => formIsVisible.value, (newValue) => {
    if (workingModelData.value && newValue !== undefined) {
        workingModelData.value.is_visible = newValue;
    }
});

watch(() => formCanEditRole.value, (newValue) => {
    if (workingModelData.value && newValue !== undefined) {
        workingModelData.value.can_edit_role = newValue;
    }
});

watch(() => formState.value, (newValue) => {
    if (workingModelData.value && newValue !== undefined) {
        workingModelData.value.state = newValue;
    }
});

watch(() => formParentId.value, (newValue) => {
    if (workingModelData.value && newValue !== undefined) {
        workingModelData.value.parent_id = newValue;
    }
});

watch(() => formInstance.value?.in_menu, (newValue) => {
    if (workingModelData.value && newValue !== undefined) {
        workingModelData.value.in_menu = newValue;
    }
});

watch(() => formInstance.value?.menu_order, (newValue) => {
    if (workingModelData.value && newValue !== undefined) {
        workingModelData.value.menu_order = newValue;
    }
});

// Réinitialiser le formulaire quand le modal s'ouvre ou quand la page change
watch(() => [props.open, props.page], ([isOpen, page]) => {
    if (import.meta.env.DEV) {
        console.log('EditPageModal - Watcher triggered:', { isOpen, hasPage: !!page, hasModel: !!pageModel.value });
    }
    
    if (isOpen && pageModel.value && pageModel.value.id) {
        // Utiliser nextTick pour s'assurer que les computed sont à jour et que le modal est rendu
        nextTick(() => {
            // Double nextTick pour garantir que le DOM du modal est complètement rendu
            nextTick(() => {
                const initialized = initializeModel();
                
                // Si l'initialisation a échoué, réessayer après un court délai
                if (!initialized) {
                    if (import.meta.env.DEV) {
                        console.log('EditPageModal - Retrying model initialization in 100ms...');
                    }
                    setTimeout(() => {
                        initializeModel();
                    }, 100);
                } else {
                    // formInstance est maintenant créé directement dans initializeModel()
                    // donc il devrait être disponible immédiatement
                    if (import.meta.env.DEV) {
                        nextTick(() => {
                            console.log('EditPageModal - Form values after init:', {
                                hasFormInstance: !!formInstance.value,
                                title: formInstance.value?.title,
                                slug: formInstance.value?.slug,
                                is_visible: formInstance.value?.is_visible,
                                can_edit_role: formInstance.value?.can_edit_role,
                                state: formInstance.value?.state,
                                parent_id: formInstance.value?.parent_id
                            });
                        });
                    }
                }
            });
        });
    }
    // Ne pas réinitialiser le formulaire quand le modal se ferme
    // pour éviter de perdre les données si l'utilisateur rouvre le modal
}, { immediate: true, deep: true });

// Le watcher ci-dessus surveille déjà props.open et props.page, donc pas besoin d'autres watchers

// Génération automatique du slug depuis le titre (seulement si le slug est vide)
watch(() => formInstance.value?.title, (newTitle) => {
    if (newTitle && formInstance.value && !formInstance.value.slug) {
        formInstance.value.slug = newTitle
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
});

// Validation computed pour chaque champ
const titleValidation = computed(() => {
    if (!formInstance.value?.errors?.title) return null;
    return {
        state: 'error',
        message: formInstance.value.errors.title,
        showNotification: false
    };
});

const slugValidation = computed(() => {
    if (!formInstance.value?.errors?.slug) return null;
    return {
        state: 'error',
        message: formInstance.value.errors.slug,
        showNotification: false
    };
});

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

// ============================================
// 6. ENREGISTREMENT (ENVOI AU BACKEND)
// ============================================
/**
 * Soumet le formulaire avec les données du modèle de travail
 */
const submit = () => {
    if (!formInstance.value || !workingModelData.value) {
        console.error('EditPageModal - Form or working model is missing');
        return;
    }
    
    const pageId = workingModelData.value.id;
    if (!pageId) {
        console.error('EditPageModal - Page ID is missing', workingModelData.value);
        return;
    }
    
    // Utiliser formInstance pour envoyer les données
    formInstance.value.patch(route('pages.update', { page: pageId }), {
        preserveScroll: true,
        onSuccess: () => {
            // Mettre à jour la copie originale avec les nouvelles données
            originalModelData.value = JSON.parse(JSON.stringify(workingModelData.value));
            emit('close');
            router.reload({ only: ['page', 'pages'] });
        },
        onError: () => {
            // Les erreurs sont gérées automatiquement par Inertia
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
    
    if (!pageId) {
        console.error('Page ID is missing', pageModel.value);
        return;
    }
    
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
        <form v-if="activeTab === 'general'" :key="`form-${pageModel?.id || 'new'}`" @submit.prevent="submit" class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
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
                v-if="formInstance"
                v-model="formInstance.title"
                label="Titre"
                type="text"
                required
                :validation="titleValidation"
                placeholder="Titre de la page"
            />
            
            <!-- Slug -->
            <InputField
                v-if="formInstance"
                v-model="formInstance.slug"
                label="Slug"
                type="text"
                required
                :validation="slugValidation"
                placeholder="url-de-la-page"
                helper="L'URL de la page (généré automatiquement depuis le titre)"
            />
            
            <!-- Visibilité -->
            <SelectField
                v-if="formInstance"
                :key="`is_visible-${formInstance.is_visible || 'none'}-${pageModel?.id || 'new'}`"
                v-model="formIsVisible"
                label="Visibilité"
                :options="visibilityOptions"
                required
                helper="Qui peut voir cette page ?"
            />
            
            <!-- Rôle requis pour modifier -->
            <SelectField
                v-if="formInstance"
                :key="`can_edit_role-${formInstance.can_edit_role || 'none'}-${pageModel?.id || 'new'}`"
                v-model="formCanEditRole"
                label="Rôle requis pour modifier"
                :options="visibilityOptions"
                required
                helper="Rôle minimum requis pour modifier cette page (admin par défaut)"
            />
            
            <!-- État -->
            <SelectField
                v-if="formInstance"
                :key="`state-${formInstance.state || 'none'}-${pageModel?.id || 'new'}`"
                v-model="formState"
                label="État"
                :options="stateOptions"
                required
                helper="État de publication de la page"
            />
            
            <!-- Page parente -->
            <SelectField
                v-if="formInstance"
                :key="`parent_id-${formInstance.parent_id || 'null'}-${pageModel?.id || 'new'}`"
                v-model="formParentId"
                label="Page parente"
                :options="parentPageOptions"
                helper="Page parente pour créer un menu hiérarchique (optionnel)"
            />
            
            <!-- Dans le menu -->
            <ToggleField
                v-if="formInstance"
                v-model="formInstance.in_menu"
                label="Afficher dans le menu"
                helper="Si activé, la page apparaîtra dans le menu de navigation"
            />
            
            <!-- Ordre dans le menu -->
            <InputField
                v-if="formInstance"
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
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Enregistrement...</span>
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

