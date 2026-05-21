<script setup>
/**
 * SpellViewMinimal — Vue Minimal pour Spell
 *
 * @description
 * Effets : `resolveSpellEffectsDisplayCell` (résumé API + SpellEffectChips, ou fallback `effect`).
 * Méta : `SpellMinimalUsageMetaRow` (PA, PO + icônes portée/ligne de vue, lancers avec tooltip, élément au survol, types/catégorie au survol).
 * Résolution : ligne au-dessus des effets (`buildResolutionSummary`).
 * Invocations : portées par les sous-effets / chips (pas de bloc séparé).
 *
 * @props {Spell} spell - Instance du modèle Spell
 */
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import EntityStateBadge from "@/Pages/Atoms/data-display/EntityStateBadge.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import {
    resolveSpellEffectsDisplayCell,
    spellEffectsCellHasContent,
} from "@/Composables/entity/useSpellEffectsDisplayCell";
import { getSpellFieldDescriptors } from "@/Entities/spell/spell-descriptors";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import { spellTypesCellHasRenderableContent } from "@/Utils/Entity/spellTypeVisual.js";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import SpellMinimalUsageMetaRow from "@/Pages/Molecules/entity/spell/SpellMinimalUsageMetaRow.vue";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import { buildResolutionSummary } from "@/Utils/Entity/spellMinimalUsageDisplay";

const props = defineProps({
    spell: {
        type: Object,
        required: true,
    },
    showActions: {
        type: Boolean,
        default: true,
    },
    displayMode: {
        type: String,
        default: "extended",
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
const ctx = computed(() => ({
    capabilities: {
        viewAny: permissions.can("spells", "viewAny"),
        createAny: permissions.can("spells", "createAny"),
        updateAny: permissions.can("spells", "updateAny"),
        deleteAny: permissions.can("spells", "deleteAny"),
        manageAny: permissions.can("spells", "manageAny"),
    },
    meta: { capabilities: {} },
}));
const descriptors = computed(() => getSpellFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === "function") {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch {
            return false;
        }
    }
    return true;
};

const entity = computed(() => props.spell);

const cellOpts = () => ({ size: "xs", context: "minimal" });

const spellTypesCell = computed(() => entity.value?.toCell?.("spell_types", cellOpts()) ?? null);
const showSpellTypesCell = computed(() => spellTypesCellHasRenderableContent(spellTypesCell.value));

const levelValue = computed(() => {
    const lv = entity.value?.level ?? entity.value?._data?.level;
    if (lv == null || lv === "") return null;
    const n = Number(lv);
    return Number.isFinite(n) ? n : null;
});

const descriptionFull = computed(
    () => entity.value?.description ?? entity.value?._data?.description ?? ""
);

const effectDisplayCell = computed(() =>
    resolveSpellEffectsDisplayCell(entity.value, {
        size: "xs",
        context: "minimal",
        ctx: props.tableMeta,
        maxEffectRows: 3,
    }),
);
const hasEffects = computed(() => spellEffectsCellHasContent(effectDisplayCell.value));

const resolutionUsage = computed(() => buildResolutionSummary(entity.value));

const imageUrl = computed(() => {
    const u = entity.value?.image ?? entity.value?._data?.image;
    return u && String(u).trim() ? String(u) : null;
});

const showHref = computed(() =>
    entity.value?.id ? route("entities.spells.show", { spell: entity.value.id }) : null
);

/** Force le rafraîchissement de la pastille après PATCH état (mutation in-place sur l’instance). */
const stateTick = ref(0);
const entityState = computed(() => {
    void stateTick.value;
    return entity.value?.state ?? entity.value?._data?.state ?? null;
});

const entityActionsContext = computed(() => ({
    viewMode: props.displayMode === "compact" ? "compact" : "minimal",
    inMinimal: true,
}));

const handleAction = async (actionKey) => {
    const spellId = entity.value?.id;
    if (!spellId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.spells.show", { spell: spellId }));
            emit("view", props.spell);
            break;
        case "quick-view":
            emit("action", "quick-view", props.spell);
            break;
        case "edit":
            router.visit(route("entities.spells.edit", { spell: spellId }));
            emit("edit", props.spell);
            break;
        case "delete":
            emit("delete", props.spell);
            break;
        case "state":
            stateTick.value += 1;
            emit("action", actionKey, props.spell);
            break;
        default:
            emit("action", actionKey, props.spell);
    }
};
</script>

<template>
    <EntityMinimalCard :display-mode="displayMode" pinned-entity-type="spells" :pinned-entity-id="entity?.id">
        <template #compact>
            <div
                data-cy="entity-minimal-card-compact"
                class="relative p-2 flex flex-col gap-1.5 transition-colors"
            >
                <div class="flex gap-2">
                    <EntityThumb
                        size="compact"
                        :src="imageUrl || ''"
                        :label="entity?.name ?? 'Sort'"
                    />
                    <div class="flex-1 min-w-0 flex flex-col gap-1 pl-0.5">
                        <div class="flex items-center gap-1.5">
                            <LevelBadge v-if="levelValue != null" :level="levelValue" size="xs" class="shrink-0" />
                            <EntityStateBadge
                                v-if="entityState"
                                :state="entityState"
                                size="xs"
                                :show-label="false"
                                class="shrink-0"
                            />
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
                                    entity-type="spells"
                                    :entity="entity"
                                    :context="entityActionsContext"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="['state', 'pin', 'favorite', 'copy-link', 'quick-view', 'quick-edit']"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <SpellMinimalUsageMetaRow
                            :entity="entity"
                            :descriptors="descriptors"
                            :table-meta="tableMeta"
                            :can-show-field="canShowField"
                            :show-spell-types-cell="showSpellTypesCell"
                            property-size="xs"
                            row-class="gap-1.5 text-xs"
                        />
                    </div>
                </div>
                <div
                    v-if="hasEffects || resolutionUsage.show"
                    class="spell-effects-minimal w-full pt-1.5 mt-1 border-t border-base-300"
                >
                    <p
                        v-if="resolutionUsage.show"
                        class="mb-1 text-xs text-base-content/75"
                    >
                        {{ resolutionUsage.text }}
                    </p>
                    <CellRenderer
                        v-if="hasEffects"
                        :cell="effectDisplayCell"
                        ui-color="primary"
                        class="text-xs leading-snug [&_.inline-flex]:max-w-full [&_.inline-flex]:flex-wrap"
                    />
                </div>
            </div>
        </template>
        <template #expanded>
            <div
                data-cy="entity-minimal-card-expanded"
                class="relative p-2 flex flex-col gap-1.5 transition-colors"
            >
                <div class="flex gap-2">
                    <EntityThumb
                        size="compact"
                        :src="imageUrl || ''"
                        :label="entity?.name ?? 'Sort'"
                    />
                    <div class="flex-1 min-w-0 flex flex-col gap-1 pl-0.5">
                        <div class="flex items-center gap-1.5">
                            <LevelBadge v-if="levelValue != null" :level="levelValue" size="xs" class="shrink-0" />
                            <EntityStateBadge
                                v-if="entityState"
                                :state="entityState"
                                size="xs"
                                :show-label="false"
                                class="shrink-0"
                            />
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
                                    entity-type="spells"
                                    :entity="entity"
                                    :context="entityActionsContext"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="['state', 'pin', 'favorite', 'copy-link', 'quick-view', 'quick-edit']"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <SpellMinimalUsageMetaRow
                            :entity="entity"
                            :descriptors="descriptors"
                            :table-meta="tableMeta"
                            :can-show-field="canShowField"
                            :show-spell-types-cell="showSpellTypesCell"
                            property-size="xs"
                            row-class="gap-1.5 text-xs"
                        />
                        <p
                            v-if="descriptionFull"
                            class="text-xs text-base-content/80 line-clamp-3"
                            :title="descriptionFull"
                        >
                            {{ descriptionFull }}
                        </p>
                    </div>
                </div>
                <div
                    v-if="hasEffects || resolutionUsage.show"
                    class="spell-effects-minimal w-full pt-1.5 mt-1 border-t border-base-300"
                >
                    <p
                        v-if="resolutionUsage.show"
                        class="mb-1 text-xs text-base-content/75"
                    >
                        {{ resolutionUsage.text }}
                    </p>
                    <CellRenderer
                        v-if="hasEffects"
                        :cell="effectDisplayCell"
                        ui-color="primary"
                        class="text-xs leading-snug [&_.inline-flex]:max-w-full [&_.inline-flex]:flex-wrap"
                    />
                </div>
            </div>
        </template>
    </EntityMinimalCard>
</template>
