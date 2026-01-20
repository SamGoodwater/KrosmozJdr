<script setup>
/**
 * EntityModal Organism
 * 
 * @description
 * Modal pour afficher une entité avec les 4 vues possibles
 * 
 * @props {Object} entity - Données de l'entité
 * @props {String} entityType - Type d'entité
 * @props {String} view - Vue à afficher ('large', 'compact', 'minimal', 'text'), défaut 'large'
 * @props {Boolean} open - Contrôle l'ouverture du modal
 * @props {Boolean} useStoredFormat - Utiliser le format stocké dans localStorage (défaut: true)
 * @emit close - Événement émis lors de la fermeture
 */
import { computed, defineAsyncComponent, shallowRef, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Dropdown from '@/Pages/Atoms/action/Dropdown.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import { useEntityViewFormat } from '@/Composables/store/useEntityViewFormat';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { normalizeEntityType } from '@/Entities/entity-registry';
import { resolveEntityViewComponent } from '@/Utils/entity/resolveEntityViewComponent';

const props = defineProps({
    entity: {
        type: Object,
        required: true
    },
    entityType: {
        type: String,
        required: true
    },
    view: {
        type: String,
        default: null,
        validator: (v) => !v || ['large', 'compact', 'minimal', 'text'].includes(v)
    },
    open: {
        type: Boolean,
        default: false
    },
    useStoredFormat: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['close', 'edit', 'quick-edit', 'expand', 'copy-link', 'download-pdf', 'refresh', 'delete']);

// Utiliser le format stocké si useStoredFormat est true et que view n'est pas fourni
const { viewFormat, setViewFormat, availableFormats, minimalDisplayMode, setMinimalDisplayMode, availableMinimalDisplayModes } = useEntityViewFormat(props.entityType);
const currentView = computed(() => {
    if (props.view) {
        return props.view;
    }
    if (props.useStoredFormat) {
        return viewFormat.value;
    }
    return 'large';
});

const modalSize = computed(() => {
    const sizes = {
        large: 'xl',
        compact: 'lg',
        minimal: 'md',
        text: 'sm'
    };
    return sizes[currentView.value] || 'xl';
});

const handleClose = () => {
    emit('close');
};

/**
 * Récupère le nom de l'entité en gérant les modèles et objets bruts
 */
const getEntityName = () => {
    // Si c'est une instance de modèle, utiliser le getter name
    if (props.entity && typeof props.entity._data !== 'undefined') {
        return props.entity.name || props.entity.title || 'Entité';
    }
    // Sinon, accès direct
    return props.entity?.name || props.entity?.title || 'Entité';
};

// Handlers pour les actions
const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf(props.entityType);

const entityTypeKey = computed(() => {
    // Normaliser le type d'entité (ex: 'resources' -> 'resource')
    const type = props.entityType;
    if (type.endsWith('s')) {
        return type.slice(0, -1);
    }
    return type;
});

// Charger dynamiquement le composant de vue approprié
const ViewComponent = shallowRef(null);

// Mapper les types d'entités vers leurs noms de composants
const ENTITY_COMPONENT_MAP = {
    'resources': 'Resource',
    'items': 'Item',
    'consumables': 'Consumable',
    'spells': 'Spell',
    'monsters': 'Monster',
    'creatures': 'Creature',
    'npcs': 'Npc',
    'classes': 'Classe',
    'campaigns': 'Campaign',
    'scenarios': 'Scenario',
    'attributes': 'Attribute',
    'panoplies': 'Panoply',
    'capabilities': 'Capability',
    'specializations': 'Specialization',
    'shops': 'Shop',
};

// Mapper les vues vers leurs noms de composants
const VIEW_COMPONENT_MAP = {
    'large': 'ViewLarge',
    'compact': 'ViewCompact',
    'minimal': 'ViewMinimal',
    'text': 'ViewText',
};

// Fonction pour charger le composant de vue
const loadViewComponent = async () => {
    try {
        const component = await resolveEntityViewComponent(props.entityType, currentView.value);
        if (component) {
            ViewComponent.value = defineAsyncComponent(() => Promise.resolve(component));
        } else {
            ViewComponent.value = null;
        }
    } catch (error) {
        console.error(`[EntityModal] Erreur lors du chargement du composant pour ${props.entityType}/${currentView.value}:`, error);
        ViewComponent.value = null;
    }
};

// Charger le composant quand l'entité ou la vue change
watch([() => props.entityType, currentView], () => {
    loadViewComponent();
}, { immediate: true });

// Obtenir les props à passer au composant selon le type d'entité
const getComponentProps = () => {
    const normalizedType = normalizeEntityType(props.entityType);
    const entityName = ENTITY_COMPONENT_MAP[normalizedType];
    
    if (!entityName) {
        return { entity: props.entity, showActions: false };
    }
    
    // Convertir le nom de l'entité en camelCase pour la prop
    const propName = entityName.charAt(0).toLowerCase() + entityName.slice(1);
    
    const common = {
        [propName]: props.entity,
        showActions: false,
    };

    if (currentView.value === 'minimal') {
        return {
            ...common,
            displayMode: minimalDisplayMode.value,
        };
    }

    return common;
};

const handleAction = async (actionKey, entity) => {
    const targetEntity = entity || props.entity;
    const entityId = targetEntity?.id ?? props.entity?.id ?? null;
    if (!entityId) return;

    const entityTypePlural = props.entityType === 'panoply' ? 'panoplies' : `${entityTypeKey.value}s`;

    switch (actionKey) {
        case 'quick-edit':
            emit('quick-edit', targetEntity);
            break;

        case 'expand':
            // Expand depuis un modal : redirige vers view (page complète)
            router.visit(route(`entities.${entityTypePlural}.show`, { [entityTypeKey.value]: entityId }));
            emit('expand', targetEntity);
            handleClose(); // Fermer le modal après redirection
            break;

        case 'copy-link': {
            const cfg = getEntityRouteConfig(entityTypeKey.value);
            const url = resolveEntityRouteUrl(entityTypeKey.value, 'show', entityId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de l'entité copié !");
            }
            emit('copy-link', targetEntity);
            break;
        }

        case 'download-pdf':
            await downloadPdf(entityId);
            emit('download-pdf', targetEntity);
            break;

        case 'refresh':
            router.reload({ only: [entityTypePlural] });
            emit('refresh', targetEntity);
            break;

        case 'delete':
            emit('delete', targetEntity);
            break;
    }
};
</script>

<template>
    <Modal 
        :open="open" 
        :size="modalSize"
        placement="middle-center"
        close-on-esc
        :close-on-button="false"
        @close="handleClose">
        
        <template #header>
            <div class="flex items-center justify-between w-full gap-4">
                <h3 class="text-lg font-bold text-primary-100 flex-1 min-w-0">
                    {{ getEntityName() }}
                </h3>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <Dropdown
                        v-if="!view && useStoredFormat"
                        placement="bottom-end"
                        variant="glass"
                        color="primary"
                        size="sm"
                        :close-on-content-click="false"
                        aria-label="Options d’affichage"
                    >
                        <template #trigger>
                            <Btn size="sm" variant="glass" color="primary" aria-label="Options d’affichage">
                                <Icon source="fa-solid fa-sliders" size="sm" />
                            </Btn>
                        </template>
                        <template #content>
                            <div class="p-3 space-y-3 min-w-64">
                                <div class="text-xs font-semibold uppercase tracking-wide text-primary-200/80">
                                    Format
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <Btn
                                        v-for="f in availableFormats"
                                        :key="f.value"
                                        size="sm"
                                        :variant="viewFormat === f.value ? 'glass' : 'ghost'"
                                        color="primary"
                                        class="justify-start gap-2"
                                        @click="setViewFormat(f.value)"
                                    >
                                        <Icon :source="f.icon" size="sm" />
                                        <span class="truncate">{{ f.label }}</span>
                                    </Btn>
                                </div>

                                <div v-if="viewFormat === 'minimal'" class="pt-2 border-t border-base-300">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-primary-200/80 mb-2">
                                        Minimal
                                    </div>
                                    <div class="space-y-1">
                                        <Btn
                                            v-for="m in availableMinimalDisplayModes"
                                            :key="m.value"
                                            size="sm"
                                            :variant="minimalDisplayMode === m.value ? 'glass' : 'ghost'"
                                            color="primary"
                                            class="w-full justify-start"
                                            @click="setMinimalDisplayMode(m.value)"
                                        >
                                            {{ m.label }}
                                        </Btn>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Dropdown>

                    <EntityActions
                        :entity-type="entityTypeKey"
                        :entity="entity"
                        format="buttons"
                        display="icon-only"
                        size="sm"
                        color="primary"
                        :context="{ inModal: true, modalMode: 'view' }"
                        @action="handleAction"
                    />
                    <Btn
                        circle
                        size="sm"
                        variant="ghost"
                        aria-label="Fermer"
                        @click="handleClose"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </Btn>
                </div>
            </div>
        </template>

        <div>
            <component
                v-if="ViewComponent"
                :is="ViewComponent"
                v-bind="getComponentProps()"
            />
            <div v-else class="text-center text-primary-300 py-8">
                <p>Chargement de la vue...</p>
            </div>
        </div>

        <template #actions>
            <Btn @click="handleClose">Fermer</Btn>
        </template>
    </Modal>
</template>

