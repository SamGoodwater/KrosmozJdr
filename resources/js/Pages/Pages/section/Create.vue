<script setup>
/**
 * Section Create Component
 * 
 * @description
 * Page de création d'une section dynamique.
 * - Formulaire complet pour créer une nouvelle section
 * - Support de différents types de sections
 * - Validation des champs
 * - Gestion des erreurs
 * 
 * @props {Array} pages - Liste des pages disponibles
 */
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import TextareaField from '@/Pages/Molecules/data-input/TextareaField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import ToggleField from '@/Pages/Molecules/data-input/ToggleField.vue';
import RichTextEditorField from '@/Pages/Molecules/data-input/RichTextEditorField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Alert from '@/Pages/Atoms/feedback/Alert.vue';
import { getSectionTypeOptions } from '@/Utils/enums/SectionType';
import { getPageStateOptions } from '@/Utils/enums/PageState';
import { getVisibilityOptions } from '@/Utils/enums/Visibility';

const { setPageTitle } = usePageTitle();
setPageTitle('Créer une section');

const props = defineProps({
    pages: {
        type: Array,
        default: () => []
    }
});

// Options pour les selects
const sectionTypeOptions = computed(() => getSectionTypeOptions());
const stateOptions = computed(() => getPageStateOptions());
const visibilityOptions = computed(() => getVisibilityOptions());

const pageOptions = computed(() => {
    return props.pages.map(page => ({
        value: page.id,
        label: `${page.title} (${page.slug})`
    }));
});

// Formulaire
const form = useForm({
    page_id: null,
    order: 0,
    type: 'text',
    params: {
        content: '',
        align: 'left',
        size: 'md'
    },
    is_visible: 'guest',
    state: 'draft'
});

// Réinitialiser les params quand le type change
watch(() => form.type, (newType) => {
    form.params = getDefaultParamsForType(newType);
});

/**
 * Retourne les params par défaut selon le type
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

// Validation computed
const pageIdValidation = computed(() => {
    if (!form.errors.page_id) return null;
    return {
        state: 'error',
        message: form.errors.page_id,
        showNotification: false
    };
});

const typeValidation = computed(() => {
    if (!form.errors.type) return null;
    return {
        state: 'error',
        message: form.errors.type,
        showNotification: false
    };
});

const paramsValidation = computed(() => {
    if (!form.errors.params) return null;
    const errors = Object.values(form.errors.params).flat();
    if (errors.length === 0) return null;
    return {
        state: 'error',
        message: errors.join(', '),
        showNotification: false
    };
});

const contentValidation = computed(() => {
    const key = 'params.content';
    if (!form.errors[key]) return null;
    return {
        state: 'error',
        message: form.errors[key],
        showNotification: false
    };
});

const submit = () => {
    form.post(route('sections.store'), {
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
    <Head title="Créer une section" />
    
    <Container class="max-w-4xl mx-auto p-4 md:p-8">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h1 class="card-title text-3xl mb-6">Créer une nouvelle section</h1>
                
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
                    <!-- Page -->
                    <SelectField
                        v-model="form.page_id"
                        label="Page"
                        :options="pageOptions"
                        required
                        :validation="pageIdValidation"
                        helper="Page à laquelle cette section appartient"
                    />
                    
                    <!-- Type -->
                    <SelectField
                        v-model="form.type"
                        label="Type de section"
                        :options="sectionTypeOptions"
                        required
                        :validation="typeValidation"
                        helper="Type de contenu de la section"
                    />
                    
                    <!-- Ordre -->
                    <InputField
                        v-model="form.order"
                        label="Ordre"
                        type="number"
                        min="0"
                        required
                        helper="Ordre d'affichage dans la page (0 = premier)"
                    />
                    
                    <!-- Paramètres selon le type -->
                    <div v-if="form.type === 'text'" class="space-y-4 p-4 bg-base-200 rounded-lg">
                        <h3 class="font-bold text-lg mb-4">Paramètres de la section texte</h3>
                        
                        <RichTextEditorField
                            v-model="form.params.content"
                            label="Contenu"
                            required
                            :validation="contentValidation"
                            helper="Texte riche de la section (affiché tel quel sur la page)"
                        />
                        
                        <SelectField
                            v-model="form.params.align"
                            label="Alignement"
                            :options="[
                                { value: 'left', label: 'Gauche' },
                                { value: 'center', label: 'Centre' },
                                { value: 'right', label: 'Droite' }
                            ]"
                        />
                        
                        <SelectField
                            v-model="form.params.size"
                            label="Taille"
                            :options="[
                                { value: 'sm', label: 'Petit' },
                                { value: 'md', label: 'Moyen' },
                                { value: 'lg', label: 'Grand' },
                                { value: 'xl', label: 'Très grand' }
                            ]"
                        />
                    </div>
                    
                    <div v-else class="alert alert-info">
                        <i class="fa-solid fa-info-circle"></i>
                        <div>
                            <p class="text-sm">
                                Les paramètres pour le type "{{ form.type }}" seront disponibles prochainement.
                                Pour l'instant, veuillez utiliser le type "text".
                            </p>
                        </div>
                    </div>
                    
                    <!-- Visibilité -->
                    <SelectField
                        v-model="form.is_visible"
                        label="Visibilité"
                        :options="visibilityOptions"
                        required
                        helper="Qui peut voir cette section ?"
                    />
                    
                    <!-- État -->
                    <SelectField
                        v-model="form.state"
                        label="État"
                        :options="stateOptions"
                        required
                        helper="État de publication de la section"
                    />
                    
                    <!-- Actions -->
                    <div class="card-actions justify-end mt-8">
                        <Btn
                            type="button"
                            variant="ghost"
                            @click="$inertia.visit(route('pages.index'))"
                        >
                            Annuler
                        </Btn>
                        <Btn
                            type="submit"
                            variant="primary"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Création...</span>
                            <span v-else>Créer la section</span>
                        </Btn>
                    </div>
                </form>
            </div>
        </div>
    </Container>
</template>

<style scoped lang="scss">
// Styles spécifiques si nécessaire
</style>
