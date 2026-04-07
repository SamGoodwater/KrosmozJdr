<script setup>
/**
 * SpellViewMinimal — Vue Minimal pour Spell
 *
 * @description
 * Effets : `resolveSpellEffectsDisplayCell` (résumé API + SpellEffectChips, ou fallback `effect`).
 * Méta : `EntityPropertyDisplay` (aligné sur SpellViewCompact).
 * Invocations : {@link SpellSummonMonstersTextSection} (vue texte monstre, identique à la vue line).
 * Types + catégorie : masqués par défaut, visibles au survol ; types = icônes seules, catégorie = icône + libellé.
 *
 * @props {Spell} spell - Instance du modèle Spell
 */
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import {
    resolveSpellEffectsDisplayCell,
    spellEffectsCellHasContent,
} from "@/Composables/entity/useSpellEffectsDisplayCell";
import { getSpellFieldDescriptors } from "@/Entities/spell/spell-descriptors";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import SpellSummonMonstersTextSection from "@/Pages/Molecules/entity/spell/SpellSummonMonstersTextSection.vue";
import { Spell } from "@/Models/Entity/Spell";
import { spellTypesCellHasRenderableContent } from "@/Utils/Entity/spellTypeVisual.js";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import { PROPERTY_DISPLAY_MODES } from "@/Utils/Entity/Constants";

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

const stateValue = computed(() => entity.value?.state ?? entity.value?._data?.state ?? null);

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

/** Monstres invoqués (instance Spell ou objet brut table / Inertia avec `effects_definitions`). */
const summonMonsterBriefs = computed(() => {
    const e = entity.value;
    const raw =
        e instanceof Spell
            ? e.effectsDefinitions
            : e?.effects_definitions ?? e?._data?.effects_definitions;
    return Spell.summonMonstersFromEffectsDefinitionsPayload(raw);
});

const imageUrl = computed(() => {
    const u = entity.value?.image ?? entity.value?._data?.image;
    return u && String(u).trim() ? String(u) : null;
});

const showHref = computed(() =>
    entity.value?.id ? route("entities.spells.show", { spell: entity.value.id }) : null
);

const handleAction = async (actionKey) => {
    const spellId = entity.value?.id;
    if (!spellId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.spells.show", { spell: spellId }));
            emit("view", props.spell);
            break;
        case "edit":
            router.visit(route("entities.spells.edit", { spell: spellId }));
            emit("edit", props.spell);
            break;
        case "delete":
            emit("delete", props.spell);
            break;
        default:
            emit("action", actionKey, props.spell);
    }
};
</script>

<template>
    <EntityMinimalCard :display-mode="displayMode">
        <template #compact>
            <div
                data-cy="entity-minimal-card-compact"
                class="relative p-2 flex flex-col gap-1.5 transition-colors"
            >
                <div class="absolute top-1.5 left-1.5 z-10">
                    <EntityUsableDot :state="stateValue" />
                </div>
                <div class="flex gap-2">
                    <div
                        class="w-14 h-14 shrink-0 rounded overflow-hidden bg-base-200 flex items-center justify-center"
                    >
                        <img
                            v-if="imageUrl"
                            :src="imageUrl"
                            :alt="entity?.name ?? 'Sort'"
                            class="h-full w-full object-contain"
                            loading="lazy"
                        />
                        <Icon
                            v-else
                            source="fa-solid fa-wand-magic-sparkles"
                            alt=""
                            size="xs"
                            class="text-base-content/40"
                        />
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
                                    entity-type="spells"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="['view', 'edit', 'quick-edit', 'delete', 'copy-link']"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5 text-xs">
                            <EntityPropertyDisplay
                                v-if="canShowField('element')"
                                field-key="element"
                                :entity="entity"
                                entity-type="spell"
                                :display-mode="PROPERTY_DISPLAY_MODES.compact"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                                size="xs"
                                class="min-w-0"
                            />
                            <EntityPropertyDisplay
                                v-if="canShowField('pa')"
                                field-key="pa"
                                :entity="entity"
                                entity-type="spell"
                                :display-mode="PROPERTY_DISPLAY_MODES.compact"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                                size="xs"
                                class="min-w-0"
                            />
                            <EntityPropertyDisplay
                                v-if="canShowField('po')"
                                field-key="po"
                                :entity="entity"
                                entity-type="spell"
                                :display-mode="PROPERTY_DISPLAY_MODES.compact"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                                size="xs"
                                class="min-w-0"
                            />
                            <div
                                v-if="
                                    (canShowField('spell_types') && showSpellTypesCell) ||
                                    canShowField('category')
                                "
                                class="grid max-w-full grid-rows-[0fr] transition-[grid-template-rows] duration-200 ease-out group-hover:grid-rows-[1fr]"
                            >
                                <div
                                    class="min-h-0 overflow-hidden group-hover:overflow-visible"
                                >
                                    <div class="inline-flex max-w-full flex-wrap items-center gap-1.5">
                                        <EntityPropertyDisplay
                                            v-if="canShowField('spell_types') && showSpellTypesCell"
                                            field-key="spell_types"
                                            presentation="spell-types-icons-only"
                                            :entity="entity"
                                            entity-type="spell"
                                            :display-mode="PROPERTY_DISPLAY_MODES.minimal"
                                            :descriptors="descriptors"
                                            :table-meta="tableMeta"
                                            size="xs"
                                            class="min-w-0"
                                        />
                                        <EntityPropertyDisplay
                                            v-if="canShowField('category')"
                                            field-key="category"
                                            :entity="entity"
                                            entity-type="spell"
                                            :display-mode="PROPERTY_DISPLAY_MODES.minimal"
                                            variant="inline"
                                            hide-field-label
                                            :descriptors="descriptors"
                                            :table-meta="tableMeta"
                                            size="xs"
                                            class="min-w-0"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    v-if="hasEffects"
                    class="spell-effects-minimal w-full pt-1.5 mt-1 border-t border-base-300"
                >
                    <CellRenderer
                        :cell="effectDisplayCell"
                        ui-color="primary"
                        class="text-xs leading-snug [&_.inline-flex]:max-w-full [&_.inline-flex]:flex-wrap"
                    />
                </div>
                <SpellSummonMonstersTextSection :monsters="summonMonsterBriefs" />
            </div>
        </template>
        <template #expanded>
            <div
                data-cy="entity-minimal-card-expanded"
                class="relative p-2 flex flex-col gap-1.5 transition-colors"
            >
                <div class="absolute top-1.5 left-1.5 z-10">
                    <EntityUsableDot :state="stateValue" />
                </div>
                <div class="flex gap-2">
                    <div
                        class="w-14 h-14 shrink-0 rounded overflow-hidden bg-base-200 flex items-center justify-center"
                    >
                        <img
                            v-if="imageUrl"
                            :src="imageUrl"
                            :alt="entity?.name ?? 'Sort'"
                            class="h-full w-full object-contain"
                            loading="lazy"
                        />
                        <Icon
                            v-else
                            source="fa-solid fa-wand-magic-sparkles"
                            alt=""
                            size="xs"
                            class="text-base-content/40"
                        />
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
                                    entity-type="spells"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="['view', 'edit', 'quick-edit', 'delete', 'copy-link']"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5 text-xs">
                            <EntityPropertyDisplay
                                v-if="canShowField('element')"
                                field-key="element"
                                :entity="entity"
                                entity-type="spell"
                                :display-mode="PROPERTY_DISPLAY_MODES.compact"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                                size="xs"
                                class="min-w-0"
                            />
                            <EntityPropertyDisplay
                                v-if="canShowField('pa')"
                                field-key="pa"
                                :entity="entity"
                                entity-type="spell"
                                :display-mode="PROPERTY_DISPLAY_MODES.compact"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                                size="xs"
                                class="min-w-0"
                            />
                            <EntityPropertyDisplay
                                v-if="canShowField('po')"
                                field-key="po"
                                :entity="entity"
                                entity-type="spell"
                                :display-mode="PROPERTY_DISPLAY_MODES.compact"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                                size="xs"
                                class="min-w-0"
                            />
                            <div
                                v-if="
                                    (canShowField('spell_types') && showSpellTypesCell) ||
                                    canShowField('category')
                                "
                                class="grid max-w-full grid-rows-[0fr] transition-[grid-template-rows] duration-200 ease-out group-hover:grid-rows-[1fr]"
                            >
                                <div
                                    class="min-h-0 overflow-hidden group-hover:overflow-visible"
                                >
                                    <div class="inline-flex max-w-full flex-wrap items-center gap-1.5">
                                        <EntityPropertyDisplay
                                            v-if="canShowField('spell_types') && showSpellTypesCell"
                                            field-key="spell_types"
                                            presentation="spell-types-icons-only"
                                            :entity="entity"
                                            entity-type="spell"
                                            :display-mode="PROPERTY_DISPLAY_MODES.minimal"
                                            :descriptors="descriptors"
                                            :table-meta="tableMeta"
                                            size="xs"
                                            class="min-w-0"
                                        />
                                        <EntityPropertyDisplay
                                            v-if="canShowField('category')"
                                            field-key="category"
                                            :entity="entity"
                                            entity-type="spell"
                                            :display-mode="PROPERTY_DISPLAY_MODES.minimal"
                                            variant="inline"
                                            hide-field-label
                                            :descriptors="descriptors"
                                            :table-meta="tableMeta"
                                            size="xs"
                                            class="min-w-0"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
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
                    v-if="hasEffects"
                    class="spell-effects-minimal w-full pt-1.5 mt-1 border-t border-base-300"
                >
                    <CellRenderer
                        :cell="effectDisplayCell"
                        ui-color="primary"
                        class="text-xs leading-snug [&_.inline-flex]:max-w-full [&_.inline-flex]:flex-wrap"
                    />
                </div>
                <SpellSummonMonstersTextSection :monsters="summonMonsterBriefs" />
            </div>
        </template>
    </EntityMinimalCard>
</template>
