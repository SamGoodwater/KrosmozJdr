<script setup>
/**
 * CapabilityViewCompact — Vue Compact pour Capability
 * 
 * @description
 * Vue réduite d'une capacité avec informations essentielles.
 * Utilisée dans les modals compacts.
 * 
 * @props {Capability} capability - Instance du modèle Capability
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Image from '@/Pages/Atoms/data-display/Image.vue';
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import MonsterViewText from "@/Pages/Molecules/entity/monster/MonsterViewText.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getCapabilityFieldDescriptors } from "@/Entities/capability/capability-descriptors";
import { sanitizeHtml } from "@/Utils/security/sanitizeHtml";
import { PROPERTY_DISPLAY_MODES } from "@/Utils/Entity/Constants";
const props = defineProps({
    capability: {
        type: Object,
        required: true
    },
    showActions: {
        type: Boolean,
        default: true
    },
    tableMeta: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'quick-edit', 'delete', 'action']);

const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf('capability');
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('capabilities', 'viewAny'),
        createAny: permissions.can('capabilities', 'createAny'),
        updateAny: permissions.can('capabilities', 'updateAny'),
        deleteAny: permissions.can('capabilities', 'deleteAny'),
        manageAny: permissions.can('capabilities', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getCapabilityFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf ?? desc?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[CapabilityViewCompact] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

// Champs à afficher dans la vue compacte (aligné densité sort / fiche utile)
const compactFields = computed(() =>
    [
        "is_passive",
        "level",
        "pa",
        "po",
        "po_editable",
        "element",
        "casting_time",
        "duration",
        "time_before_use_again",
        "is_magic",
        "ritual_available",
        "powerful",
    ].filter(canShowField),
);

const getCell = (fieldKey) => {
    return props.capability.toCell(fieldKey, {
        size: 'md',
        context: 'compact',
    });
};

const effectHtmlSafe = computed(() => {
    const raw = props.capability?.effect ?? props.capability?._data?.effect;
    if (raw === null || raw === undefined || String(raw).trim() === '') return '';
    return sanitizeHtml(String(raw));
});

const invocationMonsters = computed(() => {
    const raw = props.capability?.creatures ?? props.capability?._data?.creatures ?? [];
    return Array.isArray(raw) ? raw.filter((m) => m?.id != null || m?.name || m?.creature_name) : [];
});

const handleAction = async (actionKey) => {
    const capabilityId = props.capability.id;
    if (!capabilityId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.capabilities.show', { capability: capabilityId }));
            emit('view', props.capability);
            break;
        case 'edit':
            router.visit(route('entities.capabilities.edit', { capability: capabilityId }));
            emit('edit', props.capability);
            break;
        case 'quick-edit':
            emit('quick-edit', props.capability);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('capability');
            const url = resolveEntityRouteUrl('capability', 'show', capabilityId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de la capacité copié !");
            }
            emit('copy-link', props.capability);
            break;
        }
        case 'download-pdf':
            await downloadPdf(capabilityId);
            emit('download-pdf', props.capability);
            break;
        case 'refresh':
            router.reload({ only: ['capabilities'] });
            emit('refresh', props.capability);
            break;
        case 'delete':
            emit('delete', props.capability);
            break;
    }
};
</script>

<template>
    <div class="space-y-3">
        <!-- En-tête compact -->
        <div class="flex items-center justify-between gap-2">
            <div class="flex gap-2 items-center flex-1 min-w-0">
                <Image
                    v-if="capability.image"
                    :src="capability.image"
                    :alt="capability.name || 'Capability'"
                    class="w-10 h-10 entity-radius-field object-cover shrink-0"
                />
                <h3 class="text-lg font-semibold text-primary-100 truncate">
                    <CellRenderer
                        :cell="getCell('name')"
                        ui-color="primary"
                    />
                </h3>
            </div>
            
            <div v-if="showActions" class="shrink-0">
                <EntityActions
                    entity-type="capabilities"
                    :entity="capability"
                    format="buttons"
                    display="icon-only"
                    size="sm"
                    color="primary"
                    :context="{ inPanel: false, inPage: true }"
                    @action="handleAction"
                />
            </div>
        </div>

        <!-- Propriétés principales : toutes via CharacteristicProperty / EntityPropertyDisplay. -->
        <div class="grid grid-cols-1 gap-2 text-sm sm:grid-cols-2">
            <EntityPropertyDisplay
                v-for="fieldKey in compactFields"
                :key="fieldKey"
                :field-key="fieldKey"
                :entity="capability"
                entity-type="capability"
                :display-mode="PROPERTY_DISPLAY_MODES.compact"
                :descriptors="descriptors"
                :table-meta="tableMeta"
                :variant="fieldKey === 'is_passive' ? 'icon' : 'inline'"
                :hide-characteristic-icon="fieldKey === 'po_editable'"
                size="sm"
                class="min-w-0 rounded-box border border-base-300/60 bg-glass-2xl px-2.5 py-1.5"
                style="--bg-color: var(--color-base-200)"
            />
        </div>

        <div
            v-if="invocationMonsters.length > 0"
            class="flex flex-wrap items-center gap-2 rounded-box border border-base-300/60 bg-glass-2xl px-2.5 py-1.5 text-sm"
            style="--bg-color: var(--color-base-200)"
        >
            <span class="text-primary-300 font-semibold">Invocation :</span>
            <MonsterViewText
                v-for="monster in invocationMonsters"
                :key="monster.id ?? monster.name"
                :monster="monster"
                :table-meta="tableMeta"
            />
        </div>

        <section class="space-y-2">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Effets</h3>
            <!-- eslint-disable vue/no-v-html -- contenu issu de l’éditeur riche, nettoyé avant rendu -->
            <article
                v-if="effectHtmlSafe"
                class="prose prose-sm prose-invert max-w-none text-sm text-primary-200 capability-compact-effect-prose"
                v-html="effectHtmlSafe"
            />
            <!-- eslint-enable vue/no-v-html -->
            <p v-else class="text-sm text-primary-400 italic">Aucun effet décrit (texte riche).</p>
        </section>
    </div>
</template>

<style scoped>
.entity-radius-field {
    border-radius: var(--radius-field, 0.1rem);
}
</style>
