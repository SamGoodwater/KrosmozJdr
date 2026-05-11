<script setup>
/**
 * CapabilityViewMinimal — Vue Minimal pour Capability
 *
 * @description
 * Méta alignée sur les sorts : {@link CapabilityMinimalUsageMetaRow} (PA, PO, portée modulable, incantation/rituel, durée/relance, élément & Wakfu/Physique au survol).
 * Description générale : italique, discrète, visible surtout au survol (compact + ligne d’overlay hover/extended).
 * Effets : bloc mis en avant ; 3 lignes puis troncature, détail complet au survol du bloc.
 *
 * @props {Capability} capability - Instance du modèle Capability
 */
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import CharacteristicEffectsGrid from "@/Pages/Molecules/data-display/CharacteristicEffectsGrid.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import CapabilityMinimalUsageMetaRow from "@/Pages/Molecules/entity/capability/CapabilityMinimalUsageMetaRow.vue";
import { buildCharacteristicEffectCell } from "@/Composables/entity/useCharacteristicEffectFormatter";
import { sanitizeHtml } from "@/Utils/security/sanitizeHtml";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getCapabilityFieldDescriptors } from "@/Entities/capability/capability-descriptors";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
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
    displayMode: {
        type: String,
        default: "hover",
        validator: (v) => ["compact", "hover", "extended"].includes(v),
    },
    tableMeta: {
        type: Object,
        default: () => ({}),
    },
    characteristicRuntime: { type: Object, default: null },
});

provideCharacteristicRuntime(computed(() => props.characteristicRuntime));

const emit = defineEmits(["edit", "view", "delete", "action"]);

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

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf ?? desc?.visibleIf;
    if (typeof visibleIf === "function") {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch {
            return false;
        }
    }
    return true;
};

const entity = computed(() => props.capability);

const levelValue = computed(() => {
    const lv = entity.value?.level ?? entity.value?._data?.level;
    if (lv == null || lv === "") return null;
    const n = Number(lv);
    return Number.isFinite(n) ? n : null;
});

const imageUrl = computed(() => {
    const u = entity.value?.image ?? entity.value?._data?.image;
    return u && String(u).trim() ? String(u) : null;
});

const effectItems = computed(() => {
    const cell = buildCharacteristicEffectCell({
        rawValues: [entity.value?.effect ?? entity.value?._data?.effect],
        options: {},
        sourceGroups: ["capability", "spell", "item", "panoply"],
        size: "sm",
    });
    return cell?.type === "chips" ? cell.params?.items || [] : [];
});

const descriptionFull = computed(() => {
    const d = entity.value?.description ?? entity.value?._data?.description;
    return d && String(d).trim() ? String(d) : "";
});

const linkedConditions = computed(() => {
    const raw = entity.value?.conditions ?? entity.value?._data?.conditions ?? [];
    return Array.isArray(raw) ? raw : [];
});

const hasLinkedConditions = computed(() => linkedConditions.value.length > 0);

/** Texte d’effets (champ `effect`) : extrait pour tooltips / fallback. */
const effectPlainText = computed(() => {
    const raw = entity.value?.effect ?? entity.value?._data?.effect;
    if (raw === null || raw === undefined || String(raw).trim() === "") return "";
    const stripped = String(raw)
        .replace(/<[^>]+>/g, " ")
        .replace(/\s+/g, " ")
        .trim();
    return stripped;
});

const hasEffectText = computed(() => Boolean(effectPlainText.value));

/** Effets HTML sanitizés. */
const effectHtmlSafe = computed(() => {
    const raw = entity.value?.effect ?? entity.value?._data?.effect;
    if (raw === null || raw === undefined || String(raw).trim() === "") return "";
    return sanitizeHtml(String(raw));
});

const showHref = computed(() =>
    entity.value?.id ? route("entities.capabilities.show", { capability: entity.value.id }) : null
);

/**
 * En `compact`, pas d’overlay : la description reste discrète jusqu’au survol de la carte (`group`).
 * En `hover` / `extended`, la description est dans le panneau étendu (déjà conditionné par le mode carte).
 */
const showDescriptionInCompactSlot = computed(() => props.displayMode === "compact");

const handleAction = async (actionKey) => {
    const capabilityId = entity.value?.id;
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
        case "delete":
            emit("delete", props.capability);
            break;
        default:
            emit("action", actionKey, props.capability);
    }
};
</script>

<template>
    <EntityMinimalCard :display-mode="displayMode" pinned-entity-type="capabilities" :pinned-entity-id="entity?.id">
        <template #compact>
            <div
                data-cy="entity-minimal-card-compact"
                class="relative p-2 flex flex-col gap-1.5 transition-colors"
            >
                <div class="flex gap-2">
                    <div
                        class="w-14 h-14 shrink-0 rounded overflow-hidden bg-base-200 flex items-center justify-center"
                    >
                        <Image
                            v-if="imageUrl"
                            :source="imageUrl"
                            :alt="entity?.name ?? 'Capacité'"
                            fit="contain"
                            class="h-full w-full"
                        />
                        <Icon v-else source="fa-solid fa-bolt" alt="" size="xs" class="text-base-content/40" />
                    </div>
                    <div class="flex-1 min-w-0 flex flex-col gap-1 pl-0.5">
                        <div class="flex items-center gap-1.5">
                            <LevelBadge v-if="levelValue != null" :level="levelValue" size="xs" class="shrink-0" />
                            <div class="min-w-0 flex-1">
                                <Route
                                    v-if="showHref"
                                    :href="showHref"
                                    color="neutral"
                                    class="font-semibold truncate block text-sm text-base-content hover:text-base-content no-underline"
                                >
                                    {{ entity?.name ?? "—" }}
                                </Route>
                                <span v-else class="font-semibold truncate block text-sm">
                                    {{ entity?.name ?? "—" }}
                                </span>
                            </div>
                            <div v-if="showActions" data-entity-actions class="shrink-0" @click.stop>
                                <EntityActions
                                    entity-type="capabilities"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="['state', 'pin', 'favorite', 'copy-link', 'quick-view', 'quick-edit']"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <CapabilityMinimalUsageMetaRow
                            :entity="entity"
                            :descriptors="descriptors"
                            :table-meta="tableMeta"
                            :can-show-field="canShowField"
                            property-size="xs"
                            row-class="gap-1.5 text-xs"
                        />
                        <p
                            v-if="showDescriptionInCompactSlot && descriptionFull"
                            class="text-[11px] leading-snug italic text-base-content/45 max-h-0 opacity-0 overflow-hidden transition-all duration-200 ease-out group-hover:max-h-32 group-hover:opacity-100 group-hover:mt-0.5"
                            :title="descriptionFull"
                        >
                            {{ descriptionFull }}
                        </p>
                    </div>
                </div>
                <ConditionBadges
                    v-if="hasLinkedConditions"
                    :conditions="linkedConditions"
                    size="xs"
                />
                <div
                    v-if="hasEffectText"
                    class="group/effect w-full pt-1.5 mt-1 border-glass-t-sm bg-primary/5 rounded-md px-1.5 py-1"
                >
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-primary-300/95 mb-0.5">
                        Effets
                    </p>
                    <!-- eslint-disable vue/no-v-html -- éditeur riche, HTML sanitizé (sanitizeHtml) -->
                    <article
                        v-if="effectHtmlSafe"
                        class="prose prose-sm prose-invert max-w-none text-[11px] leading-snug text-base-content capability-minimal-effect-prose line-clamp-3 group-hover/effect:line-clamp-none"
                        v-html="effectHtmlSafe"
                    />
                    <!-- eslint-enable vue/no-v-html -->
                    <p
                        v-else
                        class="text-[11px] leading-snug text-base-content line-clamp-3 group-hover/effect:line-clamp-none wrap-break-word"
                    >
                        {{ effectPlainText }}
                    </p>
                </div>
                <div
                    v-if="effectItems.length > 0"
                    class="w-full pt-1.5 mt-1 border-glass-t-sm"
                >
                    <CharacteristicEffectsGrid :items="effectItems" label-mode="icon-only" />
                </div>
            </div>
        </template>
        <template #expanded>
            <div
                data-cy="entity-minimal-card-expanded"
                class="relative p-2 flex flex-col gap-1.5 transition-colors"
            >
                <div class="flex gap-2">
                    <div
                        class="w-14 h-14 shrink-0 rounded overflow-hidden bg-base-200 flex items-center justify-center"
                    >
                        <Image
                            v-if="imageUrl"
                            :source="imageUrl"
                            :alt="entity?.name ?? 'Capacité'"
                            fit="contain"
                            class="h-full w-full"
                        />
                        <Icon v-else source="fa-solid fa-bolt" alt="" size="xs" class="text-base-content/40" />
                    </div>
                    <div class="flex-1 min-w-0 flex flex-col gap-1 pl-0.5">
                        <div class="flex items-center gap-1.5">
                            <LevelBadge v-if="levelValue != null" :level="levelValue" size="xs" class="shrink-0" />
                            <div class="min-w-0 flex-1">
                                <Route
                                    v-if="showHref"
                                    :href="showHref"
                                    color="neutral"
                                    class="font-semibold truncate block text-sm text-base-content hover:text-base-content no-underline"
                                >
                                    {{ entity?.name ?? "—" }}
                                </Route>
                                <span v-else class="font-semibold truncate block text-sm">
                                    {{ entity?.name ?? "—" }}
                                </span>
                            </div>
                            <div v-if="showActions" data-entity-actions class="shrink-0" @click.stop>
                                <EntityActions
                                    entity-type="capabilities"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="['state', 'pin', 'favorite', 'copy-link', 'quick-view', 'quick-edit']"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <CapabilityMinimalUsageMetaRow
                            :entity="entity"
                            :descriptors="descriptors"
                            :table-meta="tableMeta"
                            :can-show-field="canShowField"
                            property-size="xs"
                            row-class="gap-1.5 text-xs"
                        />
                        <p
                            v-if="descriptionFull"
                            :class="
                                displayMode === 'extended'
                                    ? 'text-[11px] leading-snug italic text-base-content/55 mt-0.5'
                                    : 'text-[11px] leading-snug italic text-base-content/45 max-h-0 opacity-0 overflow-hidden transition-all duration-200 ease-out group-hover:max-h-40 group-hover:opacity-100'
                            "
                            :title="descriptionFull"
                        >
                            {{ descriptionFull }}
                        </p>
                    </div>
                </div>
                <div
                    v-if="hasEffectText"
                    class="group/effect w-full space-y-1 pt-1.5 mt-1 border-glass-t-sm bg-primary/5 rounded-md px-1.5 py-1.5"
                >
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-primary-300/95">
                        Effets
                    </p>
                    <!-- eslint-disable vue/no-v-html -- éditeur riche, HTML sanitizé (sanitizeHtml) -->
                    <article
                        v-if="effectHtmlSafe"
                        class="prose prose-sm prose-invert max-w-none text-[11px] leading-snug text-base-content/95 capability-minimal-effect-prose line-clamp-3 group-hover/effect:line-clamp-none"
                        v-html="effectHtmlSafe"
                    />
                    <!-- eslint-enable vue/no-v-html -->
                    <p
                        v-else
                        class="text-[11px] leading-snug text-base-content line-clamp-3 group-hover/effect:line-clamp-none wrap-break-word"
                    >
                        {{ effectPlainText }}
                    </p>
                </div>
                <div
                    v-if="effectItems.length > 0"
                    class="w-full pt-1.5 mt-1 border-glass-t-sm"
                >
                    <CharacteristicEffectsGrid :items="effectItems" label-mode="icon-only" />
                </div>
            </div>
        </template>
    </EntityMinimalCard>
</template>
