<script setup>
/**
 * Page Edit Component
 * 
 * @description
 * Page de modification d'une page dynamique.
 * - Formulaire complet pour modifier une page existante
 * - Validation des champs
 * - Gestion des erreurs
 * 
 * @props {Object} page - Données de la page à modifier
 * @props {Array} pages - Liste des pages disponibles (pour parent_id)
 */
import { Head, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import ToggleField from '@/Pages/Molecules/data-input/ToggleField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Alert from '@/Pages/Atoms/feedback/Alert.vue';
import { getPageStateOptions } from '@/Utils/enums/PageState';
import { getVisibilityOptions } from '@/Utils/enums/Visibility';
import PageSectionEditor from '@/Pages/Organismes/section/PageSectionEditor.vue';

const props = defineProps({
    page: {
        type: Object,
        required: true
    },
    pages: {
        type: Array,
        default: () => []
    }
});

const { setPageTitle } = usePageTitle();

// Extraire les données de la page (gérer la structure Resource qui peut être dans data)
// Inertia wrappe parfois les ressources dans un objet { data: {...} }
const pageData = computed(() => {
    // Si props.page a une propriété data, utiliser data, sinon utiliser props.page directement
    return props.page?.data || props.page || {};
});

setPageTitle(`Modifier : ${pageData.value?.title || 'Page'}`);

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

// Formulaire - initialiser avec les valeurs de la page
const form = useForm({
    title: pageData.value?.title || '',
    slug: pageData.value?.slug || '',
    is_visible: pageData.value?.is_visible || 'guest',
    can_edit_role: pageData.value?.can_edit_role || 'admin',
    in_menu: pageData.value?.in_menu ?? true,
    state: pageData.value?.state || 'draft',
    parent_id: pageData.value?.parent_id || null,
    menu_order: pageData.value?.menu_order || 0
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

const submit = () => {
    const pageId = pageData.value?.id;
    if (!pageId) {
        console.error('Page ID is missing', pageData.value);
        return;
    }
    // Passer l'objet page ou son ID - Ziggy attend le paramètre nommé 'page'
    form.patch(route('pages.update', { page: pageId }), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirection gérée par le contrôleur
        },
        onError: () => {
            // Les erreurs sont gérées automatiquement par Inertia
        }
    });
};
</script>

<template>
    <Head :title="`Modifier : ${pageData.title || 'Page'}`" />
    
    <Container class="max-w-4xl mx-auto p-4 md:p-8 space-y-8">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h1 class="card-title text-3xl mb-6">Modifier la page</h1>
                
                <!-- Message d'erreur global -->
                <Alert
                    v-if="Object.keys(form.errors).length > 0"
                    type="error"
                    class="mb-6"
                >
                    <ul class="list-disc list-inside">
                        <li v-for="(error, key) in form.errors" :key="key">
                            {{ error }}
                        </li>
                    </ul>
                </Alert>
                
                <form @submit.prevent="submit" class="space-y-6">
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
                    <div class="card-actions justify-end mt-8">
                        <Btn
                            type="button"
                            variant="ghost"
                            @click="$inertia.visit(route('pages.show', pageData.slug))"
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
            </div>
        </div>

        <!-- Éditeur de sections avec drag & drop -->
        <PageSectionEditor
            v-if="pageData.sections && Array.isArray(pageData.sections)"
            :sections="pageData.sections"
            :page-id="pageData.id"
            :can-edit="pageData.can?.update ?? true"
        />
    </Container>
</template>

<style scoped lang="scss">
// Styles spécifiques si nécessaire
</style>
