<script setup>
/**
 * CapabilityViewFull — Vue Full pour Capability
 *
 * @description
 * Alignée sur SpellViewFull / PanoplyViewFull : EntityViewHeader, métas, grille détail, paramètres niveaux.
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

const headlineFields = computed(() => ["level"].filter(canShowField));

const metaFields = computed(() =>
    [
        "is_passive",
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
    ]
        .filter(canShowField)
        .filter((k) => !headlineFields.value.includes(k))
);

const displayMetaFields = computed(() => [...headlineFields.value, ...metaFields.value]);

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
    <div class="space-y-6">
        <EntityViewHeader :mode="headerMode">
            <template #media>
                <div class="group relative w-44 h-44 md:w-64 md:h-64 lg:w-72 lg:h-72">
                    <div
                        v-if="headlineFields.length > 0"
                        class="absolute top-2 right-2 z-20 flex flex-wrap gap-1 justify-end max-w-[75%] transition-opacity duration-150 group-hover:opacity-0"
                    >
                        <template v-for="fieldKey in headlineFields" :key="fieldKey">
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
                    </div>

                    <ImageViewer
                        v-if="mediaSrc"
                        :source="mediaSrc"
                        :alt="capability.name || 'Capacité'"
                        :caption="capability.name || ''"
                        preload="hover"
                        :image-props="{
                            size: 'xl',
                            rounded: 'lg',
                            fit: 'cover',
                            class: 'w-full h-full',
                        }"
                    />

                    <div v-else class="w-full h-full flex items-center justify-center bg-base-200 entity-radius-box">
                        <Icon source="fa-solid fa-bolt" :alt="capability.name" size="xl" />
                    </div>
                </div>
            </template>

            <template #title>
                <component :is="titleTag" class="text-2xl font-bold text-primary-100 wrap-break-word">
                    {{ capability.name }}
                </component>
            </template>

            <template #subtitle>
                <p v-if="capability.description" class="text-primary-300 mt-2 wrap-break-word">
                    {{ capability.description }}
                </p>
            </template>

            <template #mainInfos>
                <div v-if="displayMetaFields.length > 0" class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    <EntityPropertyDisplay
                        v-for="fieldKey in displayMetaFields"
                        :key="fieldKey"
                        :field-key="fieldKey"
                        :entity="capability"
                        entity-type="capability"
                        display-mode="extended"
                        :descriptors="descriptors"
                        :table-meta="tableMeta"
                        :variant="fieldKey === 'is_passive' ? 'icon' : 'inline'"
                        :hide-characteristic-icon="fieldKey === 'po_editable'"
                        size="sm"
                        class="max-w-[18rem] whitespace-normal wrap-break-word"
                    />
                </div>
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

        <div
            v-if="invocationMonsters.length > 0"
            class="flex flex-wrap items-center gap-2 rounded-xl border border-base-300/60 bg-glass-2xl p-3 text-sm"
            style="--bg-color: var(--color-base-100)"
        >
            <span class="text-primary-300 font-semibold">Invocation :</span>
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
            <div v-if="specializationLinks.length > 0" class="flex flex-wrap gap-2 items-center">
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

        <section v-if="canShowField('effect')" class="space-y-3">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Effets</h3>
            <!-- eslint-disable-next-line vue/no-v-html -- contenu éditeur riche, sanitizé côté client -->
            <article v-if="effectHtml" class="prose prose-sm prose-invert max-w-none text-primary-100 capability-effect-prose" v-html="effectHtml" />
            <p v-else class="text-sm text-primary-400 italic">Aucun effet décrit (texte riche).</p>
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
