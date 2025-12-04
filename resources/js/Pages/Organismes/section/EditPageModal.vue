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
import { computed, watch, ref } from 'vue';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import ToggleField from '@/Pages/Molecules/data-input/ToggleField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Alert from '@/Pages/Atoms/feedback/Alert.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { getPageStateOptions } from '@/Utils/enums/PageState';
import { getVisibilityOptions } from '@/Utils/enums/Visibility';
import { Page } from '@/Models';

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

// Utiliser le modèle Page pour normaliser l'accès aux données
const pageModel = computed(() => {
    if (!props.page) return null;
    return new Page(props.page);
});

// Options pour les selects
const stateOptions = computed(() => getPageStateOptions());
const visibilityOptions = computed(() => getVisibilityOptions());

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

// Formulaire - initialiser avec les valeurs de la page
const form = useForm({
    title: '',
    slug: '',
    is_visible: 'guest',
    can_edit_role: 'admin',
    in_menu: true,
    state: 'draft',
    parent_id: null,
    menu_order: 0
});

// Réinitialiser le formulaire quand le modal s'ouvre ou que la page change
watch([() => props.open, () => props.page], ([isOpen, page]) => {
    if (isOpen && pageModel.value) {
        const page = pageModel.value;
        
        // Utiliser les méthodes du modèle pour extraire les données
        form.title = page.title || '';
        form.slug = page.slug || '';
        form.is_visible = page.isVisible || 'guest';
        form.can_edit_role = page.canEditRole || 'admin';
        form.in_menu = page.inMenu ?? true;
        form.state = page.state || 'draft';
        form.parent_id = page.parentId || null;
        form.menu_order = page.menuOrder || 0;
        
        form.clearErrors();
    }
}, { immediate: true });

// Réinitialiser le formulaire quand le modal se ferme
watch(() => props.open, (isOpen) => {
    if (!isOpen) {
        form.reset();
        form.clearErrors();
    }
});

// Génération automatique du slug depuis le titre (seulement si le slug est vide)
watch(() => form.title, (newTitle) => {
    if (newTitle && !form.slug) {
        form.slug = newTitle
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
});

// Validation computed pour chaque champ
const titleValidation = computed(() => {
    if (!form.errors.title) return null;
    return {
        state: 'error',
        message: form.errors.title,
        showNotification: false
    };
});

const slugValidation = computed(() => {
    if (!form.errors.slug) return null;
    return {
        state: 'error',
        message: form.errors.slug,
        showNotification: false
    };
});

// URL de la page
const pageUrl = computed(() => {
    if (!pageModel.value) return '';
    return pageModel.value.fullUrl;
});

// Copier l'URL dans le presse-papiers
const copyUrl = async () => {
    try {
        await navigator.clipboard.writeText(pageUrl.value);
        // Optionnel : afficher une notification de succès
        // Vous pouvez utiliser un système de notification si disponible
    } catch (err) {
        console.error('Erreur lors de la copie de l\'URL:', err);
    }
};

// Soumettre le formulaire
const submit = () => {
    if (!pageModel.value) {
        console.error('Page model is missing');
        return;
    }
    
    const pageId = pageModel.value.id;
    if (!pageId) {
        console.error('Page ID is missing', pageModel.value);
        return;
    }
    
    form.patch(route('pages.update', { page: pageId }), {
        preserveScroll: true,
        onSuccess: () => {
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
        placement="middle"
        animation="fade"
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
                        <Icon source="fa-solid fa-link" pack="solid" size="sm" />
                    </Btn>
                    <!-- Bouton supprimer -->
                    <Btn
                        v-if="pageModel?.canDelete"
                        variant="error"
                        size="sm"
                        @click="deletePage"
                        title="Supprimer la page"
                    >
                        <Icon source="fa-solid fa-trash-can" pack="solid" size="sm" />
                    </Btn>
                </div>
            </div>
        </template>

        <form @submit.prevent="submit" class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
            <!-- Message d'erreur global -->
            <Alert
                v-if="Object.keys(form.errors).length > 0"
                type="error"
            >
                <ul class="list-disc list-inside">
                    <li v-for="(error, key) in form.errors" :key="key">
                        {{ error }}
                    </li>
                </ul>
            </Alert>
            
            <!-- Titre -->
            <InputField
                v-model="form.title"
                label="Titre"
                type="text"
                required
                :validation="titleValidation"
                placeholder="Titre de la page"
            />
            
            <!-- Slug -->
            <InputField
                v-model="form.slug"
                label="Slug"
                type="text"
                required
                :validation="slugValidation"
                placeholder="url-de-la-page"
                helper="L'URL de la page (généré automatiquement depuis le titre)"
            />
            
            <!-- Visibilité -->
            <SelectField
                v-model="form.is_visible"
                label="Visibilité"
                :options="visibilityOptions"
                required
                helper="Qui peut voir cette page ?"
            />
            
            <!-- Rôle requis pour modifier -->
            <SelectField
                v-model="form.can_edit_role"
                label="Rôle requis pour modifier"
                :options="visibilityOptions"
                required
                helper="Rôle minimum requis pour modifier cette page (admin par défaut)"
            />
            
            <!-- État -->
            <SelectField
                v-model="form.state"
                label="État"
                :options="stateOptions"
                required
                helper="État de publication de la page"
            />
            
            <!-- Page parente -->
            <SelectField
                v-model="form.parent_id"
                label="Page parente"
                :options="parentPageOptions"
                helper="Page parente pour créer un menu hiérarchique (optionnel)"
            />
            
            <!-- Dans le menu -->
            <ToggleField
                v-model="form.in_menu"
                label="Afficher dans le menu"
                helper="Si activé, la page apparaîtra dans le menu de navigation"
            />
            
            <!-- Ordre dans le menu -->
            <InputField
                v-model="form.menu_order"
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
                    variant="primary"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Enregistrement...</span>
                    <span v-else>Enregistrer les modifications</span>
                </Btn>
            </div>
        </form>
    </Modal>
</template>

<style scoped lang="scss">
// Styles spécifiques si nécessaire
</style>

