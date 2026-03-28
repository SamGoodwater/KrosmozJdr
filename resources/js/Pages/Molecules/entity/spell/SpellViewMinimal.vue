<script setup>
/**
 * SpellViewMinimal — Vue Minimal pour Spell
 *
 * @description
 * Effets : `resolveSpellEffectsDisplayCell` (résumé API + SpellEffectChips, ou fallback `effect`).
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
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import {
    resolveSpellEffectsDisplayCell,
    spellEffectsCellHasContent,
} from "@/Composables/entity/useSpellEffectsDisplayCell";
import { getEntityCharacteristicsByDbColumn } from "@/Utils/Entity/entity-view-ui";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";

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
});

const emit = defineEmits(["edit", "view", "delete", "action"]);

const entity = computed(() => props.spell);

const cellOpts = () => ({ size: "xs", context: "minimal" });

const elementCell = computed(() => entity.value?.toCell?.("element", cellOpts()) ?? null);
const categoryCell = computed(() => entity.value?.toCell?.("category", cellOpts()) ?? null);
const spellTypesCell = computed(() => entity.value?.toCell?.("spell_types", cellOpts()) ?? null);

const stateValue = computed(() => entity.value?.state ?? entity.value?._data?.state ?? null);

const levelValue = computed(() => {
    const lv = entity.value?.level ?? entity.value?._data?.level;
    if (lv == null || lv === "") return null;
    const n = Number(lv);
    return Number.isFinite(n) ? n : null;
});

const paValue = computed(() => entity.value?.pa ?? entity.value?._data?.pa ?? null);
const poValue = computed(() => entity.value?.po ?? entity.value?._data?.po ?? null);

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

const byDbColumn = computed(() => getEntityCharacteristicsByDbColumn(props.tableMeta, "spell"));
const paMeta = computed(() => byDbColumn.value?.pa || null);
const poMeta = computed(() => byDbColumn.value?.po || byDbColumn.value?.po_max || null);

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
                            <CellRenderer
                                v-if="elementCell?.value && elementCell.value !== '—'"
                                :cell="elementCell"
                                class="inline-flex items-center"
                            />
                            <CellRenderer
                                v-if="categoryCell?.value && categoryCell.value !== '-' && categoryCell.value !== '—'"
                                :cell="categoryCell"
                                class="inline-flex items-center"
                            />
                            <CellRenderer
                                v-if="spellTypesCell?.value && spellTypesCell.value !== '-' && spellTypesCell.value !== '—'"
                                :cell="spellTypesCell"
                                class="inline-flex items-center"
                            />
                            <Tooltip
                                v-if="paValue != null && paValue !== ''"
                                :content="`PA: ${paValue}`"
                                placement="top"
                            >
                                <span class="inline-flex items-center gap-1">
                                    <Icon
                                        :source="paMeta?.icon || 'fa-solid fa-bolt'"
                                        alt="PA"
                                        size="xs"
                                        :style="paMeta?.color ? { color: `var(--color-${paMeta.color})` } : undefined"
                                    />
                                    <span>{{ paValue }}</span>
                                </span>
                            </Tooltip>
                            <Tooltip
                                v-if="poValue != null && poValue !== ''"
                                :content="`Portée: ${poValue}`"
                                placement="top"
                            >
                                <span class="inline-flex items-center gap-1">
                                    <Icon
                                        :source="poMeta?.icon || 'fa-solid fa-crosshairs'"
                                        alt="Portée"
                                        size="xs"
                                        :style="poMeta?.color ? { color: `var(--color-${poMeta.color})` } : undefined"
                                    />
                                    <span>{{ poValue }}</span>
                                </span>
                            </Tooltip>
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
                            <CellRenderer
                                v-if="elementCell?.value && elementCell.value !== '—'"
                                :cell="elementCell"
                                class="inline-flex items-center"
                            />
                            <CellRenderer
                                v-if="categoryCell?.value && categoryCell.value !== '-' && categoryCell.value !== '—'"
                                :cell="categoryCell"
                                class="inline-flex items-center"
                            />
                            <CellRenderer
                                v-if="spellTypesCell?.value && spellTypesCell.value !== '-' && spellTypesCell.value !== '—'"
                                :cell="spellTypesCell"
                                class="inline-flex items-center"
                            />
                            <Tooltip
                                v-if="paValue != null && paValue !== ''"
                                :content="`PA: ${paValue}`"
                                placement="top"
                            >
                                <span class="inline-flex items-center gap-1">
                                    <Icon
                                        :source="paMeta?.icon || 'fa-solid fa-bolt'"
                                        alt="PA"
                                        size="xs"
                                        :style="paMeta?.color ? { color: `var(--color-${paMeta.color})` } : undefined"
                                    />
                                    <span>{{ paValue }}</span>
                                </span>
                            </Tooltip>
                            <Tooltip
                                v-if="poValue != null && poValue !== ''"
                                :content="`Portée: ${poValue}`"
                                placement="top"
                            >
                                <span class="inline-flex items-center gap-1">
                                    <Icon
                                        :source="poMeta?.icon || 'fa-solid fa-crosshairs'"
                                        alt="Portée"
                                        size="xs"
                                        :style="poMeta?.color ? { color: `var(--color-${poMeta.color})` } : undefined"
                                    />
                                    <span>{{ poValue }}</span>
                                </span>
                            </Tooltip>
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
            </div>
        </template>
    </EntityMinimalCard>
</template>
