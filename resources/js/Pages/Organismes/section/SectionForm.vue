<script setup>
/**
 * @description
 * SectionForm Organism. Formulaire de création et d'édition de section.
 * - Création et édition d'une section (titre, contenu, page associée, image)
 * - Validation des champs et gestion des erreurs
 * - Feedback utilisateur (succès/erreur)
 * - Responsive design
 *
 * @prop {Object} section - Objet section à éditer (optionnel, défaut : section vide)
 * @prop {Boolean} isUpdating - Mode édition ou création
 * @prop {Array} pages - Liste des pages disponibles pour l'association
 */
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import InputField from '@/Pages/Molecules/data-input/InputField.vue'
import Textarea from '@/Pages/Atoms/data-input/TextareaCore.vue'
import Select from '@/Pages/Atoms/data-input/SelectCore.vue'
import FileInput from '@/Pages/Molecules/data-input/FileInputField.vue'
import Btn from '@/Pages/Atoms/action/Btn.vue'
import Container from '@/Pages/Atoms/data-display/Container.vue'
import Alert from '@/Pages/Atoms/feedback/Alert.vue'
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue'

const props = defineProps({
    section: {
        type: Object,
        default: () => ({})
    },
    isUpdating: {
        type: Boolean,
        default: false
    },
    pages: {
        type: Array,
        default: () => []
    }
})

const form = useForm({
    title: props.section.title || '',
    content: props.section.content || '',
    page_id: props.section.page_id || null,
    image: null
})

const feedback = ref('')

const submit = () => {
    if (props.isUpdating) {
        form.put(route('sections.update', { section: props.section.uniqid }), {
            onSuccess: () => { feedback.value = 'Section mise à jour avec succès.'; },
            onError: () => { feedback.value = 'Erreur lors de la mise à jour.'; }
        })
    } else {
        form.post(route('sections.store'), {
            onSuccess: () => { feedback.value = 'Section créée avec succès.'; },
            onError: () => { feedback.value = 'Erreur lors de la création.'; }
        })
    }
}
</script>

<template>
    <Container class="max-w-2xl mx-auto p-4 md:p-8 bg-base-100 rounded-lg shadow-md">
        <form @submit.prevent="submit" class="space-y-6">
            <Tooltip content="Titre de la section" placement="top">
                <InputField v-model="form.title" label="Titre" :errorMessage="form.errors.title"
                    aria-label="Titre de la section" class="w-full" />
            </Tooltip>

            <Tooltip content="Contenu de la section" placement="top">
                <Textarea v-model="form.content" label="Contenu" :errorMessage="form.errors.content"
                    aria-label="Contenu de la section" class="w-full" />
            </Tooltip>

            <Tooltip content="Sélectionnez la page associée" placement="top">
                <Select v-model="form.page_id" label="Page associée"
                    :options="pages.map(p => ({ value: p.uniqid, label: p.name }))" :errorMessage="form.errors.page_id"
                    aria-label="Page associée" class="w-full" />
            </Tooltip>

            <Tooltip content="Image de la section (optionnelle)" placement="top">
                <FileInput v-model="form.image" label="Image" :error="form.errors.image" aria-label="Image de la section"
                    class="w-full" />
            </Tooltip>

            <Tooltip :content="isUpdating ? 'Mettre à jour la section' : 'Créer la section'" placement="top">
                <Btn type="submit" :disabled="form.processing"
                    :aria-label="isUpdating ? 'Mettre à jour la section' : 'Créer la section'">
                    {{ isUpdating ? 'Mettre à jour' : 'Créer' }}
                </Btn>
            </Tooltip>

            <Alert v-if="feedback" color="success" variant="soft" class="mt-4">
                {{ feedback }}
            </Alert>
        </form>
    </Container>
</template>
