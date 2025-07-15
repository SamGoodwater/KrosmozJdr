<script setup>
/**
 * @description
 * SectionList Organism. Affiche la liste des sections avec actions d'édition.
 * - Affichage d'un tableau des sections (titre, page associée)
 * - Action d'édition accessible pour chaque section
 * - Bouton de création de section si autorisé
 * - Responsive design
 *
 * @prop {Array} sections - Tableau des sections à afficher
 * @prop {Boolean} canCreate - Autorise la création de section
 */
import { Link } from '@inertiajs/vue3'
import Btn from '@/Pages/Atoms/action/Btn.vue'
import Container from '@/Pages/Atoms/data-display/Container.vue'
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue'

const props = defineProps({
    sections: {
        type: Array,
        required: true
    },
    canCreate: {
        type: Boolean,
        default: false
    }
})
</script>

<template>
    <Container class="max-w-4xl mx-auto p-4 md:p-8 bg-base-100 rounded-lg shadow-md">
        <div class="space-y-6">
            <div v-if="canCreate" class="flex justify-end">
                <Link :href="route('sections.create')">
                    <Tooltip content="Ajouter une nouvelle section" placement="top">
                        <Btn label="Créer une section" aria-label="Créer une section" />
                    </Tooltip>
                </Link>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Page associée</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="section in sections" :key="section.uniqid">
                            <td>{{ section.title }}</td>
                            <td>{{ section.page?.name }}</td>
                            <td>
                                <Link :href="route('sections.edit', { section: section.uniqid })">
                                    <Tooltip content="Modifier cette section" placement="top">
                                        <Btn label="Éditer" size="sm" aria-label="Éditer la section" />
                                    </Tooltip>
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </Container>
</template>
