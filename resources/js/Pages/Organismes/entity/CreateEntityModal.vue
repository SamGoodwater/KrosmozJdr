<script setup>
/**
 * CreateEntityModal Organism
 * 
 * @description
 * Modal générique pour créer une nouvelle entité.
 * Utilise EntityEditForm avec isUpdating: false pour la création.
 * 
 * @props {Boolean} open - Contrôle l'ouverture du modal
 * @props {String} entityType - Type d'entité (item, spell, monster, etc.)
 * @props {Object} fieldsConfig - Configuration des champs à afficher (optionnel)
 * @props {Object} defaultEntity - Entité par défaut avec valeurs initiales (optionnel)
 * @emit close - Événement émis lors de la fermeture
 * @emit created - Événement émis après création réussie
 */
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import EntityEditForm from './EntityEditForm.vue';
import { getEntityConfig as getRegistryEntityConfig, normalizeEntityType } from '@/Entities/entity-registry';
import { createDefaultEntityFromDescriptors, createFieldsConfigFromDescriptors } from '@/Utils/entity/descriptor-form';

const props = defineProps({
    open: {
        type: Boolean,
        default: false
    },
    entityType: {
        type: String,
        required: true
    },
    fieldsConfig: {
        type: Object,
        default: () => ({})
    },
    defaultEntity: {
        type: Object,
        default: () => ({})
    },
    /**
     * Override optionnel des routes utilisées par EntityEditForm.
     * Utile pour des entités dont les routes ne suivent pas le pattern `entities.{plural}.{store|update}`.
     */
    routeNameBase: {
        type: String,
        default: null
    },
    routeParamKey: {
        type: String,
        default: null
    }
});

const emit = defineEmits(['close', 'created']);

const page = usePage();

const normalizedEntityType = computed(() => normalizeEntityType(props.entityType));
const registryEntityConfig = computed(() => getRegistryEntityConfig(props.entityType));

const descriptorContext = computed(() => {
    const capabilities = page.props?.auth?.user?.can || {};
    return {
        ...page.props,
        capabilities,
        meta: {
            ...page.props,
            capabilities,
        },
    };
});

const descriptorBackedFieldsConfig = computed(() => {
    const getDescriptors = registryEntityConfig.value?.getDescriptors;
    if (typeof getDescriptors !== 'function') return {};
    const descriptors = getDescriptors(descriptorContext.value) || {};
    return createFieldsConfigFromDescriptors(descriptors, descriptorContext.value);
});

const descriptorBackedDefaultEntity = computed(() => {
    const getDescriptors = registryEntityConfig.value?.getDescriptors;
    if (typeof getDescriptors !== 'function') return {};
    const descriptors = getDescriptors(descriptorContext.value) || {};
    return createDefaultEntityFromDescriptors(descriptors);
});

const createHiddenFieldKeys = new Set([
    'id',
    'slug',
    'auto_update',
    'dofus_version',
    'dofusdb_id',
    'dofusdb_type_id',
    'source',
    'source_url',
    'source_ref',
    'created_by',
    'updated_by',
    'created_at',
    'updated_at',
    'deleted_at',
]);

const mergedFieldsConfig = computed(() => {
    const custom = props.fieldsConfig || {};
    if (Object.keys(custom).length > 0) {
        return Object.fromEntries(
            Object.entries(custom).filter(([fieldKey]) => !createHiddenFieldKeys.has(fieldKey))
        );
    }
    const generated = descriptorBackedFieldsConfig.value || {};
    if (Object.keys(generated).length > 0) {
        return Object.fromEntries(
            Object.entries(generated).filter(([fieldKey]) => !createHiddenFieldKeys.has(fieldKey))
        );
    }
    return custom;
});

const hasProvidedFieldsConfig = computed(() => Object.keys(props.fieldsConfig || {}).length > 0);
const hasDescriptorFieldsConfig = computed(() => Object.keys(descriptorBackedFieldsConfig.value || {}).length > 0);

const fieldConfigSourceLabel = computed(() => {
    if (hasProvidedFieldsConfig.value) return 'Formulaire personnalisé';
    if (hasDescriptorFieldsConfig.value) return 'Formulaire optimisé';
    return 'Formulaire standard';
});

const showFieldConfigWarning = computed(() =>
    !hasProvidedFieldsConfig.value && !hasDescriptorFieldsConfig.value
);

const entityTypeForUi = computed(() => {
    const map = {
        resources: 'resource',
        'resource-types': 'resource',
        items: 'item',
        spells: 'spell',
        monsters: 'monster',
        npcs: 'npc',
        breeds: 'breed',
        consumables: 'consumable',
        campaigns: 'campaign',
        scenarios: 'scenario',
        attributes: 'attribute',
        panoplies: 'panoply',
        capabilities: 'capability',
        specializations: 'specialization',
        shops: 'shop',
    };
    return map[normalizedEntityType.value] || props.entityType;
});

const entityColorVar = computed(() => `var(--color-${entityTypeForUi.value}-700)`);

const modalBodyStyle = computed(() => ({
    backgroundColor: `color-mix(in srgb, ${entityColorVar.value} 5%, var(--color-base-100))`,
    borderColor: `color-mix(in srgb, ${entityColorVar.value} 28%, var(--color-base-300))`,
    boxShadow: `0 18px 40px -30px ${entityColorVar.value}`,
    '--create-entity-accent': entityColorVar.value,
}));

// Entité vide pour la création
const emptyEntity = computed(() => {
    return {
        id: null,
        ...descriptorBackedDefaultEntity.value,
        ...props.defaultEntity,
    };
});

// Nom de l'entité pour l'affichage
const entityTypeLabel = computed(() => {
    const labels = {
        item: 'objet',
        spell: 'sort',
        monster: 'monstre',
        npc: 'PNJ',
        breed: 'Classe',
        panoply: 'panoplie',
        campaign: 'campagne',
        scenario: 'scénario',
        creature: 'créature',
        resource: 'ressource',
        consumable: 'consommable',
        attribute: 'attribut',
        capability: 'capacité',
        specialization: 'spécialisation',
        shop: 'hotel de vente'
        ,resourceType: 'type de ressource'
    };
    return labels[props.entityType] || props.entityType;
});

// Gestion de la fermeture
const handleClose = () => {
    emit('close');
};

// Gestion de la soumission
const handleSubmit = () => {
    // Recharger la page pour afficher la nouvelle entité
    const reloadPropByNormalizedType = {
        resources: 'resources',
        'resource-types': 'resourceTypes',
        items: 'items',
        spells: 'spells',
        monsters: 'monsters',
        npcs: 'npcs',
        breeds: 'breeds',
        consumables: 'consumables',
        campaigns: 'campaigns',
        scenarios: 'scenarios',
        attributes: 'attributes',
        panoplies: 'panoplies',
        capabilities: 'capabilities',
        specializations: 'specializations',
        shops: 'shops',
    };
    const reloadProp = reloadPropByNormalizedType[normalizedEntityType.value] || `${props.entityType}s`;
    router.reload({
        only: [reloadProp],
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            emit('created');
            handleClose();
        }
    });
};

// Gestion de l'annulation
const handleCancel = () => {
    handleClose();
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
            <h3 class="text-xl font-bold text-primary-100">
                {{ String(entityTypeLabel || '').toUpperCase() }} <span class="text-primary-300">- création</span>
            </h3>
        </template>

        <div class="entity-create-theme rounded-(--radius-field) border p-3 space-y-3" :style="modalBodyStyle">
            <div
                class="rounded-(--radius-field) border px-3 py-2 text-xs text-primary-200 flex flex-wrap items-center gap-2"
                :style="{
                    backgroundColor: `color-mix(in srgb, ${entityColorVar} 4%, var(--color-base-100))`,
                    borderColor: `color-mix(in srgb, ${entityColorVar} 22%, var(--color-base-300))`
                }"
            >
                <span class="font-medium text-primary-100">{{ fieldConfigSourceLabel }}</span>
                <span class="opacity-70">•</span>
                <span>Champs adaptés automatiquement au type de contenu</span>
            </div>

            <div
                v-if="showFieldConfigWarning"
                class="rounded-(--radius-field) border border-warning/40 bg-warning/10 px-3 py-2 text-xs text-warning-content"
            >
                Certains champs avancés ne sont pas encore personnalisés pour ce type.
                Vous pouvez continuer, puis compléter les détails ensuite.
            </div>

            <div class="max-h-[70vh] overflow-y-auto pr-2">
                <EntityEditForm
                    :entity="emptyEntity"
                    :entity-type="entityType"
                    :fields-config="mergedFieldsConfig"
                    :route-name-base="routeNameBase"
                    :route-param-key="routeParamKey"
                    :is-updating="false"
                    @submit="handleSubmit"
                    @cancel="handleCancel"
                />
            </div>
        </div>

        <template #actions>
            <!-- Les actions sont gérées par EntityEditForm -->
        </template>
    </Modal>
</template>

<style scoped lang="scss">
.entity-create-theme {
    :deep(.btn.btn-primary) {
        border-color: color-mix(in srgb, var(--create-entity-accent) 45%, var(--color-primary) 55%);
        background-color: color-mix(in srgb, var(--create-entity-accent) 40%, var(--color-primary) 60%);
        box-shadow: 0 10px 20px -16px var(--create-entity-accent);
    }
}
</style>

