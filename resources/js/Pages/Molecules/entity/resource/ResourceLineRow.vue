<script setup>
/**
 * ResourceLineRow — Une ligne de la vue Line pour Resource
 *
 * @description
 * Affichage dense : State • Image • Level • Nom • Type • Rareté • Prix • Poids • Description • Effets
 * Structure conforme au schéma ENTITY_VIEWS_LINE.
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import CharacteristicEffectsGrid from "@/Pages/Molecules/data-display/CharacteristicEffectsGrid.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { buildCharacteristicEffectCell } from "@/Composables/entity/useCharacteristicEffectFormatter";
import { getRarityConfig } from "@/Utils/Entity/SharedConstants";

const props = defineProps({
    row: { type: Object, required: true },
    getCellFor: { type: Function, default: null },
    columns: { type: Array, default: () => [] },
    tableMeta: { type: Object, default: () => ({}) },
    showSelection: { type: Boolean, default: false },
    isSelected: { type: Boolean, default: false },
    showActions: { type: Boolean, default: true },
    uiColor: { type: String, default: "primary" },
});

const emit = defineEmits(["row-click", "toggle-select", "action"]);

const getCell = (fieldKey) => {
    const col = props.columns.find((c) => (c.cellId || c.id) === fieldKey);
    if (!col || !props.getCellFor) return { type: "text", value: "—", params: {} };
    return props.getCellFor(props.row, col) || { type: "text", value: "—", params: {} };
};

const stateValue = computed(() => props.row?.state ?? props.row?._data?.state ?? null);
const levelValue = computed(() => props.row?.level ?? props.row?._data?.level ?? null);

const nameCell = computed(() => getCell("name"));
const imageCell = computed(() => getCell("image"));
const typeCell = computed(() => getCell("resource_type"));
const priceCell = computed(() => getCell("price"));
const weightCell = computed(() => getCell("weight"));
const descriptionCell = computed(() => getCell("description"));

const effectItems = computed(() => {
    const ctx = {
        ...props.tableMeta,
        characteristics: props.tableMeta?.characteristics || {},
    };
    const cell = buildCharacteristicEffectCell({
        rawValues: [props.row?.effect ?? props.row?._data?.effect],
        options: { ctx },
        sourceGroups: ["resource", "item"],
        size: "md",
    });
    return cell?.type === "chips" ? cell.params?.items || [] : [];
});

const rarityConfig = computed(() => {
    const v = props.row?.rarity ?? props.row?._data?.rarity;
    const n = v != null ? Number(v) : null;
    return Number.isFinite(n) ? getRarityConfig(n) : null;
});

const byDbColumn = computed(
    () => props.tableMeta?.characteristics?.resource?.byDbColumn || props.tableMeta?.characteristics?.item?.byDbColumn || {}
);
const priceMeta = computed(() => byDbColumn.value?.price || byDbColumn.value?.kamas || null);
const weightMeta = computed(() => byDbColumn.value?.weight || byDbColumn.value?.pods || null);

const handleRowClick = () => emit("row-click", props.row);
</script>

<template>
    <div
        class="rounded-lg border border-base-300 bg-base-100/50 p-3 space-y-2 transition-colors"
        :class="{ 'bg-primary/10 ring-1 ring-primary/30': isSelected }"
        role="button"
        tabindex="0"
        @click="handleRowClick"
        @keydown.enter.space.prevent="handleRowClick"
    >
        <!-- Ligne 1 : State + Image + Level + Nom + Actions -->
        <div class="flex flex-wrap items-center gap-2">
            <div class="flex items-center gap-2 min-w-0 flex-1">
                <EntityUsableDot :state="stateValue" class="shrink-0 mt-0.5" />
                <div class="h-12 w-12 shrink-0 rounded overflow-hidden bg-base-200 flex items-center justify-center">
                    <img
                        v-if="imageCell?.value"
                        :src="imageCell.value"
                        :alt="row?.name || 'Image'"
                        class="h-full w-full object-contain"
                        loading="lazy"
                    />
                    <Icon v-else source="fa-solid fa-image" alt="" size="sm" class="text-base-content/40" />
                </div>
                <LevelBadge v-if="levelValue != null" :level="levelValue" size="sm" class="shrink-0" />
                <div class="min-w-0 flex-1">
                    <Route
                        v-if="nameCell?.type === 'route' && nameCell?.params?.href"
                        :href="nameCell.params.href"
                        :color="uiColor"
                        hover
                        class="font-semibold truncate block"
                    >
                        {{ nameCell.value || "—" }}
                    </Route>
                    <span v-else class="font-semibold truncate block">{{ nameCell?.value || "—" }}</span>
                </div>
            </div>
            <div v-if="showActions" class="shrink-0" @click.stop>
                <EntityActions
                    entity-type="resources"
                    :entity="row"
                    format="dropdown"
                    :available="['view', 'edit', 'quick-edit', 'delete', 'copy-link', 'download-pdf', 'refresh']"
                    @action="(k, e) => emit('action', k, e, row)"
                />
            </div>
            <CheckboxCore
                v-if="showSelection"
                :model-value="isSelected"
                size="xs"
                :color="uiColor"
                aria-label="Sélectionner"
                class="shrink-0"
                @update:model-value="(v) => emit('toggle-select', row, Boolean(v))"
                @click.stop
            />
        </div>

        <!-- Ligne 2 : Type • Rareté • Prix • Poids -->
        <div class="flex flex-wrap items-center gap-2 text-sm">
            <Badge v-if="typeCell?.value" color="neutral" variant="soft" size="xs">
                {{ typeCell.value }}
            </Badge>
            <Badge
                v-if="rarityConfig"
                :color="rarityConfig.daisyColor || rarityConfig.color || 'neutral'"
                variant="soft"
                size="xs"
            >
                {{ rarityConfig.label }}
            </Badge>
            <Tooltip v-if="priceCell?.value != null && priceCell?.value !== '—'" :content="`Prix: ${priceCell.value}`">
                <span class="inline-flex items-center gap-1">
                    <Icon
                        :source="priceMeta?.icon || 'fa-solid fa-coins'"
                        alt="Prix"
                        size="xs"
                        :style="priceMeta?.color ? { color: `var(--color-${priceMeta.color})` } : undefined"
                    />
                    <span>{{ priceCell.value }}</span>
                </span>
            </Tooltip>
            <Tooltip v-if="weightCell?.value != null && weightCell?.value !== '—'" :content="`Poids: ${weightCell.value}`">
                <span class="inline-flex items-center gap-1">
                    <Icon
                        :source="weightMeta?.icon || 'fa-solid fa-weight-hanging'"
                        alt="Poids"
                        size="xs"
                        :style="weightMeta?.color ? { color: `var(--color-${weightMeta.color})` } : undefined"
                    />
                    <span>{{ weightCell.value }}</span>
                </span>
            </Tooltip>
        </div>

        <!-- Ligne 3 : Description -->
        <p
            v-if="descriptionCell?.value && descriptionCell?.value !== '—'"
            class="text-xs text-base-content/80 line-clamp-2"
            :title="descriptionCell?.params?.tooltip || descriptionCell.value"
        >
            {{ descriptionCell.value }}
        </p>

        <!-- Ligne 4 : Effets (grille responsive) -->
        <CharacteristicEffectsGrid v-if="effectItems.length > 0" :items="effectItems" />
    </div>
</template>
