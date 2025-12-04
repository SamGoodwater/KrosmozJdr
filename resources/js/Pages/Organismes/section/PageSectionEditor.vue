<script setup>
/**
 * PageSectionEditor Organism
 *
 * @description
 * Éditeur de sections pour une page donnée, avec drag & drop.
 * - Affiche la liste des sections de la page
 * - Permet de réordonner les sections par drag & drop
 * - Permet d'éditer chaque section via la route d'édition
 * - Sauvegarde l'ordre via une requête Inertia vers sections.reorder
 *
 * @prop {Array} sections - Liste des sections de la page (id, title, order, type, etc.)
 * @prop {Number} pageId - Identifiant de la page
 * @prop {Boolean} canEdit - Autorise l'édition/réorganisation
 */
import { ref, computed, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import Container from '@/Pages/Atoms/data-display/Container.vue'
import Btn from '@/Pages/Atoms/action/Btn.vue'
import Icon from '@/Pages/Atoms/data-display/Icon.vue'
import Alert from '@/Pages/Atoms/feedback/Alert.vue'
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue'

const props = defineProps({
    sections: {
        type: Array,
        default: () => []
    },
    pageId: {
        type: [Number, String],
        required: true
    },
    canEdit: {
        type: Boolean,
        default: true
    }
})

// Copie locale modifiable des sections, triée par ordre
const localSections = ref(
    [...props.sections].sort((a, b) => (a.order || 0) - (b.order || 0))
)

watch(
    () => props.sections,
    (newSections) => {
        localSections.value = [...newSections].sort(
            (a, b) => (a.order || 0) - (b.order || 0)
        )
    },
    { deep: true }
)

const draggingIndex = ref(null)
const saving = ref(false)
const saveError = ref('')
const saveSuccess = ref('')

const hasChanges = computed(() => {
    if (localSections.value.length !== props.sections.length) return true
    return localSections.value.some((section, index) => {
        const original = props.sections.find((s) => s.id === section.id)
        return !original || (original.order || 0) !== index
    })
})

function onDragStart(index) {
    if (!props.canEdit) return
    draggingIndex.value = index
}

function onDragOver(event, index) {
    if (!props.canEdit) return
    event.preventDefault()
    if (draggingIndex.value === null || draggingIndex.value === index) return

    const items = [...localSections.value]
    const draggedItem = items[draggingIndex.value]
    items.splice(draggingIndex.value, 1)
    items.splice(index, 0, draggedItem)
    localSections.value = items
    draggingIndex.value = index
}

function onDragEnd() {
    draggingIndex.value = null
}

function sectionTypeLabel(section) {
    return section.type || 'text'
}

function saveOrder() {
    if (!props.canEdit || !hasChanges.value) return

    saving.value = true
    saveError.value = ''
    saveSuccess.value = ''

    const payload = {
        sections: localSections.value.map((section, index) => ({
            id: section.id,
            order: index
        }))
    }

    router.patch(route('sections.reorder'), payload, {
        preserveScroll: true,
        onSuccess: () => {
            saving.value = false
            saveSuccess.value = 'Ordre des sections enregistré avec succès.'
        },
        onError: () => {
            saving.value = false
            saveError.value =
                "Une erreur est survenue lors de l'enregistrement de l'ordre des sections."
        }
    })
}
</script>

<template>
    <Container class="mt-10 p-4 md:p-6 bg-base-200/60 rounded-xl shadow-inner">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-semibold">
                Sections de la page
            </h2>
            <div class="flex gap-2">
                <Link
                    :href="route('sections.create') + `?page_id=${pageId}`"
                    v-if="canEdit"
                >
                    <Tooltip content="Ajouter une nouvelle section" placement="top">
                        <Btn
                            size="sm"
                            variant="primary"
                            aria-label="Ajouter une section"
                        >
                            <i class="fa-solid fa-plus mr-2"></i>
                            Ajouter une section
                        </Btn>
                    </Tooltip>
                </Link>
            </div>
        </div>

        <p
            v-if="!localSections.length"
            class="text-sm text-base-content/60 mb-4"
        >
            Aucune section pour le moment. Ajoutez une section pour commencer à
            construire la page.
        </p>

        <div v-else class="space-y-2">
            <div
                v-for="(section, index) in localSections"
                :key="section.id"
                class="flex items-center gap-3 p-3 rounded-lg bg-base-100 shadow-sm border border-base-300/60 hover:border-primary/70 transition-colors"
                draggable="true"
                @dragstart="onDragStart(index)"
                @dragover="onDragOver($event, index)"
                @dragend="onDragEnd"
            >
                <div
                    class="cursor-grab active:cursor-grabbing text-base-content/60 hover:text-primary transition-colors"
                    :title="canEdit ? 'Glisser pour réordonner' : ''"
                >
                    <i class="fa-solid fa-grip-vertical"></i>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-mono text-base-content/60">
                            #{{ index }}
                        </span>
                        <h3 class="font-semibold truncate">
                            {{ section.title || `Section ${index + 1}` }}
                        </h3>
                    </div>
                    <div class="text-xs text-base-content/60 mt-1 flex flex-wrap gap-2">
                        <span class="badge badge-xs badge-outline">
                            Type :
                            <span class="ml-1 font-mono">
                                {{ sectionTypeLabel(section) }}
                            </span>
                        </span>
                        <span class="badge badge-xs badge-outline">
                            ID :
                            <span class="ml-1 font-mono">
                                {{ section.id }}
                            </span>
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <Link
                        :href="route('sections.edit', { section: section.id })"
                        v-if="canEdit"
                    >
                        <Tooltip content="Modifier la section" placement="top">
                            <Btn
                                size="sm"
                                variant="ghost"
                                aria-label="Modifier la section"
                            >
                                <i class="fa-solid fa-pen"></i>
                            </Btn>
                        </Tooltip>
                    </Link>
                </div>
            </div>
        </div>

        <div v-if="localSections.length" class="mt-4 flex items-center gap-3">
            <Btn
                type="button"
                variant="primary"
                size="sm"
                :disabled="saving || !hasChanges"
                @click="saveOrder"
            >
                <span v-if="saving">
                    <span class="loading loading-spinner loading-xs mr-2" />
                    Enregistrement...
                </span>
                <span v-else>Enregistrer l'ordre des sections</span>
            </Btn>

            <span
                v-if="hasChanges && !saving"
                class="text-xs text-warning/80"
            >
                Des modifications d'ordre ne sont pas encore enregistrées.
            </span>
        </div>

        <div class="mt-3 space-y-2">
            <Alert
                v-if="saveSuccess"
                type="success"
                variant="soft"
                class="text-sm"
            >
                {{ saveSuccess }}
            </Alert>
            <Alert
                v-if="saveError"
                type="error"
                variant="soft"
                class="text-sm"
            >
                {{ saveError }}
            </Alert>
        </div>
    </Container>
</template>

<style scoped lang="scss">
</style>


