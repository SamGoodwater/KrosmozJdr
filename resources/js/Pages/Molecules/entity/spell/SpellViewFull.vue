<script setup>
/**
 * SpellViewFull — Vue Full pour Spell
 *
 * @description
 * Fiche sort condensée : image (élément) | nom / méta / description ;
 * utilisation (colonnes étiquetées), résolution + effets, métadonnées.
 *
 * @props {Spell} spell - Instance du modèle Spell
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import CellRenderer from '@/Pages/Atoms/data-display/CellRenderer.vue';
import EntityPropertyDisplay from '@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue';
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import EntityViewHeader from '@/Pages/Molecules/entity/shared/EntityViewHeader.vue';
import ImageViewer from '@/Pages/Molecules/data-display/ImageViewer.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import SpellEffectsJournal from '@/Pages/Molecules/entity/spell/SpellEffectsJournal.vue';
import SpellDegreeTabs from '@/Pages/Molecules/entity/spell/SpellDegreeTabs.vue';
import SpellUsageBlock from '@/Pages/Molecules/entity/spell/SpellUsageBlock.vue';
import SpellMinimalDegreeFacts from '@/Pages/Molecules/entity/spell/SpellMinimalDegreeFacts.vue';
import SpellResolutionFormula from '@/Pages/Molecules/entity/spell/SpellResolutionFormula.vue';
import { resolveEntityFieldUi, resolveEntityBadgeUi } from '@/Utils/Entity/entity-view-ui';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { useEntityActionDispatcher } from '@/Composables/entity/useEntityActionDispatcher';
import EntityDofusdbRefreshPanel from '@/Pages/Molecules/entity/EntityDofusdbRefreshPanel.vue';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { getSpellFieldDescriptors } from '@/Entities/spell/spell-descriptors';
import { getCharacteristicColorStyle, resolveDef } from '@/Composables/entity/useCharacteristicDisplay';
import { provideCharacteristicRuntime } from '@/Composables/entity/characteristicRuntimeContext';
import { spellTypesCellHasRenderableContent } from '@/Utils/Entity/spellTypeVisual.js';
import { spellHasDegreePayload, useSpellDisplayDegrees } from '@/Composables/entity/useSpellDisplayDegrees';
import { fetchEntityModelById } from '@/Composables/entity/useEntityTableFetch';
import {
    resolveSpellEffectsDisplayCell,
    spellEffectsCellHasContent,
} from '@/Composables/entity/useSpellEffectsDisplayCell';
import {
    ELEMENT_PRIMARY_LABELS,
    getElementIcon,
    getElementPrimaries,
    resolveEntityElementValue,
} from '@/Utils/Entity/Elements';
import { spellTypeRuleNotes } from '@/Utils/Entity/spellTypeRuleNotes';

const props = defineProps({
    spell: {
        type: Object,
        required: true,
    },
    showActions: {
        type: Boolean,
        default: true,
    },
    tableMeta: {
        type: Object,
        default: () => ({}),
    },
    /** Balise du titre principal (h1 sur page fiche, h2 en modal). */
    titleTag: {
        type: String,
        default: 'h2',
        validator: (v) => ['h1', 'h2', 'h3'].includes(v),
    },
    /** Payload runtime (ex. Inertia `characteristicRuntime`) pour tooltips formules — transmis aux EntityPropertyDisplay */
    characteristicRuntime: {
        type: Object,
        default: null,
    },
    /** true dans EntityModal — header dense */
    inModal: {
        type: Boolean,
        default: false,
    },
});


const actionsContext = computed(() =>
    props.inModal
        ? { inPanel: false, inModal: true, surface: 'modal', viewMode: 'full', modalMode: 'view' }
        : { inPanel: false, inPage: true, surface: 'page', viewMode: 'full' },
);

const headerMode = computed(() => (props.inModal ? 'compact' : 'full'));

provideCharacteristicRuntime(computed(() => props.characteristicRuntime));

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'delete', 'action']);

const { copyToClipboard } = useCopyToClipboard();
const { dispatchEntityAction, refreshConfirm, confirmPendingRefresh, cancelPendingRefresh, submitAiConvert, submitAiInject, confirmUpdateDiffSave, confirmUpdateDiffRestore } =
    useEntityActionDispatcher('spells', {
        onRefresh: () => router.reload(),
    });
const { downloadPdf } = useDownloadPdf('spell');
const permissions = usePermissions();

/** Payload table/liste parfois sans `spell_degrees` — hydrate pour les onglets. */
const hydratedSpell = ref(null);
const spell = computed(() => hydratedSpell.value || props.spell);

watch(
    () => props.spell,
    async (incoming) => {
        hydratedSpell.value = null;
        const id = incoming?.id ?? incoming?._data?.id;
        if (!id || spellHasDegreePayload(incoming)) return;
        try {
            const full = await fetchEntityModelById('spells', id);
            if (full && spellHasDegreePayload(full)) {
                hydratedSpell.value = full;
            }
        } catch {
            /* payload allégé en repli */
        }
    },
    { immediate: true },
);

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('spells', 'viewAny'),
        createAny: permissions.can('spells', 'createAny'),
        updateAny: permissions.can('spells', 'updateAny'),
        deleteAny: permissions.can('spells', 'deleteAny'),
        manageAny: permissions.can('spells', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getSpellFieldDescriptors(ctx.value));

const autoUpdateValue = computed(() => {
    const v = spell.value?.auto_update ?? spell.value?._data?.auto_update;
    return typeof v === 'boolean' ? v : null;
});

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf ?? desc?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[SpellViewFull] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

const userCanEditFields = computed(() => ['auto_update', 'read_level', 'write_level'].filter(canShowField));

const technicalFields = computed(() =>
    ['dofusdb_id', 'official_id', 'created_by', 'created_at', 'updated_at'].filter(canShowField),
);

const getFieldUi = (fieldKey) =>
    resolveEntityFieldUi({
        fieldKey,
        descriptors: descriptors.value,
        tableMeta: props.tableMeta,
        entityType: 'spell',
    });

const getFieldLabel = (fieldKey) => getFieldUi(fieldKey).label;

const getFieldIcon = (fieldKey) => getFieldUi(fieldKey).icon;

const getFieldTooltip = (fieldKey) => getFieldUi(fieldKey).tooltip;

const getFieldIconStyle = (fieldKey) => {
    const color = getFieldUi(fieldKey).color;
    return color ? { color } : undefined;
};

const getCell = (fieldKey) =>
    spell.value.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
    });

const getBadgeColor = (fieldKey) => {
    const colorMap = {
        level: 'warning',
        auto_update: 'warning',
        read_level: 'primary',
        write_level: 'secondary',
        dofusdb_id: 'neutral',
        official_id: 'neutral',
        created_by: 'neutral',
        created_at: 'neutral',
        updated_at: 'neutral',
    };
    return resolveEntityBadgeUi({
        fieldKey,
        cell: getCell(fieldKey),
        fieldUi: getFieldUi(fieldKey),
        localColorMap: colorMap,
    }).color;
};

const getBadgeAutoParams = (fieldKey) => {
    const { autoLabel, autoScheme, autoTone } = resolveEntityBadgeUi({
        fieldKey,
        cell: getCell(fieldKey),
        fieldUi: getFieldUi(fieldKey),
    });
    return { autoLabel, autoScheme, autoTone };
};

const asTextCell = (cell) => {
    if (!cell) return { type: 'text', value: '-', params: {} };
    const v = cell?.value;
    return {
        type: 'text',
        value: v === null || typeof v === 'undefined' || String(v) === '' ? '-' : String(v),
        params: cell?.params || {},
    };
};

const magicDef = computed(() =>
    resolveDef('is_magic_spell', spell.value?.isMagic, { sourceGroups: ['spell'] }),
);

const effectsDefinitions = computed(() =>
    Array.isArray(spell.value?.effectsDefinitions) ? spell.value.effectsDefinitions : [],
);

const spellDegreesPayload = computed(() => spell.value?.spellDegrees || { degrees: [], default_degree_id: null });

const { degrees: displayDegrees, hasDegrees } = useSpellDisplayDegrees(spell);

const activeDegreeIndex = ref(0);

const activeDegree = computed(() => displayDegrees.value[activeDegreeIndex.value] || null);

watch(
    displayDegrees,
    (list) => {
        if (!list.length) {
            activeDegreeIndex.value = 0;
            return;
        }
        if (activeDegreeIndex.value >= list.length) {
            activeDegreeIndex.value = 0;
        }
    },
    { immediate: true },
);

/**
 * Entité pour la méta PA/PO/LoS : clone Spell avec `_data` surchargé par le degré actif.
 *
 * @returns {import('@/Models/Entity/Spell').Spell|object}
 */
const usageEntity = computed(() => {
    const base = spell.value;
    const propsDeg = activeDegree.value?.properties;
    if (!base || !propsDeg) {
        return base;
    }
    const merged = { ...(base._data || {}) };
    for (const [k, v] of Object.entries(propsDeg)) {
        if (v !== null && v !== undefined) {
            merged[k] = v;
        }
    }
    merged._active_degree_properties = propsDeg;
    // Réutiliser la classe Spell si disponible sur l’instance.
    const Ctor = base.constructor;
    if (typeof Ctor === 'function' && Ctor !== Object) {
        try {
            return new Ctor(merged);
        } catch {
            /* fallback objet plat */
        }
    }
    return { ...base, _data: merged, ...merged };
});

const freeEffectComment = computed(() => {
    const t = spell.value?.effect != null ? String(spell.value.effect).trim() : '';
    return t;
});

const ritualDef = computed(() =>
    resolveDef('ritual_available_spell', usageEntity.value?.isRitual, { sourceGroups: ['spell'] }),
);

const showRitualBadge = computed(() => usageEntity.value?.isRitual === true);

const ritualTooltipText = computed(() => {
    const d = ritualDef.value;
    if (d?._resolvedSubtitle) return d._resolvedSubtitle;
    const t = d?.helper || (Array.isArray(d?.descriptions) ? d.descriptions.join(' ') : d?.descriptions) || '';
    return t || 'Ce sort peut être lancé comme un rituel (incantation prolongée).';
});

function characteristicTooltipText(meta) {
    if (!meta) return '';
    return (
        meta.helper ||
        (Array.isArray(meta.descriptions) ? meta.descriptions.join(' ') : meta.descriptions) ||
        ''
    );
}

const showSpellTypesCell = computed(() =>
    spellTypesCellHasRenderableContent(getCell('spell_types')),
);

/** Identité sans élément (icône sur l’image) ni types vides. */
const identityFieldKeys = computed(() => {
    const keys = [];
    if (canShowField('spell_types') && showSpellTypesCell.value) {
        keys.push('spell_types');
    }
    if (canShowField('category')) {
        keys.push('category');
    }
    return keys;
});

const elementOverlay = computed(() => {
    const primaries = getElementPrimaries(resolveEntityElementValue(spell.value));
    if (!primaries.length) return null;
    const first = primaries[0];
    if (first === 0 && primaries.length === 1) return null;
    return {
        icon: getElementIcon(first),
        label: ELEMENT_PRIMARY_LABELS[first] ?? '',
    };
});

const ruleNotes = computed(() => spellTypeRuleNotes(usageEntity.value ?? spell.value));

/** Pastille niveau sort masquée si les onglets de degré portent déjà le niveau. */
const showLevelBadgeOnImage = computed(() => !hasDegrees.value);

const activeDegreeHasRows = computed(() => {
    const rows = activeDegree.value?.rows;
    return Array.isArray(rows) && rows.length > 0;
});

/** Chips structurés uniquement (pas le fallback texte `effect`, pour éviter la répétition). */
const hasSummaryChips = computed(() =>
    spellEffectsCellHasContent(
        resolveSpellEffectsDisplayCell(usageEntity.value, {
            size: 'xs',
            context: 'minimal',
            structuredOnly: true,
        }),
    ),
);

const hasLegacyEffectText = computed(() => freeEffectComment.value !== '');

const handleAction = async (actionKey) => {
    const spellId = spell.value.id;
    if (!spellId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.spells.show', { spell: spellId }));
            emit('view', spell.value);
            break;
        case 'edit':
            await dispatchEntityAction("edit", spell.value, { context: actionsContext.value });
            emit('edit', spell.value);
            break;
        
        case 'copy-link': {
            const cfg = getEntityRouteConfig('spell');
            const url = resolveEntityRouteUrl('spell', 'show', spellId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, 'Lien du sort copié !');
            }
            emit('copy-link', spell.value);
            break;
        }
        case 'download-pdf':
            await downloadPdf(spellId);
            emit('download-pdf', spell.value);
            break;
        case 'refresh': {
            await dispatchEntityAction('refresh', spell.value);
            break;
        }
        case 'delete':
            emit('delete', spell.value);
            break;
    }
};
</script>

<template>
    <div class="space-y-3">
        <EntityViewHeader :mode="headerMode">
            <template #media>
                <div
                    class="group relative size-24 shrink-0 sm:size-28 md:size-32"
                    data-cy="spell-full-media"
                >
                    <div
                        v-if="showLevelBadgeOnImage"
                        class="absolute top-1.5 left-1.5 z-20 transition-opacity duration-150 group-hover:opacity-0"
                    >
                        <Badge
                            :color="getBadgeColor('level')"
                            :auto-label="getBadgeAutoParams('level').autoLabel"
                            :auto-scheme="getBadgeAutoParams('level').autoScheme"
                            :auto-tone="getBadgeAutoParams('level').autoTone"
                            size="sm"
                        >
                            <CellRenderer :cell="asTextCell(getCell('level'))" ui-color="primary" />
                        </Badge>
                    </div>

                    <ImageViewer
                        v-if="spell.image"
                        :source="spell.image"
                        :alt="spell.name || 'Sort'"
                        :caption="spell.name || ''"
                        preload="hover"
                        :image-props="{
                            size: 'lg',
                            rounded: 'lg',
                            fit: 'cover',
                            class: 'w-full h-full',
                        }"
                    />

                    <div v-else class="flex size-full items-center justify-center bg-base-200 entity-radius-box">
                        <Icon source="fa-solid fa-wand-magic-sparkles" :alt="spell.name" size="lg" />
                    </div>

                    <span
                        v-if="elementOverlay"
                        class="pointer-events-none absolute top-1.5 right-1.5 z-20 inline-flex size-7 items-center justify-center rounded-full bg-base-100/90 shadow-sm ring-1 ring-base-content/15"
                        data-cy="spell-full-element-overlay"
                    >
                        <Icon
                            :source="elementOverlay.icon"
                            :alt="elementOverlay.label"
                            size="xs"
                            class="shrink-0"
                        />
                    </span>
                </div>
            </template>

            <template #title>
                <component :is="titleTag" class="text-xl font-bold break-words text-primary-100 md:text-2xl">
                    {{ spell.name }}
                </component>
            </template>

            <template #mainInfos>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5">
                    <EntityPropertyDisplay
                        v-for="fieldKey in identityFieldKeys"
                        :key="fieldKey"
                        :field-key="fieldKey"
                        :entity="spell"
                        entity-type="spell"
                        display-mode="extended"
                        :presentation="fieldKey === 'spell_types' ? 'spell-types-icons-only' : 'default'"
                        :descriptors="descriptors"
                        :table-meta="tableMeta"
                        size="sm"
                        class="max-w-[20rem] whitespace-normal break-words"
                    />

                    <Tooltip
                        v-if="typeof spell.isMagic === 'boolean' && magicDef"
                        :content="magicDef._resolvedSubtitle || characteristicTooltipText(magicDef) || 'Orientation du sort.'"
                        placement="top"
                    >
                        <span
                            class="inline-flex cursor-default items-center gap-1.5 text-sm font-medium"
                            :style="(magicDef._resolvedColor || magicDef.color) ? getCharacteristicColorStyle(magicDef._resolvedColor || magicDef.color) : undefined"
                        >
                            <Icon
                                v-if="magicDef._resolvedIcon || magicDef.icon"
                                :source="magicDef._resolvedIcon || magicDef.icon"
                                :alt="spell.isMagic ? 'Wakfu' : 'Physique'"
                                size="sm"
                            />
                            <span>{{ spell.isMagic ? (magicDef.short_name || 'Wakfu') : 'Physique' }}</span>
                        </span>
                    </Tooltip>

                    <Tooltip v-if="showRitualBadge" :content="ritualTooltipText" placement="top">
                        <span
                            class="inline-flex cursor-default items-center gap-1.5 text-sm"
                            :style="(ritualDef?._resolvedColor || ritualDef?.color) ? getCharacteristicColorStyle(ritualDef._resolvedColor || ritualDef.color) : undefined"
                        >
                            <Icon
                                :source="ritualDef?._resolvedIcon || ritualDef?.icon || 'fa-solid fa-book-open'"
                                :alt="ritualDef?.short_name || 'Rituel'"
                                size="sm"
                            />
                            <span>{{ ritualDef?.short_name || ritualDef?.name || 'Rituel' }}</span>
                        </span>
                    </Tooltip>
                </div>
            </template>

            <template v-if="spell.description" #subtitle>
                <p
                    class="text-sm leading-relaxed whitespace-pre-wrap break-words text-primary-200 md:text-base"
                    data-cy="spell-full-description"
                >
                    {{ spell.description }}
                </p>
            </template>

            <template #actions>
                <div v-if="showActions">
                    <EntityActions
                        entity-type="spells"
                        :entity="spell"
                        format="buttons"
                        display="icon-only"
                        size="sm"
                        color="primary"
                        :context="actionsContext"
                        @action="handleAction"
                    />
                </div>
            </template>
        </EntityViewHeader>

        <section class="space-y-2">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Utilisation</h3>
                <SpellDegreeTabs
                    v-if="hasDegrees"
                    v-model="activeDegreeIndex"
                    :degrees="displayDegrees"
                    appearance="buttons"
                    size="sm"
                />
            </div>
            <div class="min-w-0 space-y-2 p-3 bg-base-200 entity-radius-box">
                <SpellMinimalDegreeFacts
                    :entity="usageEntity"
                    :descriptors="descriptors"
                    :table-meta="tableMeta"
                    :can-show-field="canShowField"
                    :area="activeDegree?.area || ''"
                    layout="labeled"
                    show-area-schema
                    property-size="sm"
                />
                <ul
                    v-if="ruleNotes.length"
                    class="space-y-0.5 text-sm leading-snug text-primary-200/80"
                    data-cy="spell-full-rule-notes"
                >
                    <li v-for="note in ruleNotes" :key="note">{{ note }}</li>
                </ul>
            </div>
        </section>

        <section class="space-y-3">
            <div class="space-y-1.5">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Résolution</h3>
                <SpellResolutionFormula :entity="usageEntity" variant="full" class="text-sm text-primary-100" />
                <p v-if="usageEntity.saveSuccessNote" class="text-sm whitespace-pre-wrap break-words text-primary-300">
                    <span class="font-semibold text-primary-400">Si réussite : </span>
                    {{ usageEntity.saveSuccessNote }}
                </p>
            </div>

            <div class="space-y-1.5">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Effets</h3>
                <SpellEffectsJournal
                    v-if="activeDegreeHasRows"
                    v-model="activeDegreeIndex"
                    hide-tabs
                    :spell-degrees="spellDegreesPayload"
                    :definitions="effectsDefinitions"
                    sub-effect-layout="large"
                />
                <SpellUsageBlock
                    v-else-if="hasSummaryChips"
                    parts="effects"
                    :entity="usageEntity"
                    :descriptors="descriptors"
                    :table-meta="tableMeta"
                    :can-show-field="canShowField"
                    :max-effect-rows="6"
                    flush-effects
                    :show-empty-effects="false"
                    :show-rule-notes="false"
                    free-effect-comment=""
                    notes-class="mb-2 text-sm leading-snug text-primary-200/80"
                    cell-class="text-sm leading-snug [&_.inline-flex]:max-w-full [&_.inline-flex]:flex-wrap"
                />
                <p
                    v-if="hasLegacyEffectText"
                    class="text-xs leading-snug whitespace-pre-wrap wrap-break-word text-primary-300/70"
                    data-cy="spell-free-effect-comment"
                >
                    {{ freeEffectComment }}
                </p>
                <div
                    v-else-if="!activeDegreeHasRows && !hasSummaryChips"
                    class="rounded-box border border-dashed border-base-300 bg-base-200/30 p-4 text-sm text-primary-400"
                >
                    <p class="font-medium text-primary-300">Aucun effet renseigné</p>
                    <p class="mt-1 text-xs leading-relaxed">
                        Pas de degré ni de texte libre. Ajoutez des degrés depuis l’édition du sort, ou
                        saisissez un résumé dans le champ description des effets.
                    </p>
                </div>
            </div>
        </section>

        <div v-if="technicalFields.length > 0 || userCanEditFields.length > 0" class="pt-3 border-t border-base-300">
            <div v-if="technicalFields.length > 0" class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
                <template v-for="fieldKey in technicalFields" :key="fieldKey">
                    <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                        <div class="inline-flex items-center gap-2 min-w-0">
                            <Icon
                                :source="getFieldIcon(fieldKey)"
                                :alt="getFieldLabel(fieldKey)"
                                size="xs"
                                class="text-primary-300 flex-shrink-0"
                                :style="getFieldIconStyle(fieldKey)"
                            />
                            <span class="uppercase tracking-wide text-primary-300">{{ getFieldLabel(fieldKey) }}</span>
                            <span class="min-w-0 break-words">
                                <CellRenderer :cell="asTextCell(getCell(fieldKey))" ui-color="primary" />
                            </span>
                        </div>
                    </Tooltip>
                </template>
            </div>

            <div v-if="userCanEditFields.length > 0" class="mt-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-primary-300 mb-2">Paramètres</div>
                <div class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
                    <template v-for="fieldKey in userCanEditFields" :key="fieldKey">
                        <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                            <div class="inline-flex items-center gap-2 min-w-0">
                                <Icon
                                    :source="getFieldIcon(fieldKey)"
                                    :alt="getFieldLabel(fieldKey)"
                                    size="xs"
                                    class="text-primary-300 flex-shrink-0"
                                    :style="getFieldIconStyle(fieldKey)"
                                />
                                <span class="uppercase tracking-wide text-primary-300">{{ getFieldLabel(fieldKey) }}</span>
                                <span class="min-w-0 break-words">
                                    <template v-if="fieldKey === 'auto_update'">
                                        <Icon
                                            v-if="autoUpdateValue !== null"
                                            :source="autoUpdateValue ? 'fa-solid fa-check' : 'fa-solid fa-xmark'"
                                            :alt="autoUpdateValue ? 'Oui' : 'Non'"
                                            size="sm"
                                            :class="autoUpdateValue ? 'text-success-800' : 'text-error-800'"
                                        />
                                        <span v-else>—</span>
                                    </template>
                                    <template v-else>
                                        <Badge
                                            :color="getBadgeColor(fieldKey)"
                                            :auto-label="getBadgeAutoParams(fieldKey).autoLabel"
                                            :auto-scheme="getBadgeAutoParams(fieldKey).autoScheme"
                                            :auto-tone="getBadgeAutoParams(fieldKey).autoTone"
                                            size="sm"
                                        >
                                            <CellRenderer :cell="asTextCell(getCell(fieldKey))" ui-color="primary" />
                                        </Badge>
                                    </template>
                                </span>
                            </div>
                        </Tooltip>
                    </template>
                </div>
            </div>
        </div>
    </div>
    <EntityDofusdbRefreshPanel
        :open="refreshConfirm.open"
        :loading="refreshConfirm.loading"
        :applying="refreshConfirm.applying"
        :preview="refreshConfirm.preview"
        :error="refreshConfirm.error"
        :playable="refreshConfirm.playable"
        :entity-label="refreshConfirm.entityLabel"
        :entity-type="refreshConfirm.entityType"
        :show-dofusdb="refreshConfirm.showDofusdb"
        :show-ai="refreshConfirm.showAi"
        :show-json="refreshConfirm.showJson"
        :ai-brief="refreshConfirm.aiBrief"
        :ai-submitting="refreshConfirm.aiSubmitting"
        :ai-error="refreshConfirm.aiError"
        :ai-success="refreshConfirm.aiSuccess"
        :ai-estimate="refreshConfirm.aiEstimate"
        :ai-usage="refreshConfirm.aiUsage"
        :ai-action-label="refreshConfirm.aiActionLabel"
        :diff="refreshConfirm.diff"
        :diff-busy="refreshConfirm.diffBusy"
        @confirm="confirmPendingRefresh"
        @save-diff="confirmUpdateDiffSave"
        @restore-diff="confirmUpdateDiffRestore"
        @close="cancelPendingRefresh"
        @update:ai-brief="(v) => (refreshConfirm.aiBrief = v)"
        @convert="submitAiConvert"
        @inject="submitAiInject"
    />
</template>

<style scoped>
.entity-radius-box {
    border-radius: var(--radius-box, 0.1rem);
}
</style>
