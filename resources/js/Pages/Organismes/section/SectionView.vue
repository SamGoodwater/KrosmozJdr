<script setup>
/**
 * @description
 * SectionView Organism. Affiche le détail d'une section avec actions d'édition et de suppression.
 * - Affichage du titre et du contenu de la section
 * - Actions d'édition et de suppression (avec modale de confirmation)
 * - Feedback utilisateur (succès/erreur)
 * - Responsive design
 *
 * @prop {Object} section - Objet section à afficher
 * @prop {Boolean} canEdit - Autorise l'édition
 * @prop {Boolean} canDelete - Autorise la suppression
 */
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import Alert from '@/Pages/Atoms/feedback/Alert.vue';

const props = defineProps({
    section: {
        type: Object,
        required: true
    },
    canEdit: {
        type: Boolean,
        default: false
    },
    canDelete: {
        type: Boolean,
        default: false
    }
});

const showDeleteModal = ref(false);
const feedback = ref({ type: '', message: '' });

function openDeleteModal() {
    showDeleteModal.value = true;
}
function closeDeleteModal() {
    showDeleteModal.value = false;
}
function deleteSection() {
    router.delete(route('sections.destroy', { section: props.section.uniqid }), {
        onSuccess: () => {
            feedback.value = { type: 'success', message: 'Section supprimée avec succès.' };
            closeDeleteModal();
            // Redirection possible ici si besoin
        },
        onError: (errors) => {
            feedback.value = { type: 'error', message: 'Erreur lors de la suppression.' };
        }
    });
}
</script>

<template>
    <Container class="max-w-2xl mx-auto p-4 md:p-8 bg-base-100 rounded-lg shadow-md">
        <div class="space-y-6">
            <h1 class="text-2xl font-bold">{{ section.title }}</h1>
            <div v-html="section.content"></div>

            <div v-if="canEdit || canDelete" class="flex gap-2">
                <Link v-if="canEdit" :href="route('sections.edit', { section: section.uniqid })">
                <Btn label="Éditer" tooltip="Modifier cette section" aria-label="Éditer la section" />
                </Link>
                <Btn v-if="canDelete" label="Supprimer" theme="error" @click="openDeleteModal"
                    tooltip="Supprimer cette section" aria-label="Supprimer la section" />
            </div>

            <Alert v-if="feedback.message" :color="feedback.type === 'success' ? 'success' : 'error'" variant="soft"
                class="mt-4">
                {{ feedback.message }}
            </Alert>

            <Modal :show="showDeleteModal" @close="closeDeleteModal">
                <Container class="p-6 space-y-6 bg-base-100 rounded-lg shadow-lg">
                    <h2 class="text-lg font-medium text-error-100">Confirmer la suppression</h2>
                    <p class="text-sm text-error-200">
                        Êtes-vous sûr de vouloir supprimer cette section ? Cette action est irréversible.
                    </p>
                    <div class="flex justify-end gap-4 mt-6">
                        <Btn theme="secondary" @click="closeDeleteModal" label="Annuler" tooltip="Annuler" />
                        <Btn theme="error" @click="deleteSection" label="Supprimer"
                            tooltip="Confirmer la suppression" />
                    </div>
                </Container>
            </Modal>
        </div>
    </Container>
</template>
