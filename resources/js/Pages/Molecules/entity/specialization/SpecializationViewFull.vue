<script setup>
/**
 * SpecializationViewFull — Vue Full pour Specialization
 *
 * @description
 * Mise en page dédiée spécialisation : texte à gauche (nom, accroche, description),
 * visuel à droite. L’état reste dans les actions. Le bandeau « Rédaction » est rendu
 * en fin de page dans `Show.vue` (après les sections CMS).
 *
 * @props {Specialization} specialization - Instance du modèle Specialization
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Image from '@/Pages/Atoms/data-display/Image.vue';
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import CreatureTraitBadges from "@/Pages/Molecules/entity/creature-trait/CreatureTraitBadges.vue";

const props = defineProps({
    specialization: {
        type: Object,
        required: true
    },
    showActions: {
        type: Boolean,
        default: true
    },

    inModal: {
        type: Boolean,
        default: false,
    },
    titleTag: {
        type: String,
        default: 'h2',
        validator: (v) => ['h1', 'h2', 'h3'].includes(v),
    },
    tableMeta: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'quick-edit', 'delete', 'action']);

const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf('specialization');

const linkedCreatureTraits = computed(() => {
    const raw = props.specialization?._data?.creatureTraits ?? props.specialization?.creatureTraits;
    return Array.isArray(raw) ? raw : [];
});

const hasLinkedCreatureTraits = computed(() => linkedCreatureTraits.value.length > 0);
const linkedCapabilities = computed(() => props.specialization?._data?.capabilities ?? props.specialization?.capabilities ?? []);
const linkedSpells = computed(() => props.specialization?._data?.spells ?? props.specialization?.spells ?? []);
const linkedItems = computed(() => props.specialization?._data?.items ?? props.specialization?.items ?? []);
const linkedResources = computed(() => props.specialization?._data?.resources ?? props.specialization?.resources ?? []);
const linkedConsumables = computed(() => props.specialization?._data?.consumables ?? props.specialization?.consumables ?? []);
const linkedSections = computed(() => props.specialization?._data?.sections ?? props.specialization?.sections ?? []);

const shortPitch = computed(() => {
    const raw =
        props.specialization?.shortDescription
        ?? props.specialization?._data?.short_description
        ?? props.specialization?.short_description;
    const s = String(raw ?? '').trim();
    return s.length ? s : '';
});

const longDescription = computed(() => {
    const raw = props.specialization?.description ?? props.specialization?._data?.description;
    const s = String(raw ?? '').trim();
    return s.length ? s : '';
});

const heroImageSrc = computed(() => {
    const raw = props.specialization?.image ?? props.specialization?._data?.image;
    return String(raw ?? '').trim();
});

const getCell = (fieldKey) => {
    return props.specialization.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
    });
};

const handleAction = async (actionKey) => {
    const specializationId = props.specialization.id;
    if (!specializationId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.specializations.show', { specialization: specializationId }));
            emit('view', props.specialization);
            break;
        case 'edit':
            router.visit(route('entities.specializations.edit', { specialization: specializationId }));
            emit('edit', props.specialization);
            break;
        case 'quick-edit':
            emit('quick-edit', props.specialization);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('specialization');
            const url = resolveEntityRouteUrl('specialization', 'show', specializationId, cfg);
            if (url) {
                await copyToClipboard(url, "Lien de la spécialisation copié !");
            }
            emit('copy-link', props.specialization);
            break;
        }
        case 'download-pdf':
            await downloadPdf(specializationId);
            emit('download-pdf', props.specialization);
            break;
        case 'refresh':
            router.reload({ only: ['specializations'] });
            emit('refresh', props.specialization);
            break;
        case 'delete':
            emit('delete', props.specialization);
            break;
    }
};
</script>

<template>
    <div class="space-y-6">
        <!-- Bloc héros : contenu à gauche, visuel à droite (desktop) -->
        <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between md:gap-8">
            <div class="min-w-0 flex-1 space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <h2 class="text-2xl font-bold text-primary-100 wrap-break-word md:text-3xl">
                        <CellRenderer
                            :cell="getCell('name')"
                            ui-color="primary"
                        />
                    </h2>
                    <div v-if="showActions" class="shrink-0 pt-0.5">
                        <EntityActions
                            entity-type="specializations"
                            :entity="specialization"
                            format="buttons"
                            display="icon-only"
                            size="sm"
                            color="primary"
                            :context="{ inPanel: false, inPage: true }"
                            @action="handleAction"
                        />
                    </div>
                </div>

                <p
                    v-if="shortPitch"
                    class="specialization-pitch text-lg font-semibold leading-snug text-primary-50 wrap-break-word md:text-xl"
                >
                    {{ shortPitch }}
                </p>

                <div
                    v-if="longDescription"
                    class="specialization-body text-base leading-relaxed text-primary-200/95 wrap-break-word whitespace-pre-wrap"
                >
                    {{ longDescription }}
                </div>
            </div>

            <aside
                v-if="heroImageSrc"
                class="specialization-hero-aside mx-auto w-full max-w-[16rem] shrink-0 md:mx-0 md:max-w-[min(40%,20rem)]"
            >
                <Image
                    :src="heroImageSrc"
                    :alt="specialization.name || 'Spécialisation'"
                    class="specialization-hero-image entity-radius-box h-auto w-full object-cover shadow-lg"
                />
            </aside>
        </div>

        <div
            v-if="hasLinkedCreatureTraits"
            class="rounded-box border border-base-300 bg-base-100/40 p-4 space-y-2"
            role="region"
            aria-label="Traits de spécialisation"
        >
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Traits</h3>
            <CreatureTraitBadges :traits="linkedCreatureTraits" show-level size="sm" />
        </div>

    </div>
</template>

<style scoped>
.entity-radius-box {
    border-radius: var(--radius-box, 0.1rem);
}

.specialization-pitch {
    border-left: 3px solid color-mix(in oklch, hsl(var(--p)) 65%, transparent);
    padding-left: 0.85rem;
    margin-left: 0.1rem;
}

.specialization-hero-image {
    aspect-ratio: 1 / 1;
}

@media (min-width: 768px) {
    .specialization-hero-aside {
        position: sticky;
        top: 1rem;
    }
}
</style>
