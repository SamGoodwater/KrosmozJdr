<script setup>
/**
 * SpellViewFull — Vue Full pour Spell
 *
 * @description
 * Fiche sort : en-tête (image, niveau, nom), identité (types, catégorie, élément, magie/physique, rituel, réaction),
 * description, utilisation, résolution, effets structurés (journal), puis métadonnées techniques.
 *
 * @props {Spell} spell - Instance du modèle Spell
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from 'vue';
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
import { resolveEntityFieldUi, resolveEntityBadgeUi } from '@/Utils/Entity/entity-view-ui';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { getSpellFieldDescriptors } from '@/Entities/spell/spell-descriptors';
import { getByCharacteristicKey } from '@/Composables/store/useCharacteristicsStore';
import { getCharacteristicColorStyle, resolveDef } from '@/Composables/entity/useCharacteristicDisplay';
import { resolveSpellCharacteristicKey } from '@/Composables/entity/useSpellAbilityCharacteristic';
import { provideCharacteristicRuntime } from '@/Composables/entity/characteristicRuntimeContext';
import { PROPERTY_DISPLAY_MODES } from '@/Utils/Entity/Constants';

const RESOLUTION_LABELS = Object.freeze({
    attack_roll: "Jet d'attaque (vs CA)",
    saving_throw: 'Jet de sauvegarde',
    auto_success: 'Réussite automatique',
});

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

const headerMode = computed(() => (props.inModal ? 'compact' : 'full'));

provideCharacteristicRuntime(computed(() => props.characteristicRuntime));

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'quick-edit', 'delete', 'action']);

const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf('spell');
const permissions = usePermissions();

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
    const v = props.spell?.auto_update ?? props.spell?._data?.auto_update;
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

const identityFieldKeys = computed(() =>
    ['spell_types', 'category', 'element'].filter(canShowField),
);

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
    props.spell.toCell(fieldKey, {
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
    resolveDef('is_magic_spell', props.spell?.isMagic, { sourceGroups: ['spell'] }),
);

const effectsDefinitions = computed(() =>
    Array.isArray(props.spell?.effectsDefinitions) ? props.spell.effectsDefinitions : [],
);

const resolutionLabel = computed(() => RESOLUTION_LABELS[props.spell.resolutionMode] || props.spell.resolutionMode);

const attackCharKeyResolved = computed(() =>
    resolveSpellCharacteristicKey(props.spell.attackCharacteristicKey, 'attack'),
);

const saveCharKeyResolved = computed(() =>
    resolveSpellCharacteristicKey(props.spell.saveCharacteristicKey, 'save'),
);

const attackCharDef = computed(() => {
    const k = attackCharKeyResolved.value;
    return k ? getByCharacteristicKey('spell', k) : null;
});

const saveCharDef = computed(() => {
    const k = saveCharKeyResolved.value;
    return k ? getByCharacteristicKey('spell', k) : null;
});

const poEditableDef = computed(() => {
    const val = props.spell?._data?.po_editable ?? props.spell?.poEditable;
    return resolveDef('range_editable_spell', val, { sourceGroups: ['spell'] });
});

const ritualDef = computed(() =>
    resolveDef('ritual_available_spell', props.spell?.isRitual, { sourceGroups: ['spell'] }),
);

const showRitualBadge = computed(() => props.spell?.isRitual === true);

const ritualTooltipText = computed(() => {
    const d = ritualDef.value;
    if (d?._resolvedSubtitle) return d._resolvedSubtitle;
    const t = d?.helper || (Array.isArray(d?.descriptions) ? d.descriptions.join(' ') : d?.descriptions) || '';
    return t || 'Ce sort peut être lancé comme un rituel (incantation prolongée).';
});

const reactionDef = computed(() => {
    const val = typeof props.spell?.allowsReaction === 'boolean'
        ? props.spell.allowsReaction
        : Boolean(props.spell?._data?.allows_reaction);
    return resolveDef('allows_reaction_spell', val, { sourceGroups: ['spell'] });
});

const showReactionBadge = computed(() => {
    const s = props.spell;
    if (typeof s?.allowsReaction === 'boolean') {
        return s.allowsReaction === true;
    }
    return Boolean(s?._data?.allows_reaction);
});

/** Contenu structuré pour l’infobulle « réaction » (règles PA / round). */
const reactionTooltipBlocks = computed(() => {
    const d = reactionDef.value;
    const title = d?.short_name || d?.name || 'Réaction';
    const lines = [];
    if (d?._resolvedSubtitle) {
        lines.push(d._resolvedSubtitle);
    }
    const helper = typeof d?.helper === 'string' && d.helper.trim() !== '' ? d.helper.trim() : '';
    if (helper && helper !== d?._resolvedSubtitle) {
        lines.push(helper);
    }
    const descRaw = d?.descriptions;
    const desc =
        Array.isArray(descRaw) ? descRaw.map((p) => String(p).trim()).filter(Boolean).join(' ') : String(descRaw || '').trim();
    if (desc && desc !== helper && desc !== d?._resolvedSubtitle) {
        lines.push(desc);
    }
    if (lines.length === 0) {
        lines.push(
            'Ce sort peut être lancé en réaction pendant un round de combat.',
            'Chaque créature dispose d’une réaction par round, utilisable à tout moment du round.',
            'Les PA dépensés pour une réaction ne sont pas réattribués au début du tour suivant.',
        );
    }
    return { title, lines };
});

function characteristicTooltipText(meta) {
    if (!meta) return '';
    return (
        meta.helper ||
        (Array.isArray(meta.descriptions) ? meta.descriptions.join(' ') : meta.descriptions) ||
        ''
    );
}

const usageFieldKeys = Object.freeze([
    'pa',
    'po_range',
    'duration',
    'cast_per_turn',
    'cast_per_target',
    'sight_line',
    'cast_in_line',
    'cast_in_diagonal',
    'target_type',
    'max_stack',
    'global_cooldown',
    'number_between_two_cast',
    'po_editable',
    'casting_time',
]);

const handleAction = async (actionKey) => {
    const spellId = props.spell.id;
    if (!spellId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.spells.show', { spell: spellId }));
            emit('view', props.spell);
            break;
        case 'edit':
            router.visit(route('entities.spells.edit', { spell: spellId }));
            emit('edit', props.spell);
            break;
        case 'quick-edit':
            emit('quick-edit', props.spell);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('spell');
            const url = resolveEntityRouteUrl('spell', 'show', spellId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, 'Lien du sort copié !');
            }
            emit('copy-link', props.spell);
            break;
        }
        case 'download-pdf':
            await downloadPdf(spellId);
            emit('download-pdf', props.spell);
            break;
        case 'refresh':
            router.reload({ only: ['spells'] });
            emit('refresh', props.spell);
            break;
        case 'delete':
            emit('delete', props.spell);
            break;
    }
};
</script>

<template>
    <div class="space-y-8">
        <EntityViewHeader :mode="headerMode">
            <template #media>
                <div class="group relative w-44 h-44 md:w-64 md:h-64 lg:w-72 lg:h-72">
                    <div class="absolute top-2 right-2 z-20 transition-opacity duration-150 group-hover:opacity-0">
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
                            size: 'xl',
                            rounded: 'lg',
                            fit: 'cover',
                            class: 'w-full h-full',
                        }"
                    />

                    <div v-else class="w-full h-full flex items-center justify-center bg-base-200 entity-radius-box">
                        <Icon source="fa-solid fa-wand-magic-sparkles" :alt="spell.name" size="xl" />
                    </div>
                </div>
            </template>

            <template #title>
                <component :is="titleTag" class="text-2xl font-bold text-primary-100 break-words">
                    {{ spell.name }}
                </component>
            </template>

            <template #subtitle />

            <template #mainInfos>
                <div v-if="identityFieldKeys.length > 0" class="mt-3 flex flex-wrap gap-3 items-start">
                    <EntityPropertyDisplay
                        v-for="fieldKey in identityFieldKeys"
                        :key="fieldKey"
                        :field-key="fieldKey"
                        :entity="spell"
                        entity-type="spell"
                        display-mode="extended"
                        :descriptors="descriptors"
                        :table-meta="tableMeta"
                        size="sm"
                        class="max-w-[20rem] whitespace-normal break-words"
                    />
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-4">
                    <Tooltip
                        v-if="typeof spell.isMagic === 'boolean' && magicDef"
                        :content="magicDef._resolvedSubtitle || characteristicTooltipText(magicDef) || 'Orientation du sort.'"
                        placement="top"
                    >
                        <span
                            class="inline-flex items-center gap-2 text-sm font-medium cursor-default"
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
                            class="inline-flex items-center gap-2 text-sm cursor-default"
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

                    <Tooltip v-if="showReactionBadge" placement="top">
                        <template #content>
                            <div class="max-w-xs text-left text-sm space-y-2 text-base-content">
                                <p class="font-semibold leading-snug">{{ reactionTooltipBlocks.title }}</p>
                                <p v-for="(line, idx) in reactionTooltipBlocks.lines" :key="idx" class="leading-snug">
                                    {{ line }}
                                </p>
                            </div>
                        </template>
                        <span
                            class="inline-flex items-center gap-2 text-sm font-medium cursor-default"
                            :style="(reactionDef?._resolvedColor || reactionDef?.color) ? getCharacteristicColorStyle(reactionDef._resolvedColor || reactionDef.color) : undefined"
                        >
                            <Icon
                                :source="reactionDef?._resolvedIcon || reactionDef?.icon || 'icons/caracteristics/is_reaction.webp'"
                                :alt="reactionDef?.short_name || 'Réaction'"
                                size="sm"
                            />
                            <span>{{ reactionDef?.short_name || reactionDef?.name || 'Réaction' }}</span>
                        </span>
                    </Tooltip>

                </div>
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
                        :context="{ inPanel: false, inPage: true }"
                        @action="handleAction"
                    />
                </div>
            </template>
        </EntityViewHeader>

        <section v-if="spell.description" class="space-y-2">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Description</h3>
            <p class="text-primary-200 whitespace-pre-wrap break-words leading-relaxed">{{ spell.description }}</p>
        </section>

        <section class="space-y-3">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Utilisation</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div
                    v-for="fieldKey in usageFieldKeys"
                    :key="fieldKey"
                    class="p-3 bg-base-200 entity-radius-box min-w-0"
                >
                    <EntityPropertyDisplay
                        :field-key="fieldKey"
                        :entity="spell"
                        entity-type="spell"
                        :display-mode="PROPERTY_DISPLAY_MODES.extended"
                        :descriptors="descriptors"
                        :table-meta="tableMeta"
                        size="sm"
                        class="text-primary-100"
                    />
                </div>

                <div v-if="poEditableDef" class="p-3 bg-base-200 entity-radius-box min-w-0">
                    <EntityPropertyDisplay
                        field-key="po_editable"
                        :entity="spell"
                        entity-type="spell"
                        :display-mode="PROPERTY_DISPLAY_MODES.extended"
                        :descriptors="descriptors"
                        :table-meta="tableMeta"
                        size="sm"
                        class="text-primary-100"
                    />
                </div>
            </div>
        </section>

        <section class="space-y-3">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Résolution</h3>
            <div class="p-4 bg-base-200 entity-radius-box space-y-3">
                <p class="text-primary-100 font-medium">{{ resolutionLabel }}</p>

                <Tooltip
                    v-if="spell.resolutionMode === 'attack_roll' && attackCharDef"
                    :content="
                        characteristicTooltipText(attackCharDef) ||
                        'Caractéristique utilisée pour le jet d’attaque de ce sort.'
                    "
                    placement="top"
                >
                    <div
                        class="inline-flex flex-wrap items-center gap-2 text-sm text-primary-200 cursor-default"
                        :style="attackCharDef.color ? getCharacteristicColorStyle(attackCharDef.color) : undefined"
                    >
                        <Icon
                            v-if="attackCharDef.icon"
                            :source="attackCharDef.icon"
                            :alt="attackCharDef.short_name || ''"
                            size="sm"
                        />
                        <span>{{ attackCharDef.short_name || attackCharDef.name }}</span>
                    </div>
                </Tooltip>

                <div v-if="spell.resolutionMode === 'saving_throw'" class="space-y-2">
                    <Tooltip
                        v-if="saveCharDef"
                        :content="
                            characteristicTooltipText(saveCharDef) ||
                            'Caractéristique de sauvegarde ciblée par ce sort.'
                        "
                        placement="top"
                    >
                        <div
                            class="inline-flex flex-wrap items-center gap-2 text-sm text-primary-200 cursor-default"
                            :style="saveCharDef.color ? getCharacteristicColorStyle(saveCharDef.color) : undefined"
                        >
                            <Icon
                                v-if="saveCharDef.icon"
                                :source="saveCharDef.icon"
                                :alt="saveCharDef.short_name || ''"
                                size="sm"
                            />
                            <span>{{ saveCharDef.short_name || saveCharDef.name }}</span>
                        </div>
                    </Tooltip>
                    <p v-if="spell.saveDcFormula" class="text-sm text-primary-200 break-words">
                        <span class="text-primary-400 font-semibold">DD : </span>
                        <span class="font-mono">{{ spell.saveDcFormula }}</span>
                    </p>
                    <p v-if="spell.saveSuccessNote" class="text-sm text-primary-300 whitespace-pre-wrap break-words">
                        <span class="text-primary-400 font-semibold">Si réussite : </span>
                        {{ spell.saveSuccessNote }}
                    </p>
                </div>
            </div>
        </section>

        <section class="space-y-3">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Effets</h3>
            <SpellEffectsJournal
                v-if="effectsDefinitions.length > 0"
                :definitions="effectsDefinitions"
                sub-effect-layout="large"
            />
            <p
                v-else-if="spell.effect && String(spell.effect).trim() !== ''"
                class="text-sm text-primary-200 whitespace-pre-wrap wrap-break-word"
            >
                {{ spell.effect }}
            </p>
            <p v-else class="text-sm text-primary-400 italic">Aucun effet structuré ni texte associé.</p>
        </section>

        <div v-if="technicalFields.length > 0 || userCanEditFields.length > 0" class="pt-3 border-t border-base-300">
            <div v-if="technicalFields.length > 0" class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
                <template v-for="fieldKey in technicalFields" :key="fieldKey">
                    <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                        <div class="inline-flex items-center gap-2 min-w-0">
                            <Icon
                                :source="getFieldIcon(fieldKey)"
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
</template>

<style scoped>
.entity-radius-box {
    border-radius: var(--radius-box, 0.1rem);
}
</style>
