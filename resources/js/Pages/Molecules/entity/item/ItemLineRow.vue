<script setup>
/**
 * ItemLineRow — Une ligne de la vue Line pour Item
 *
 * @description
 * Même structure que ResourceLineRow : State • Image • Level • Nom • Type • Rareté • Prix • Description • Effets
 * Pas de poids (équipements).
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import CharacteristicEffectsGrid from "@/Pages/Molecules/data-display/CharacteristicEffectsGrid.vue";
import ResourceIngredientsList from "@/Pages/Molecules/data-display/ResourceIngredientsList.vue";
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

/** Entité source : rowParams.entity (API) ou row lui-même (données plates) */
const entity = computed(() => props.row?.rowParams?.entity ?? props.row);

const getCell = (fieldKey) => {
    const col = props.columns.find((c) => (c.cellId || c.id) === fieldKey);
    if (!col || !props.getCellFor) return { type: "text", value: "—", params: {} };
    return props.getCellFor(props.row, col) || { type: "text", value: "—", params: {} };
};

const stateValue = computed(() => entity.value?.state ?? entity.value?._data?.state ?? null);
const levelValue = computed(() => entity.value?.level ?? entity.value?._data?.level ?? null);

const nameCell = computed(() => getCell("name"));
const imageCell = computed(() => getCell("image"));
const typeCell = computed(() => getCell("item_type"));
const priceCell = computed(() => getCell("price"));
/** Description brute (non tronquée) */
const descriptionFull = computed(() => entity.value?.description ?? entity.value?._data?.description ?? "");

const effectItems = computed(() => {
    const ctx = {
        ...props.tableMeta,
        characteristics: props.tableMeta?.characteristics || {},
    };
    const cell = buildCharacteristicEffectCell({
        rawValues: [entity.value?.effect ?? entity.value?._data?.effect],
        options: { ctx },
        sourceGroups: ["item", "panoply"],
        size: "md",
    });
    return cell?.type === "chips" ? cell.params?.items || [] : [];
});

const rarityConfig = computed(() => {
    const v = entity.value?.rarity ?? entity.value?._data?.rarity;
    const n = v != null ? Number(v) : null;
    return Number.isFinite(n) ? getRarityConfig(n) : null;
});

const byDbColumn = computed(
    () => props.tableMeta?.characteristics?.item?.byDbColumn || props.tableMeta?.characteristics?.resource?.byDbColumn || {}
);
const priceMeta = computed(() => byDbColumn.value?.price || byDbColumn.value?.kamas || null);

/** Ingrédients (ressources) de recette */
const ingredients = computed(
    () => entity.value?.resources ?? entity.value?._data?.resources ?? []
);

const handleRowClick = () => emit("row-click", props.row);
</script>

<template>
    <div
        class="relative rounded-lg border border-base-300 bg-base-100/50 p-3 flex flex-col gap-2 transition-colors hover:bg-glass-sm"
        :class="{ 'bg-primary/10 ring-1 ring-primary/30': isSelected }"
        role="button"
        tabindex="0"
        @click="handleRowClick"
        @keydown.enter.space.prevent="handleRowClick"
    >
        <!-- State : coin supérieur gauche (absolute) -->
        <div class="absolute top-2 left-2 z-10" @click.stop>
            <EntityUsableDot :state="stateValue" />
        </div>
        <!-- Bloc Image + titre + propriétés -->
        <div class="flex gap-3">
        <!-- Image : pleine hauteur à gauche -->
        <div
            class="w-20 shrink-0 self-stretch min-h-20 rounded overflow-hidden bg-base-200 flex items-center justify-center"
        >
            <img
                v-if="imageCell?.value"
                :src="imageCell.value"
                :alt="entity?.name ?? row?.name ?? 'Image'"
                class="h-full w-full object-contain"
                loading="lazy"
            />
            <Icon v-else source="fa-solid fa-image" alt="" size="sm" class="text-base-content/40" />
        </div>
        <!-- Contenu à droite de l'image -->
        <div class="flex-1 min-w-0 flex flex-col gap-1.5 pl-1">
            <!-- Ligne 1 : Titre + Niveau + Actions -->
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    <LevelBadge v-if="levelValue != null" :level="levelValue" size="sm" class="shrink-0" />
                    <div class="min-w-0 flex-1">
                        <Route
                            v-if="nameCell?.type === 'route' && nameCell?.params?.href"
                            :href="nameCell.params.href"
                            color="neutral"
                            class="font-semibold truncate block text-base-content hover:text-base-content no-underline"
                        >
                            {{ nameCell.value || "—" }}
                        </Route>
                        <span v-else class="font-semibold truncate block">{{ nameCell?.value || "—" }}</span>
                    </div>
                </div>
                <div v-if="showActions" class="shrink-0" @click.stop>
                    <EntityActions
                        entity-type="items"
                        :entity="entity || row"
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
            <!-- Ligne 2 : Type • Rareté • Prix -->
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
            </div>
            <!-- Ligne 3 : Description (complète, retour à la ligne) -->
            <p
                v-if="descriptionFull"
                class="text-xs text-base-content/80 whitespace-normal break-words"
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
