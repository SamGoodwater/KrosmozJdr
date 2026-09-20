<script setup>
/**
 * CreateEntityModal Organism
 *
 * @description
 * Modal de création courte : à la main (champs principaux) ou via IA (admin, types convertibles).
 * Après création, redirection vers la vue Modifier pour le reste de la fiche.
 *
 * @example
 * <CreateEntityModal open entity-type="npc" @close="close" @created="onCreated" />
 */
import { computed, ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import TextareaField from '@/Pages/Molecules/data-input/TextareaField.vue';
import ConfirmPasswordModal from '@/Pages/Molecules/action/ConfirmPasswordModal.vue';
import EntityEditForm from './EntityEditForm.vue';
import { getEntityConfig as getRegistryEntityConfig, normalizeEntityType } from '@/Entities/entity-registry';
import { isAiConvertibleEntityType } from '@/Entities/entity-actions-config';
import { createDefaultEntityFromDescriptors, createFieldsConfigFromDescriptors } from '@/Utils/entity/descriptor-form';
import { getEntityCreateCoreFieldKeys, getEntityCreateLabel } from '@/Utils/entity/entity-create-config';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { useProtectedAdminAction } from '@/Composables/auth/useProtectedAdminAction';

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
    routeNameBase: {
        type: String,
        default: null
    },
    routeParamKey: {
        type: String,
        default: null
    },
    fieldSections: {
        type: Array,
        default: null,
    },
    hiddenFieldKeys: {
        type: Array,
        default: null,
    },
    showStateToolbar: {
        type: Boolean,
        default: false,
    },
    showAccessLevelsInFooter: {
        type: Boolean,
        default: false,
    },
    createAllowFieldKeys: {
        type: Array,
        default: () => [],
    },
    characteristicsGroup: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['close', 'created']);

const page = usePage();
const { isAdmin } = usePermissions();
const {
    showPasswordModal,
    passwordModalTitle,
    passwordModalMessage,
    passwordModalConfirmLabel,
    requirePassword,
    onPasswordConfirmed,
    onPasswordModalCancel,
} = useProtectedAdminAction();

const pane = ref('manual');
const aiName = ref('');
const aiBrief = ref('');
const aiBusy = ref(false);
const aiError = ref('');

const normalizedEntityType = computed(() => normalizeEntityType(props.entityType));
const registryEntityConfig = computed(() => getRegistryEntityConfig(props.entityType));
const showAiTab = computed(() => Boolean(isAdmin.value) && isAiConvertibleEntityType(normalizedEntityType.value));

watch(
    () => props.open,
    (open) => {
        if (open) {
            pane.value = 'manual';
            aiName.value = '';
            aiBrief.value = '';
            aiError.value = '';
            aiBusy.value = false;
        }
    },
);

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

const NAME_FIELD = {
    type: 'text',
    label: 'Nom',
    required: true,
    group: 'Identité',
};

const mergedFieldsConfig = computed(() => {
    const custom = props.fieldsConfig || {};
    const generated = Object.keys(custom).length > 0 ? custom : (descriptorBackedFieldsConfig.value || {});
    const coreKeys = getEntityCreateCoreFieldKeys(props.entityType);
    const picked = {};
    for (const key of coreKeys) {
        if (generated[key]) {
            picked[key] = generated[key];
        }
    }
    if (!picked.name) {
        picked.name = NAME_FIELD;
    }
    return picked;
});

const entityColorVar = computed(() => {
    const map = {
        items: 'item',
        spells: 'spell',
        monsters: 'monster',
        npcs: 'npc',
        breeds: 'breed',
        consumables: 'consumable',
        resources: 'resource',
        capabilities: 'capability',
        specializations: 'specialization',
        panoplies: 'panoply',
        conditions: 'condition',
        'creature-traits': 'creature-trait',
        campaigns: 'campaign',
        scenarios: 'scenario',
        shops: 'shop',
    };
    const token = map[normalizedEntityType.value] || 'primary';
    return `var(--color-${token}-700)`;
});

const modalBodyStyle = computed(() => ({
    backgroundColor: `color-mix(in srgb, ${entityColorVar.value} 5%, var(--color-base-100))`,
    borderColor: `color-mix(in srgb, ${entityColorVar.value} 28%, var(--color-base-300))`,
    boxShadow: `0 18px 40px -30px ${entityColorVar.value}`,
    '--create-entity-accent': entityColorVar.value,
}));

const emptyEntity = computed(() => {
    return {
        id: null,
        ...descriptorBackedDefaultEntity.value,
        ...props.defaultEntity,
    };
});

const entityCreateLabel = computed(() => getEntityCreateLabel(props.entityType));

const storeRouteName = computed(() => {
    if (props.routeNameBase) {
        return `${props.routeNameBase}.store`;
    }
    return `entities.${normalizedEntityType.value}.store`;
});

const iaAction = computed(() => {
    const map = {
        monsters: 'encounter',
        spells: 'spell',
        npcs: 'npc',
        items: 'item',
        consumables: 'consumable',
    };
    return map[normalizedEntityType.value] || '';
});

const handleClose = () => {
    emit('close');
};

const handleSubmit = () => {
    emit('created');
    handleClose();
};

const handleCancel = () => {
    handleClose();
};

function stubNameFromBrief() {
    const typed = String(aiName.value || '').trim();
    if (typed !== '') {
        return typed;
    }
    const brief = String(aiBrief.value || '').trim();
    if (brief !== '') {
        return brief.slice(0, 80);
    }
    return 'Brouillon IA';
}

async function runAiCreate() {
    const brief = String(aiBrief.value || '').trim();
    if (brief === '') {
        aiError.value = 'Indique un brief pour l’IA (rôle, niveau, ton…).';
        return;
    }
    aiBusy.value = true;
    aiError.value = '';
    try {
        const created = await axios.post(
            route(storeRouteName.value),
            {
                name: stubNameFromBrief(),
                description: brief,
                state: 'draft',
            },
            { headers: { Accept: 'application/json' } },
        );
        const id = Number(created?.data?.id);
        const editUrl = created?.data?.edit_url;
        if (!id) {
            throw new Error('Création refusée : identifiant manquant.');
        }
        await axios.post(
            route('api.entities.ia-convert', {
                entityType: normalizedEntityType.value,
                id,
            }),
            {
                action: iaAction.value,
                brief,
                force: false,
            },
            { headers: { Accept: 'application/json' } },
        );
        emit('created', { id, via: 'ia' });
        handleClose();
        if (editUrl) {
            router.visit(editUrl);
        }
    } catch (error) {
        if (error?.response?.status === 423) {
            aiBusy.value = false;
            requirePassword(
                'Confirmer la conversion IA',
                'Entre ton mot de passe admin pour lancer la génération.',
                'Continuer',
                () => {
                    runAiCreate();
                },
            );
            return;
        }
        const message = error?.response?.data?.message
            || error?.message
            || 'La création IA a échoué.';
        aiError.value = String(message);
    } finally {
        aiBusy.value = false;
    }
}

function submitAi() {
    requirePassword(
        'Confirmer la conversion IA',
        'Entre ton mot de passe admin pour lancer la génération.',
        'Créer avec l’IA',
        () => {
            runAiCreate();
        },
    );
}
</script>

<template>
    <Modal
        :open="open"
        size="lg"
        placement="middle-center"
        close-on-esc
        @close="handleClose"
    >
        <template #header>
            <h3 class="text-xl font-bold text-primary-100">
                Créer {{ entityCreateLabel }}
            </h3>
        </template>

        <div class="entity-create-theme rounded-(--radius-field) border p-3 space-y-3" :style="modalBodyStyle">
            <div
                v-if="showAiTab"
                role="tablist"
                class="tabs tabs-box tabs-sm bg-base-200/60 p-1 w-fit"
                data-testid="entity-create-tabs"
            >
                <button
                    type="button"
                    role="tab"
                    class="tab"
                    :class="{ 'tab-active': pane === 'manual' }"
                    :aria-selected="pane === 'manual' ? 'true' : 'false'"
                    data-testid="entity-create-tab-manual"
                    @click="pane = 'manual'"
                >
                    À la main
                </button>
                <button
                    type="button"
                    role="tab"
                    class="tab"
                    :class="{ 'tab-active': pane === 'ia' }"
                    :aria-selected="pane === 'ia' ? 'true' : 'false'"
                    data-testid="entity-create-tab-ia"
                    @click="pane = 'ia'"
                >
                    Conversion IA
                </button>
            </div>

            <p class="text-sm text-base-content/80">
                Quelques champs pour démarrer. Ensuite la fiche s’ouvre en
                <span class="font-medium">Modifier</span> pour le reste.
            </p>

            <div v-if="pane === 'manual'" class="max-h-[70vh] overflow-y-auto pr-2">
                <EntityEditForm
                    :entity="emptyEntity"
                    :entity-type="entityType"
                    :fields-config="mergedFieldsConfig"
                    :route-name-base="routeNameBase"
                    :route-param-key="routeParamKey"
                    :is-updating="false"
                    :hidden-field-keys="hiddenFieldKeys"
                    :show-state-toolbar="showStateToolbar"
                    :show-access-levels-in-footer="showAccessLevelsInFooter"
                    :characteristics-group="characteristicsGroup"
                    :restrict-to-field-keys="Object.keys(mergedFieldsConfig)"
                    embedded-in-modal
                    redirect-after-create
                    :shortcuts-active="open"
                    @submit="handleSubmit"
                    @cancel="handleCancel"
                />
            </div>

            <div v-else class="space-y-3">
                <InputField
                    v-model="aiName"
                    label="Nom (optionnel)"
                    placeholder="Laissé vide : l’IA invente, ou « Brouillon IA »"
                />
                <TextareaField
                    v-model="aiBrief"
                    label="Brief pour l’IA"
                    placeholder="Ex. garde Iop d’Astrub, niveau 8, brutal, pas un boss"
                    :rows="4"
                />
                <p
                    v-if="aiError"
                    class="rounded-box border border-error/40 bg-error/10 px-3 py-2 text-sm text-error"
                    data-testid="entity-create-ai-error"
                >
                    {{ aiError }}
                </p>
                <div class="flex justify-end gap-2">
                    <Btn variant="ghost" :disabled="aiBusy" @click="handleCancel">Annuler</Btn>
                    <Btn
                        color="primary"
                        :disabled="aiBusy"
                        data-testid="entity-create-ai-submit"
                        @click="submitAi"
                    >
                        {{ aiBusy ? 'Génération…' : 'Créer avec l’IA' }}
                    </Btn>
                </div>
            </div>
        </div>
    </Modal>

    <ConfirmPasswordModal
        v-model:open="showPasswordModal"
        :title="passwordModalTitle"
        :message="passwordModalMessage"
        :confirm-label="passwordModalConfirmLabel"
        @confirmed="onPasswordConfirmed"
        @cancel="onPasswordModalCancel"
    />
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
