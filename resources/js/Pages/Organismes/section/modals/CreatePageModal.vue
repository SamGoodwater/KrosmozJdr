<script setup>
/**
 * CreatePageModal Component
 * 
 * @description
 * Modal pour créer une nouvelle page dynamique.
 * - Formulaire complet pour créer une nouvelle page
 * - Validation des champs
 * - Gestion des erreurs
 * 
 * @props {Boolean} open - Contrôle l'ouverture du modal
 * @props {Array} pages - Liste des pages disponibles (pour parent_id)
 * @emits close - Événement émis quand le modal se ferme
 */
import { useForm } from '@inertiajs/vue3';
import { computed, watch, ref } from 'vue';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import ToggleField from '@/Pages/Molecules/data-input/ToggleField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Alert from '@/Pages/Atoms/feedback/Alert.vue';
import { PageState, getPageStateOptions } from '@/Utils/enums/PageState';
import { Visibility, getVisibilityOptions } from '@/Utils/enums/Visibility';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    open: {
        type: Boolean,
        default: false
    },
    pages: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['close']);

// Options pour les selects
const stateOptions = computed(() => getPageStateOptions());
const visibilityOptions = computed(() => getVisibilityOptions());

const parentPageOptions = computed(() => {
    return [
        { value: null, label: 'Aucune (page racine)' },
        ...props.pages.map(page => ({
            value: page.id,
            label: page.title
        }))
    ];
});

// Formulaire
const form = useForm({
    title: '',
    slug: '',
    is_visible: Visibility.GUEST.value,
    can_edit_role: Visibility.ADMIN.value,
    in_menu: true,
    state: PageState.DRAFT.value,
    parent_id: null,
    menu_order: 0
});

// Flag pour savoir si l'utilisateur a modifié manuellement le slug
const slugManuallyEdited = ref(false);

// Fonction pour générer le slug depuis le titre
const generateSlug = (title) => {
    if (!title) return '';
    return title
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
};

// Génération automatique du slug depuis le titre (seulement si pas modifié manuellement)
watch(() => form.title, (newTitle) => {
    if (newTitle && !slugManuallyEdited.value) {
        form.slug = generateSlug(newTitle);
    }
});

// Détecter si l'utilisateur modifie manuellement le slug
const handleSlugInput = () => {
    slugManuallyEdited.value = true;
};

// Réinitialiser le formulaire quand le modal se ferme
watch(() => props.open, (isOpen) => {
    if (!isOpen) {
        form.reset();
        form.clearErrors();
        slugManuallyEdited.value = false; // Réinitialiser le flag
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

const handleClose = () => {
    emit('close');
};

const submit = () => {
    form.post(route('pages.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit('close');
            router.reload({ only: ['pages'] });
        },
        onError: () => {
            // Les erreurs sont gérées automatiquement par Inertia
        }
    });
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
            <h3 class="text-2xl font-bold">Créer une nouvelle page</h3>
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
                    @input="handleSlugInput"
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
                        color="primary"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Création...</span>
                        <span v-else>Créer la page</span>
                    </Btn>
                </div>
            </form>
    </Modal>
</template>

<style scoped lang="scss">
// Styles spécifiques si nécessaire
</style>

