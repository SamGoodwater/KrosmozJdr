<script setup>
/**
 * CapabilityViewFull — Vue Full pour Capability
 *
 * @description
 * Alignée sur SpellViewFull : header dense, niveau unique sur l’image, usage étiqueté, effets si renseignés.
 *
 * @props {Capability} capability - Instance du modèle Capability
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from "vue";
import { Link, router } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import MonsterViewText from "@/Pages/Molecules/entity/monster/MonsterViewText.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityViewHeader from "@/Pages/Molecules/entity/shared/EntityViewHeader.vue";
import ImageViewer from "@/Pages/Molecules/data-display/ImageViewer.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { resolveEntityFieldUi, resolveEntityBadgeUi } from "@/Utils/Entity/entity-view-ui";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { useDownloadPdf } from "@/Composables/utils/useDownloadPdf";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getCapabilityFieldDescriptors } from "@/Entities/capability/capability-descriptors";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import { sanitizeHtml } from "@/Utils/security/sanitizeHtml";
import ConditionBadges from "@/Pages/Molecules/entity/condition/ConditionBadges.vue";
import CapabilityMinimalUsageMetaRow from "@/Pages/Molecules/entity/capability/CapabilityMinimalUsageMetaRow.vue";
import {
    ELEMENT_PRIMARY_LABELS,
    getElementIcon,
    getElementPrimaries,
    resolveEntityElementValue,
} from "@/Utils/Entity/Elements";

const props = defineProps({
    capability: {
        type: Object,
        required: true,
    },
    showActions: {
        type: Boolean,
        default: true,
    },
    inModal: {
        type: Boolean,
        default: false,
    },
    tableMeta: {
        type: Object,
        default: () => ({}),
    },
    characteristicRuntime: { type: Object, default: null },
    /** Balise du titre principal (h1 sur page fiche, h2 en modal). */
    titleTag: {
        type: String,
        default: "h2",
        validator: (v) => ["h1", "h2", "h3"].includes(v),
    },
});


const actionsContext = computed(() =>
    props.inModal
        ? { inPanel: false, inModal: true, surface: 'modal', viewMode: 'full', modalMode: 'view' }
        : { inPanel: false, inPage: true, surface: 'page', viewMode: 'full' },
);

const headerMode = computed(() => (props.inModal ? 'compact' : 'full'));

provideCharacteristicRuntime(computed(() => props.characteristicRuntime));

const emit = defineEmits([
    "edit",
    "copy-link",
    "download-pdf",
    "refresh",
    "view",
    "quick-view",
    "delete",
    "action",
]);

const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf("capability");
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can("capabilities", "viewAny"),
        createAny: permissions.can("capabilities", "createAny"),
        updateAny: permissions.can("capabilities", "updateAny"),
        deleteAny: permissions.can("capabilities", "deleteAny"),
        manageAny: permissions.can("capabilities", "manageAny"),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getCapabilityFieldDescriptors(ctx.value));

const mediaSrc = computed(() => {
    const c = props.capability;
    const u = c?.image ?? c?._data?.image;
    return u && String(u).trim() ? String(u) : "";
});

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf ?? desc?.visibleIf;
    if (typeof visibleIf === "function") {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn("[CapabilityViewFull] visibleIf failed for", fieldKey, e);
            return false;
        }
    }
    return true;
};

/** Niveau uniquement sur l’image (pas dans la grille méta). */
const showLevelBadgeOnImage = computed(() => canShowField("level"));

/** Chips d’identité hors coût/portée/timing (déjà dans UsageMetaRow). */
const identityMetaFields = computed(() =>
    ["is_passive", "is_magic", "powerful"].filter(canShowField),
);

const elementOverlay = computed(() => {
    const primaries = getElementPrimaries(resolveEntityElementValue(props.capability));
    if (!primaries.length) return null;
    const first = primaries[0];
    if (first === 0 && primaries.length === 1) return null;
    return {
        icon: getElementIcon(first),
        label: ELEMENT_PRIMARY_LABELS[first] ?? "",
    };
});

const userCanEditFields = computed(() => ["read_level", "write_level"].filter(canShowField));

const technicalFields = computed(() => ["created_by", "created_at", "updated_at"].filter(canShowField));

const specializationLinks = computed(() => {
    const raw = props.capability?.specializations ?? props.capability?._data?.specializations ?? [];
    const list = Array.isArray(raw) ? raw : [];
    return list
        .map((s) => ({
            id: s?.id,
            name: s?.name ?? `#${s?.id}`,
        }))
        .filter((x) => x.id != null);
});

const creatureLinks = computed(() => {
    const raw = props.capability?.creatures ?? props.capability?._data?.creatures ?? [];
    const list = Array.isArray(raw) ? raw : [];
    return list
        .map((c) => ({
            id: c?.id,
            name: c?.name ?? `#${c?.id}`,
        }))
        .filter((x) => x.id != null);
});

const invocationMonsters = computed(() => creatureLinks.value);

const linkedConditions = computed(() => {
    const raw = props.capability?.conditions ?? props.capability?._data?.conditions ?? [];
    return Array.isArray(raw) ? raw : [];
});

const hasLinkedConditions = computed(() => linkedConditions.value.length > 0);

/** Effets : HTML riche (pas d’éditeur d’effets structurés comme pour les sorts). */
const effectHtml = computed(() => {
    const raw = props.capability?.effect ?? props.capability?._data?.effect;
    if (raw === null || raw === undefined || String(raw).trim() === "") return "";
    return sanitizeHtml(String(raw));
});

const getFieldUi = (fieldKey) =>
    resolveEntityFieldUi({
        fieldKey,
        descriptors: descriptors.value,
        tableMeta: props.tableMeta,
        entityType: "capability",
    });

const getFieldLabel = (fieldKey) => getFieldUi(fieldKey).label;

const getFieldIcon = (fieldKey) => getFieldUi(fieldKey).icon;

const getFieldTooltip = (fieldKey) => getFieldUi(fieldKey).tooltip;

const getFieldIconStyle = (fieldKey) => {
    const color = getFieldUi(fieldKey).color;
    return color ? { color } : undefined;
};

const getCell = (fieldKey) => {
    return props.capability.toCell(fieldKey, {
        size: "lg",
        context: "extended",
    });
};

const getBadgeColor = (fieldKey) => {
    const colorMap = {
        level: "warning",
        read_level: "primary",
        write_level: "secondary",
        created_by: "neutral",
        created_at: "neutral",
        updated_at: "neutral",
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
    if (!cell) return { type: "text", value: "-", params: {} };
    const v = cell?.value;
    return {
        type: "text",
        value: v === null || typeof v === "undefined" || String(v) === "" ? "-" : String(v),
        params: cell?.params || {},
    };
};

const handleAction = async (actionKey) => {
    const capabilityId = props.capability.id;
    if (!capabilityId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.capabilities.show", { capability: capabilityId }));
            emit("view", props.capability);
            break;
        case "edit":
            router.visit(route("entities.capabilities.edit", { capability: capabilityId }));
            emit("edit", props.capability);
            break;
        
        case "copy-link": {
            const cfg = getEntityRouteConfig("capability");
            const url = resolveEntityRouteUrl("capability", "show", capabilityId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de la capacité copié !");
            }
            emit("copy-link", props.capability);
            break;
        }
        case "download-pdf":
            await downloadPdf(capabilityId);
            emit("download-pdf", props.capability);
            break;
        case "refresh":
            router.reload({ only: ["capabilities"] });
            emit("refresh", props.capability);
            break;
        case "delete":
            emit("delete", props.capability);
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
                    data-cy="capability-full-media"
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
                        v-if="mediaSrc"
                        :source="mediaSrc"
                        :alt="capability.name || 'Capacité'"
                        :caption="capability.name || ''"
                        preload="hover"
                        :image-props="{
                            size: 'lg',
                            rounded: 'lg',
                            fit: 'cover',
                            class: 'w-full h-full',
                        }"
                    />

                    <div v-else class="flex size-full items-center justify-center bg-base-200 entity-radius-box">
                        <Icon source="fa-solid fa-bolt" :alt="capability.name" size="lg" />
                    </div>

                    <span
                        v-if="elementOverlay"
                        class="pointer-events-none absolute top-1.5 right-1.5 z-20 inline-flex size-7 items-center justify-center rounded-full bg-base-100/90 shadow-sm ring-1 ring-base-content/15"
                        data-cy="capability-full-element-overlay"
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
                    {{ capability.name }}
                </component>
            </template>

            <template #mainInfos>
                <div v-if="identityMetaFields.length > 0" class="flex flex-wrap items-center gap-x-3 gap-y-1.5">
                    <EntityPropertyDisplay
                        v-for="fieldKey in identityMetaFields"
                        :key="fieldKey"
                        :field-key="fieldKey"
                        :entity="capability"
                        entity-type="capability"
                        display-mode="extended"
                        :descriptors="descriptors"
                        :table-meta="tableMeta"
                        :variant="fieldKey === 'is_passive' ? 'icon' : 'inline'"
                        size="sm"
                        class="max-w-[18rem] whitespace-normal break-words"
                    />
                </div>
            </template>

            <template v-if="capability.description" #subtitle>
                <p class="text-sm leading-relaxed whitespace-pre-wrap break-words text-primary-200 md:text-base">
                    {{ capability.description }}
                </p>
            </template>

            <template #actions>
                <div v-if="showActions">
                    <EntityActions
                        entity-type="capabilities"
                        :entity="capability"
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
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Utilisation</h3>
            <div class="min-w-0 space-y-2 p-3 bg-base-200 entity-radius-box">
                <CapabilityMinimalUsageMetaRow
                    :entity="capability"
                    :descriptors="descriptors"
                    :table-meta="tableMeta"
                    :can-show-field="canShowField"
                    property-size="sm"
                    row-class="text-sm gap-x-3 gap-y-2"
                    hover-inner-gap-class="gap-1.5"
                />
            </div>
        </section>

        <div
            v-if="invocationMonsters.length > 0"
            class="flex flex-wrap items-center gap-2 rounded-xl border border-base-300/60 bg-glass-2xl p-3 text-sm"
            style="--bg-color: var(--color-base-100)"
        >
            <span class="text-xs font-semibold uppercase tracking-wide text-primary-300">Invocation</span>
            <MonsterViewText
                v-for="monster in invocationMonsters"
                :key="monster.id ?? monster.name"
                :monster="monster"
                :table-meta="tableMeta"
                :characteristic-runtime="characteristicRuntime"
            />
        </div>

        <section
            v-if="specializationLinks.length > 0"
            class="space-y-2 rounded-xl border border-base-300/60 bg-glass-2xl p-4"
            style="--bg-color: var(--color-base-100)"
        >
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Relations</h3>
            <div class="flex flex-wrap gap-2 items-center">
                <span class="text-xs text-primary-400 shrink-0">Spécialisations</span>
                <Link
                    v-for="s in specializationLinks"
                    :key="`sp-${s.id}`"
                    :href="route('entities.specializations.show', { specialization: s.id })"
                    class="badge badge-sm badge-outline border-primary/40 text-primary-200 hover:border-primary"
                >
                    {{ s.name }}
                </Link>
            </div>
        </section>

        <section v-if="effectHtml" class="space-y-3">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Effets</h3>
            <!-- eslint-disable-next-line vue/no-v-html -- contenu éditeur riche, sanitizé côté client -->
            <article class="prose prose-sm prose-invert max-w-none text-primary-100 capability-effect-prose" v-html="effectHtml" />
        </section>

        <section v-if="hasLinkedConditions" class="space-y-3">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Conditions</h3>
            <ConditionBadges :conditions="linkedConditions" size="sm" />
        </section>

        <div v-if="technicalFields.length > 0 || userCanEditFields.length > 0" class="pt-3 border-t border-base-300">
            <div v-if="technicalFields.length > 0" class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
                <template v-for="fieldKey in technicalFields" :key="fieldKey">
                    <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                        <div class="inline-flex items-center gap-2 min-w-0">
                            <Icon
                                :source="getFieldIcon(fieldKey)"
                                size="xs"
                                class="text-primary-300 shrink-0"
                                :style="getFieldIconStyle(fieldKey)"
                            />
                            <span class="uppercase tracking-wide text-primary-300">{{ getFieldLabel(fieldKey) }}</span>
                            <span class="min-w-0 wrap-break-word">
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
                                    class="text-primary-300 shrink-0"
                                    :style="getFieldIconStyle(fieldKey)"
                                />
                                <span class="uppercase tracking-wide text-primary-300">{{ getFieldLabel(fieldKey) }}</span>
                                <span class="min-w-0 wrap-break-word">
                                    <Badge
                                        :color="getBadgeColor(fieldKey)"
                                        :auto-label="getBadgeAutoParams(fieldKey).autoLabel"
                                        :auto-scheme="getBadgeAutoParams(fieldKey).autoScheme"
                                        :auto-tone="getBadgeAutoParams(fieldKey).autoTone"
                                        size="sm"
                                    >
                                        <CellRenderer :cell="asTextCell(getCell(fieldKey))" ui-color="primary" />
                                    </Badge>
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

:deep(.capability-effect-prose a) {
    color: var(--color-primary-300, inherit);
    text-decoration: underline;
}
</style>
