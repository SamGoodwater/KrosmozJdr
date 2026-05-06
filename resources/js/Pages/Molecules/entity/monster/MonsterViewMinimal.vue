<script setup>
/**
 * MonsterViewMinimal — Vue Minimal pour Monster
 *
 * @description
 * Identité et méta toujours visibles ; description discrète au survol (modes compact / hover) ;
 * résumés combat / stats / etc. en blocs compacts avec expansion au survol du bloc ;
 * relations (invocations, loot…) en une ligne de chips ; carte Compétences après les blocs résumé,
 * visible au survol / focus (liste longue, scroll interne) ; liste des sorts de la créature en fin
 * (lien texte + vignette, aperçu SpellViewMinimal au survol). Plus de carte caractéristiques complète dans l’overlay.
 *
 * @props {Monster} monster - Instance du modèle Monster
 */
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import CharacteristicsCard from "@/Pages/Organismes/data-display/CharacteristicsCard.vue";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getMonsterFieldDescriptors } from "@/Entities/monster/monster-descriptors";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import { buildCreatureCompetenceGroupsByPrimary } from "@/Utils/Entity/buildCreatureCompetenceGroups";
import MonsterCreatureSpellsList from "@/Pages/Molecules/entity/monster/MonsterCreatureSpellsList.vue";
import MonsterBossMark from "@/Pages/Molecules/entity/monster/MonsterBossMark.vue";
import LanguageViewMinimal from "@/Pages/Molecules/entity/language/LanguageViewMinimal.vue";
import { cellHasRenderableContent, resolveEntityFieldUi } from "@/Utils/Entity/entity-view-ui";

const props = defineProps({
    monster: {
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
        viewAny: permissions.can("monsters", "viewAny"),
        createAny: permissions.can("monsters", "createAny"),
        updateAny: permissions.can("monsters", "updateAny"),
        deleteAny: permissions.can("monsters", "deleteAny"),
        manageAny: permissions.can("monsters", "manageAny"),
    },
    meta: { capabilities: {} },
}));

const descriptors = computed(() => getMonsterFieldDescriptors(ctx.value));

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

const entity = computed(() => props.monster);

const creatureData = computed(() => entity.value?.creature ?? entity.value?._data?.creature ?? null);

const stateValue = computed(() => entity.value?.state ?? entity.value?._data?.state ?? null);

const levelValue = computed(() => {
    const lv = creatureData.value?.level;
    if (lv == null || lv === "") return null;
    const n = Number(lv);
    return Number.isFinite(n) ? n : null;
});

const imageUrl = computed(() => {
    const u = creatureData.value?.image;
    return u && String(u).trim() ? String(u) : null;
});

const creatureName = computed(() => creatureData.value?.name ?? "—");

const isBoss = computed(() => Boolean(entity.value?.isBoss ?? entity.value?._data?.is_boss));

const bossFieldTooltip = computed(() =>
    resolveEntityFieldUi({
        fieldKey: "is_boss",
        descriptors: descriptors.value,
        tableMeta: props.tableMeta,
        entityType: "monster",
    }).tooltip,
);

const descriptionFull = computed(() => {
    const d = creatureData.value?.description;
    return d && String(d).trim() ? String(d) : "";
});

const cellOpts = () => ({ size: "xs", context: "minimal", ctx: props.tableMeta });

const SUMMARY_KEYS = [
    "creature_summary_combat",
    "creature_summary_stats",
    "creature_summary_control",
    "creature_summary_resistance",
    "creature_summary_damage",
];

function getSummaryCell(fieldKey) {
    const m = entity.value;
    if (m && typeof m.toCell === "function") {
        return m.toCell(fieldKey, cellOpts());
    }
    return { type: "text", value: "—", params: {} };
}

const relationsCell = computed(() => getSummaryCell("monster_summary_relations"));

/** Maîtrises regroupées par caractéristique primaire (carte longue → affichée au survol de la carte). */
const competenceGroups = computed(() => buildCreatureCompetenceGroupsByPrimary(creatureData.value));

const cardEntityForCompetences = computed(() =>
    creatureData.value ? { level: creatureData.value.level } : null,
);

const hasRelationsChips = computed(() => {
    const items = relationsCell.value?.params?.items;
    return Array.isArray(items) && items.length > 0;
});

const raceCell = computed(() => getSummaryCell("monster_race"));
const sizeCell = computed(() => getSummaryCell("size"));
/** Les chips (taille) ont `value` vide : ne pas tester seulement `cell.value`. */
const showSizeCell = computed(() => cellHasRenderableContent(sizeCell.value));
const hostilityCell = computed(() => getSummaryCell("creature_hostility"));
const bossPaCell = computed(() => getSummaryCell("boss_pa"));

const showHref = computed(() =>
    entity.value?.id ? route("entities.monsters.show", { monster: entity.value.id }) : null
);

const linkedLanguages = computed(() => {
    const raw = entity.value?._data?.languages ?? entity.value?.languages;
    return Array.isArray(raw) ? raw : [];
});

const hasLinkedLanguages = computed(() => linkedLanguages.value.length > 0);

const showDescriptionInCompactSlot = computed(() => props.displayMode === "compact");

const handleAction = async (actionKey) => {
    const monsterId = entity.value?.id;
    if (!monsterId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.monsters.show", { monster: monsterId }));
            emit("view", props.monster);
            break;
        case "edit":
            router.visit(route("entities.monsters.edit", { monster: monsterId }));
            emit("edit", props.monster);
            break;
        case "delete":
            emit("delete", props.monster);
            break;
        default:
            emit("action", actionKey, props.monster);
    }
};
</script>

<template>
    <EntityMinimalCard :display-mode="displayMode" pinned-entity-type="monsters" :pinned-entity-id="entity?.id">
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
                        <Image
                            v-if="imageUrl"
                            :source="imageUrl"
                            :alt="creatureName"
                            fit="contain"
                            class="h-full w-full"
                        />
                        <Icon v-else source="fa-solid fa-dragon" alt="" size="xs" class="text-base-content/40" />
                    </div>
                    <div class="flex-1 min-w-0 flex flex-col gap-1 pl-0.5">
                        <div class="flex items-center gap-1.5">
                            <LevelBadge v-if="levelValue != null" :level="levelValue" size="xs" class="shrink-0" />
                            <MonsterBossMark
                                v-if="isBoss && canShowField('is_boss')"
                                :tooltip="bossFieldTooltip"
                                size-class="h-5 w-5"
                                class="shrink-0"
                            />
                            <div class="min-w-0 flex-1">
                                <Route
                                    v-if="showHref"
                                    :href="showHref"
                                    color="neutral"
                                    class="font-semibold truncate block text-sm text-base-content hover:text-base-content no-underline"
                                >
                                    {{ creatureName }}
                                </Route>
                                <span v-else class="font-semibold truncate block text-sm">
                                    {{ creatureName }}
                                </span>
                            </div>
                            <div v-if="showActions" data-entity-actions class="shrink-0" @click.stop>
                                <EntityActions
                                    entity-type="monsters"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="['pin', 'copy-link', 'quick-view', 'quick-edit']"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5 text-xs">
                            <CellRenderer
                                v-if="raceCell?.value && raceCell.value !== '-' && raceCell.value !== '—'"
                                :cell="raceCell"
                                class="inline-flex text-xs"
                            />
                            <CellRenderer
                                v-if="showSizeCell"
                                :cell="sizeCell"
                                class="inline-flex text-[11px] text-base-content/80"
                            />
                            <CellRenderer
                                v-if="canShowField('creature_hostility') && hostilityCell?.value !== '—'"
                                :cell="hostilityCell"
                                class="inline-flex text-[11px] text-base-content/85"
                            />
                            <CellRenderer
                                v-if="isBoss && canShowField('boss_pa') && bossPaCell?.value !== '—'"
                                :cell="bossPaCell"
                                class="inline-flex text-[11px]"
                            />
                        </div>
                        <p
                            v-if="showDescriptionInCompactSlot && descriptionFull"
                            class="text-[11px] leading-snug italic text-base-content/45 max-h-0 opacity-0 overflow-hidden transition-all duration-200 ease-out group-hover:max-h-32 group-hover:opacity-100 group-hover:mt-0.5"
                            :title="descriptionFull"
                        >
                            {{ descriptionFull }}
                        </p>
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
                                class="min-w-0 max-w-[11rem]"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #expanded>
            <div
                data-cy="entity-minimal-card-expanded"
                class="relative flex flex-col gap-1.5 p-2 transition-colors"
            >
                <div class="absolute top-1.5 left-1.5 z-10">
                    <EntityUsableDot :state="stateValue" />
                </div>
                <div class="flex gap-2">
                    <div
                        class="w-14 h-14 shrink-0 rounded overflow-hidden bg-base-200 flex items-center justify-center"
                    >
                        <Image
                            v-if="imageUrl"
                            :source="imageUrl"
                            :alt="creatureName"
                            fit="contain"
                            class="h-full w-full"
                        />
                        <Icon v-else source="fa-solid fa-dragon" alt="" size="xs" class="text-base-content/40" />
                    </div>
                    <div class="flex-1 min-w-0 flex flex-col gap-1 pl-0.5">
                        <div class="flex items-center gap-1.5">
                            <LevelBadge v-if="levelValue != null" :level="levelValue" size="xs" class="shrink-0" />
                            <MonsterBossMark
                                v-if="isBoss && canShowField('is_boss')"
                                :tooltip="bossFieldTooltip"
                                size-class="h-5 w-5"
                                class="shrink-0"
                            />
                            <div class="min-w-0 flex-1">
                                <Route
                                    v-if="showHref"
                                    :href="showHref"
                                    color="neutral"
                                    class="font-semibold truncate block text-sm text-base-content hover:text-base-content no-underline"
                                >
                                    {{ creatureName }}
                                </Route>
                                <span v-else class="font-semibold truncate block text-sm">
                                    {{ creatureName }}
                                </span>
                            </div>
                            <div v-if="showActions" data-entity-actions class="shrink-0" @click.stop>
                                <EntityActions
                                    entity-type="monsters"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="['pin', 'copy-link', 'quick-view', 'quick-edit']"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5 text-xs">
                            <CellRenderer
                                v-if="raceCell?.value && raceCell.value !== '-' && raceCell.value !== '—'"
                                :cell="raceCell"
                                class="inline-flex text-xs"
                            />
                            <CellRenderer
                                v-if="showSizeCell"
                                :cell="sizeCell"
                                class="inline-flex text-[11px] text-base-content/80"
                            />
                            <CellRenderer
                                v-if="canShowField('creature_hostility') && hostilityCell?.value !== '—'"
                                :cell="hostilityCell"
                                class="inline-flex text-[11px] text-base-content/85"
                            />
                            <CellRenderer
                                v-if="isBoss && canShowField('boss_pa') && bossPaCell?.value !== '—'"
                                :cell="bossPaCell"
                                class="inline-flex text-[11px]"
                            />
                        </div>
                        <p
                            v-if="descriptionFull"
                            :class="
                                displayMode === 'extended'
                                    ? 'text-[11px] leading-snug italic text-base-content/55 mt-0.5'
                                    : 'text-[11px] leading-snug italic text-base-content/45 max-h-0 overflow-hidden opacity-0 transition-all duration-200 ease-out group-hover:max-h-40 group-hover:opacity-100'
                            "
                            :title="descriptionFull"
                        >
                            {{ descriptionFull }}
                        </p>
                    </div>
                </div>

                <div
                    v-if="canShowField('monster_summary_relations') && hasRelationsChips"
                    class="w-full border-t border-base-300/80 pt-1.5"
                >
                    <p class="mb-0.5 text-[10px] font-semibold uppercase tracking-wide text-primary-300/90">
                        Contenus liés
                    </p>
                    <CellRenderer
                        :cell="relationsCell"
                        ui-color="primary"
                        class="text-[11px] leading-tight [&_.inline-flex]:max-w-full [&_.inline-flex]:flex-wrap"
                    />
                </div>

                <div
                    v-if="hasLinkedLanguages"
                    class="w-full border-t border-base-300/80 pt-1.5"
                    role="region"
                    aria-label="Langues"
                >
                    <p class="mb-0.5 text-[10px] font-semibold uppercase tracking-wide text-primary-300/90">
                        Langues
                    </p>
                    <div class="flex flex-wrap gap-1">
                        <LanguageViewMinimal
                            v-for="lang in linkedLanguages"
                            :key="lang.id"
                            :language="lang"
                            class="min-w-0 max-w-[11rem]"
                        />
                    </div>
                </div>

                <div
                    v-if="creatureData"
                    class="grid w-full grid-cols-1 gap-1.5 border-t border-primary/20 bg-primary/5 pt-1.5 sm:grid-cols-2"
                >
                    <div
                        v-for="fieldKey in SUMMARY_KEYS"
                        :key="fieldKey"
                        class="group/ms min-h-0 min-w-0 overflow-hidden rounded-md border border-base-300/60 bg-base-200/30 p-1 transition-[max-height] duration-200 ease-out max-h-[6.25rem] hover:max-h-[min(70vh,28rem)] hover:overflow-y-auto"
                    >
                        <CellRenderer
                            :cell="getSummaryCell(fieldKey)"
                            ui-color="primary"
                            class="text-[11px] leading-tight [&_.characteristics-card]:!p-1 [&_.characteristic-group>h4]:!text-[0.6rem]"
                        />
                    </div>
                </div>

                <!-- Compétences (maîtrises) : bloc long, révélé au survol / focus de la carte -->
                <div
                    v-if="creatureData && competenceGroups.length"
                    class="minimal-monster-competences-outer transition-[max-height,opacity] duration-200 ease-out max-h-0 opacity-0 overflow-hidden group-hover:max-h-[min(90vh,44rem)] group-hover:opacity-100 group-focus-within:max-h-[min(90vh,44rem)] group-focus-within:opacity-100"
                >
                    <div
                        class="minimal-monster-competences mt-1.5 max-h-[min(85vh,42rem)] overflow-y-auto overscroll-contain border-t border-base-300/80 pt-1.5"
                    >
                        <p class="mb-1 text-[10px] font-semibold uppercase tracking-wide text-primary-300/90">
                            Compétences
                        </p>
                        <CharacteristicsCard
                            :entity="cardEntityForCompetences"
                            :groups="competenceGroups"
                            :runtime="characteristicRuntime"
                            dense
                            class="border-0 bg-transparent p-0 shadow-none ring-0 [&_.characteristics-card]:!p-1 [&_.characteristic-group>h4]:!text-[0.6rem]"
                        />
                    </div>
                </div>

                <!-- Sorts liés à la créature (texte + aperçu minimal au survol de chaque sort) -->
                <MonsterCreatureSpellsList
                    v-if="creatureData"
                    :creature="creatureData"
                    :table-meta="tableMeta"
                    :characteristic-runtime="characteristicRuntime"
                    section-class="mt-1.5 border-t border-base-300/80 pt-1.5"
                />
            </div>
        </template>
    </EntityMinimalCard>
</template>
