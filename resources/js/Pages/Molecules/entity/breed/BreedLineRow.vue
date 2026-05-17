<script setup>
/**
 * BreedLineRow — Une ligne de la vue Line pour les classes (Breed)
 *
 * @description
 * Aligné sur MonsterLineRow / SpellLineRow : état • image • dé de vie • nom • spécificité • relations • description.
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import EntityLineRowActions from "@/Pages/Molecules/entity/shared/EntityLineRowActions.vue";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";
import { emitLineRowClick, emitLineRowDblClick } from "@/Composables/table/useEntityTableRowPointer";
import { getRowEntity } from "@/Utils/Entity/rowEntity";
import BreedElementOrientationsDisplay from "@/Pages/Molecules/entity/breed/BreedElementOrientationsDisplay.vue";
import BreedCapabilitiesDisplay from "@/Pages/Molecules/entity/breed/BreedCapabilitiesDisplay.vue";
import BreedVariantsDisplay from "@/Pages/Molecules/entity/breed/BreedVariantsDisplay.vue";
import { normalizeElementOrientationMap } from "@/Utils/entity/breedOrientations";
import { buildSpellSlotGroups } from "@/Utils/entity/breedSpellSlots";
import { sanitizeHtml } from "@/Utils/security/sanitizeHtml";
import { isRichHtmlVisuallyEmpty } from "@/Utils/richText/isRichHtmlVisuallyEmpty";
import LanguageViewMinimal from "@/Pages/Molecules/entity/language/LanguageViewMinimal.vue";

const props = defineProps({
    row: { type: Object, required: true },
    getCellFor: { type: Function, default: null },
    columns: { type: Array, default: () => [] },
    tableMeta: { type: Object, default: () => ({}) },
    showSelection: { type: Boolean, default: false },
    isSelected: { type: Boolean, default: false },
    showActions: { type: Boolean, default: true },
    uiColor: { type: String, default: "primary" },
    entityType: { type: String, default: "breeds" },
});

const emit = defineEmits(["row-click", "row-dblclick", "toggle-select", "action"]);

const entity = computed(() => getRowEntity(props.row));

const getCell = (fieldKey) => {
    const col = props.columns.find((c) => (c.cellId || c.id) === fieldKey);
    if (!col || !props.getCellFor) return { type: "text", value: "—", params: {} };
    return props.getCellFor(props.row, col) || { type: "text", value: "—", params: {} };
};

const imageUrl = computed(() => {
    const u = entity.value?.image ?? entity.value?.icon ?? entity.value?._data?.image ?? entity.value?._data?.icon;
    return u && String(u).trim() ? String(u) : null;
});

const nameCell = computed(() => getCell("name"));
const lifeDiceCell = computed(() => getCell("life_dice"));
const specificityCell = computed(() => getCell("specificity"));
const descriptionFull = computed(
    () => entity.value?.description ?? entity.value?._data?.description ?? ""
);

const evolutionRaw = computed(
    () => entity.value?.evolution ?? entity.value?._data?.evolution ?? ""
);

const evolutionHtmlSafe = computed(() => {
    const raw = evolutionRaw.value;
    if (raw == null || isRichHtmlVisuallyEmpty(String(raw))) {
        return "";
    }
    return sanitizeHtml(String(raw));
});

const orientationMap = computed(() => {
    const raw = entity.value?._data ?? entity.value;
    return normalizeElementOrientationMap(raw?.element_orientations);
});

const linkedCapabilities = computed(() => {
    const raw = entity.value?._data?.capabilities ?? entity.value?.capabilities;
    return Array.isArray(raw) ? raw : [];
});

const hasLinkedCapabilities = computed(() => linkedCapabilities.value.length > 0);

const hasSpellSlots = computed(() => {
    const raw = entity.value?._data ?? entity.value;
    return buildSpellSlotGroups(raw).length > 0;
});

const linkedLanguages = computed(() => {
    const raw = entity.value?._data?.languages ?? entity.value?.languages;
    return Array.isArray(raw) ? raw : [];
});

const hasLinkedLanguages = computed(() => linkedLanguages.value.length > 0);

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
        <div class="flex gap-3">
            <div
                class="w-20 shrink-0 self-stretch min-h-20 rounded overflow-hidden bg-base-200 flex items-center justify-center"
            >
                <Image
                    v-if="imageUrl"
                    :source="imageUrl"
                    :alt="entity?.name ?? row?.name ?? 'Classe'"
                    fit="contain"
                    class="h-full w-full"
                />
                <Icon v-else source="fa-solid fa-graduation-cap" alt="" size="sm" class="text-base-content/40" />
            </div>
            <div class="flex-1 min-w-0 flex flex-col gap-1.5 pl-1">
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        <div class="min-w-0 flex-1">
                            <span class="font-semibold truncate block">{{ nameCell?.value || "—" }}</span>
                        </div>
                    </div>
                    <EntityLineRowActions
                        v-if="showActions"
                        entity-type="breeds"
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
                <div
                    v-if="lifeDiceCell?.value && lifeDiceCell.value !== '-' && lifeDiceCell.value !== '—'"
                    class="flex flex-wrap items-center gap-2 text-sm"
                >
                    <span class="text-xs text-base-content/80">
                        <span class="font-medium text-base-content">Dé de vie</span>
                        {{ lifeDiceCell.value }}
                    </span>
                </div>
                <div class="w-full mt-1">
                    <BreedElementOrientationsDisplay :orientation-map="orientationMap" size="xs" />
                </div>
                <p
                    v-if="specificityCell?.value && specificityCell.value !== '-' && specificityCell.value !== '—'"
                    class="text-xs text-base-content/70 line-clamp-2"
                    :title="String(specificityCell.value)"
                >
                    {{ specificityCell.value }}
                </p>
                <BreedCapabilitiesDisplay
                    v-if="hasLinkedCapabilities"
                    :capabilities="linkedCapabilities"
                    density="text"
                />
                <div
                    v-if="evolutionHtmlSafe"
                    class="transition-[max-height,opacity] duration-200 ease-out max-h-0 opacity-0 overflow-hidden group-hover:max-h-[min(55vh,26rem)] group-hover:opacity-100 group-focus-within:max-h-[min(55vh,26rem)] group-focus-within:opacity-100"
                    role="region"
                    aria-label="Évolution"
                >
                    <div
                        class="mt-1 max-h-[min(50vh,24rem)] overflow-y-auto overscroll-contain rounded-box border border-base-300/60 bg-base-200/25 px-2 py-1.5"
                    >
                        <!-- eslint-disable vue/no-v-html -- sanitizeHtml côté script -->
                        <div
                            class="rich-text-readonly prose prose-sm max-w-none text-xs text-base-content/85 **:my-1!"
                            v-html="evolutionHtmlSafe"
                        />
                        <!-- eslint-enable vue/no-v-html -->
                    </div>
                </div>
                <BreedVariantsDisplay
                    v-if="hasSpellSlots"
                    :breed="entity?._data ?? entity"
                    density="text"
                    :show-temple-note="false"
                />
                <div
                    v-if="hasLinkedLanguages"
                    class="flex flex-wrap gap-1 max-h-0 overflow-hidden opacity-0 transition-all duration-150 group-hover:max-h-40 group-hover:opacity-100 group-focus-within:max-h-40 group-focus-within:opacity-100"
                    role="region"
                    aria-label="Langues"
                >
                    <LanguageViewMinimal
                        v-for="lang in linkedLanguages"
                        :key="lang.id"
                        :language="lang"
                        class="min-w-0 max-w-44"
                    />
                </div>
                <p
                    v-if="descriptionFull"
                    class="wrap-break-word text-xs whitespace-normal text-base-content/80 italic line-clamp-3 transition-[line-clamp] duration-150 group-hover:line-clamp-none"
                    :title="descriptionFull"
                >
                    {{ descriptionFull }}
                </p>
            </div>
        </div>

    </div>
</template>
