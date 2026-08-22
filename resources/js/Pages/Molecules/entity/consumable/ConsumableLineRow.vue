<script setup>
/**
 * ConsumableLineRow — Une ligne de la vue Line pour Consumable
 *
 * @description
 * Même structure que ResourceLineRow : State • Image • Level • Nom • Type • Rareté • Prix • Description • Effets
 * Pas de poids. Prix : `EntityPropertyDisplay` (aligné sur ConsumableViewFull).
 */
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import CharacteristicEffectsGrid from "@/Pages/Molecules/data-display/CharacteristicEffectsGrid.vue";
import ResourceIngredientsList from "@/Pages/Molecules/data-display/ResourceIngredientsList.vue";
import EntityLineRowActions from "@/Pages/Molecules/entity/shared/EntityLineRowActions.vue";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";
import { buildCharacteristicEffectCell } from "@/Composables/entity/useCharacteristicEffectFormatter";
import { emitLineRowClick, emitLineRowDblClick } from "@/Composables/table/useEntityTableRowPointer";
import { getRarityConfig } from "@/Utils/Entity/SharedConstants";
import { getRowEntity } from "@/Utils/Entity/rowEntity";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getConsumableFieldDescriptors } from "@/Entities/consumable/consumable-descriptors";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import EntityFieldTooltip from "@/Pages/Molecules/entity/shared/EntityFieldTooltip.vue";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import { PROPERTY_DISPLAY_MODES } from "@/Utils/Entity/Constants";

const props = defineProps({
    row: { type: Object, required: true },
    getCellFor: { type: Function, default: null },
    columns: { type: Array, default: () => [] },
    tableMeta: { type: Object, default: () => ({}) },
    showSelection: { type: Boolean, default: false },
    isSelected: { type: Boolean, default: false },
    showActions: { type: Boolean, default: true },
    uiColor: { type: String, default: "primary" },
    entityType: { type: String, default: "consumables" },
    characteristicRuntime: { type: Object, default: null },
});

provideCharacteristicRuntime(computed(() => props.characteristicRuntime));

const emit = defineEmits(["row-click", "row-dblclick", "toggle-select", "action"]);

const permissions = usePermissions();
const ctx = computed(() => ({
    capabilities: {
        viewAny: permissions.can("consumables", "viewAny"),
        createAny: permissions.can("consumables", "createAny"),
        updateAny: permissions.can("consumables", "updateAny"),
        deleteAny: permissions.can("consumables", "deleteAny"),
        manageAny: permissions.can("consumables", "manageAny"),
    },
    meta: { capabilities: {} },
}));
const descriptors = computed(() => getConsumableFieldDescriptors(ctx.value));

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

/** Entité source : rowParams.entity (API) ou row lui-même (données plates) */
const entity = computed(() => getRowEntity(props.row));

const getCell = (fieldKey) => {
    const col = props.columns.find((c) => (c.cellId || c.id) === fieldKey);
    if (!col || !props.getCellFor) return { type: "text", value: "—", params: {} };
    return props.getCellFor(props.row, col) || { type: "text", value: "—", params: {} };
};

const levelValue = computed(() => entity.value?.level ?? entity.value?._data?.level ?? null);

const nameCell = computed(() => getCell("name"));
const imageCell = computed(() => getCell("image"));
const typeCell = computed(() => getCell("consumable_type"));
/** Description brute (non tronquée) */
const descriptionFull = computed(() => entity.value?.description ?? entity.value?._data?.description ?? "");

const effectItems = computed(() => {
    const cell = buildCharacteristicEffectCell({
        rawValues: [entity.value?.effect ?? entity.value?._data?.effect],
        options: {},
        sourceGroups: ["consumable", "item"],
        size: "md",
    });
    return cell?.type === "chips" ? cell.params?.items || [] : [];
});

const rarityConfig = computed(() => {
    const v = entity.value?.rarity ?? entity.value?._data?.rarity;
    const n = v != null ? Number(v) : null;
    return Number.isFinite(n) ? getRarityConfig(n) : null;
});

/** Ingrédients (ressources) de recette */
const ingredients = computed(
    () => entity.value?.resources ?? entity.value?._data?.resources ?? []
);

</script>

<template>
    <div
        class="group relative rounded-box border border-base-300 bg-glass-2xl p-3 flex flex-col gap-2 transition-colors hover:bg-glass-3xl"
        :class="{ 'bg-primary/10 ring-1 ring-primary/30': isSelected }"
        style="--bg-color: var(--color-base-100)"
        data-row-contextmenu-target
        @click="(e) => emitLineRowClick(emit, row, e)"
        @dblclick="(e) => emitLineRowDblClick(emit, row, e)"
    >
        <!-- Bloc Image + titre + propriétés -->
        <div class="flex gap-3">
        <!-- Image : pleine hauteur à gauche -->
        <EntityThumb
            size="line"
            :src="imageCell?.value || ''"
            :label="entity?.name ?? row?.name ?? 'Consommable'"
        />
        <!-- Contenu à droite de l'image -->
        <div class="flex-1 min-w-0 flex flex-col gap-1.5 pl-1">
            <!-- Ligne 1 : Titre + Niveau + Actions -->
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    <EntityFieldTooltip
                        v-if="levelValue != null"
                        field-key="level"
                        entity-type="consumable"
                        :descriptors="descriptors"
                        :table-meta="tableMeta"
                    >
                        <LevelBadge :level="levelValue" size="sm" class="shrink-0" hide-tooltip />
                    </EntityFieldTooltip>
                    <div class="min-w-0 flex-1">
                        <Link
                            v-if="nameCell?.type === 'route' && nameCell?.params?.href"
                            :href="nameCell.params.href"
                            class="font-semibold truncate block text-base-content hover:text-base-content link link-neutral link-hover"
                            @click.stop
                        >
                            {{ nameCell.value || "—" }}
                        </Link>
                        <span v-else class="font-semibold truncate block">{{ nameCell?.value || "—" }}</span>
                    </div>
                </div>
                <EntityLineRowActions
                    v-if="showActions"
                    entity-type="consumables"
                    :entity="entity"
                    @action="(k, e) => emit('action', k, e, row)"
                />
                <div
                    v-if="showSelection"
                    class="flex shrink-0 items-center transition-[max-width,opacity] duration-150 ease-out"
                    :class="
                        isSelected
                            ? 'max-w-10 overflow-visible opacity-100 pointer-events-auto'
                            : 'max-w-0 overflow-hidden opacity-0 pointer-events-none group-hover:max-w-10 group-hover:overflow-visible group-hover:opacity-100 group-hover:pointer-events-auto group-focus-within:max-w-10 group-focus-within:overflow-visible group-focus-within:opacity-100 group-focus-within:pointer-events-auto'
                    "
                    @click.stop
                >
                    <CheckboxCore
                        :model-value="isSelected"
                        size="xs"
                        :color="uiColor"
                        aria-label="Sélectionner"
                        class="shrink-0"
                        @update:model-value="(v) => emit('toggle-select', row, Boolean(v))"
                    />
                </div>
            </div>
            <!-- Ligne 2 : Type • Rareté • Prix -->
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <EntityFieldTooltip
                    v-if="typeCell?.value"
                    field-key="consumable_type"
                    entity-type="consumable"
                    :descriptors="descriptors"
                    :table-meta="tableMeta"
                >
                    <Badge color="auto" :auto-label="typeCell.value" auto-scheme="labelHash" auto-tone="light" variant="soft" size="xs">
                        {{ typeCell.value }}
                    </Badge>
                </EntityFieldTooltip>
                <EntityFieldTooltip
                    v-if="rarityConfig"
                    field-key="rarity"
                    entity-type="consumable"
                    :descriptors="descriptors"
                    :table-meta="tableMeta"
                >
                    <Badge
                        :color="rarityConfig.daisyColor || rarityConfig.color || 'neutral'"
                        variant="soft"
                        size="xs"
                    >
                        {{ rarityConfig.label }}
                    </Badge>
                </EntityFieldTooltip>
                <EntityPropertyDisplay
                    v-if="canShowField('price')"
                    field-key="price"
                    :entity="entity"
                    entity-type="consumable"
                    :display-mode="PROPERTY_DISPLAY_MODES.compact"
                    :descriptors="descriptors"
                    :table-meta="tableMeta"
                    size="xs"
                    class="min-w-0"
                />
            </div>
            <!-- Ligne 3 : Description (complète, retour à la ligne) -->
            <p
                v-if="descriptionFull"
                class="text-xs text-base-content/80 whitespace-normal wrap-break-word"
                :title="descriptionFull"
            >
                {{ descriptionFull }}
            </p>
        </div>
        </div>
        <!-- Effets : pleine largeur sous le bloc Image/titre/propriétés -->
        <div
            v-if="effectItems.length > 0"
            class="w-full pt-2 mt-1 border-t border-base-300"
        >
            <CharacteristicEffectsGrid :items="effectItems" />
        </div>
        <!-- Ingrédients (ressources) : icône + nom, sous les effets -->
        <div
            v-if="ingredients.length > 0"
            class="w-full pt-2 mt-1 border-t border-base-300"
        >
            <ResourceIngredientsList :ingredients="ingredients" />
        </div>
    </div>

</template>
