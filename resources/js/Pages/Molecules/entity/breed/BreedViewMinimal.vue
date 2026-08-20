<script setup>
/**
 * BreedViewMinimal — Vue Minimal pour Breed
 *
 * @description
 * Alignée sur MonsterViewMinimal : EntityMinimalCard, état • image • nom • vie / dé • spécificité • relations • description.
 *
 * @props {Breed} breed - Instance du modèle Breed
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from "vue";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import EntityMinimalTitle from "@/Pages/Molecules/entity/shared/EntityMinimalTitle.vue";
import { useEntityMinimalShell } from "@/Composables/entity/useEntityMinimalShell";
import BreedElementOrientationsDisplay from "@/Pages/Molecules/entity/breed/BreedElementOrientationsDisplay.vue";
import BreedCapabilitiesDisplay from "@/Pages/Molecules/entity/breed/BreedCapabilitiesDisplay.vue";
import BreedVariantsDisplay from "@/Pages/Molecules/entity/breed/BreedVariantsDisplay.vue";
import CreatureTraitBadges from "@/Pages/Molecules/entity/creature-trait/CreatureTraitBadges.vue";
import LanguageViewMinimal from "@/Pages/Molecules/entity/language/LanguageViewMinimal.vue";
import { normalizeElementOrientationMap } from "@/Utils/entity/breedOrientations";
import { buildSpellSlotGroups } from "@/Utils/entity/breedSpellSlots";

const props = defineProps({
    breed: {
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

const emit = defineEmits(["edit", "view", "delete", "action", "quick-view"]);

const entity = computed(() => props.breed);

const imageUrl = computed(() => {
    const u = entity.value?.image ?? entity.value?.icon ?? entity.value?._data?.image ?? entity.value?._data?.icon;
    return u && String(u).trim() ? String(u) : null;
});

const cellOpts = () => ({ size: "xs", context: "minimal" });

const orientationMap = computed(() => {
    const raw = entity.value?._data ?? entity.value;
    return normalizeElementOrientationMap(raw?.element_orientations);
});

const descriptionFull = computed(() => {
    const d = entity.value?.description ?? entity.value?._data?.description;
    return d && String(d).trim() ? String(d) : "";
});

const linkedCapabilities = computed(() => {
    const raw = entity.value?._data?.capabilities ?? entity.value?.capabilities;
    return Array.isArray(raw) ? raw : [];
});

const hasLinkedCapabilities = computed(() => linkedCapabilities.value.length > 0);

const linkedCreatureTraits = computed(() => {
    const raw = entity.value?._data?.creatureTraits ?? entity.value?.creatureTraits;
    return Array.isArray(raw) ? raw : [];
});

const hasLinkedCreatureTraits = computed(() => linkedCreatureTraits.value.length > 0);

const hasSpellSlots = computed(() => {
    const raw = entity.value?._data ?? entity.value;
    return buildSpellSlotGroups(raw).length > 0;
});

const linkedLanguages = computed(() => {
    const raw = entity.value?._data?.languages ?? entity.value?.languages;
    return Array.isArray(raw) ? raw : [];
});

const hasLinkedLanguages = computed(() => linkedLanguages.value.length > 0);



const {
    minimalActionsContext,
    minimalActionWhitelist,
    openQuickView,
    handleMinimalAction,
} = useEntityMinimalShell({
    entityTypePlural: "breeds",
    showRoute: "entities.breeds.show",
    editRoute: "entities.breeds.edit",
    routeParam: "breed",
    emit,
    getEntity: () => entity.value,
});

const handleAction = async (actionKey) => {
    await handleMinimalAction(actionKey);
};

const handleLinkedQuickView = (linkedEntity) => {
    if (!linkedEntity) return;
    emit("action", "quick-view", linkedEntity);
};
</script>

<template>
    <EntityMinimalCard
        :display-mode="displayMode"
        pinned-entity-type="breeds"
        :pinned-entity-id="entity?.id"
        @open-quick-view="openQuickView"
    >
        <template #compact>
            <div
                data-cy="entity-minimal-card-compact"
                class="relative p-2 flex flex-col gap-1.5 transition-colors"
            >
                <div class="flex gap-2">
                    <EntityThumb
                        size="compact"
                        :src="imageUrl || ''"
                        :label="entity?.name ?? 'Classe'"
                    />
                    <div class="flex-1 min-w-0 flex flex-col gap-1 pl-0.5">
                        <div class="flex items-center gap-1.5">
                            <div class="min-w-0 flex-1">
                                <EntityMinimalTitle :label="entity?.name ?? '—'" @open="openQuickView" />
                            </div>
                            
                        </div>
                        <BreedElementOrientationsDisplay
                            :orientation-map="orientationMap"
                            size="xs"
                            class="w-full"
                        />
                        <BreedCapabilitiesDisplay
                            v-if="hasLinkedCapabilities"
                            :capabilities="linkedCapabilities"
                            density="text"
                            lightweight
                            @open-capability="handleLinkedQuickView"
                        />
                        <BreedVariantsDisplay
                            v-if="hasSpellSlots"
                            :breed="entity?._data ?? entity"
                            density="text"
                            lightweight
                            :show-temple-note="false"
                            @open-spell="handleLinkedQuickView"
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
                class="relative p-2 flex flex-col gap-1.5 transition-colors"
            >
                <div class="flex gap-2">
                    <EntityThumb
                        size="compact"
                        :src="imageUrl || ''"
                        :label="entity?.name ?? 'Classe'"
                    />
                    <div class="flex-1 min-w-0 flex flex-col gap-1 pl-0.5">
                        <div class="flex items-center gap-1.5">
                            <div class="min-w-0">
                                <EntityMinimalTitle :label="entity?.name ?? '—'" @open="openQuickView" />
                            </div>
                            <div v-if="showActions" data-entity-actions class="flex min-w-8 flex-1 justify-end" @click.stop>
                                <EntityActions
                                    entity-type="breeds"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="minimalActionWhitelist"
                                    :context="minimalActionsContext"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <BreedElementOrientationsDisplay
                            :orientation-map="orientationMap"
                            size="xs"
                            class="w-full"
                        />
                        <BreedCapabilitiesDisplay
                            v-if="hasLinkedCapabilities"
                            :capabilities="linkedCapabilities"
                            density="text"
                            lightweight
                            @open-capability="handleLinkedQuickView"
                        />
                        <CreatureTraitBadges
                            v-if="hasLinkedCreatureTraits"
                            :traits="linkedCreatureTraits"
                            show-level
                            size="xs"
                        />
                        <BreedVariantsDisplay
                            v-if="hasSpellSlots"
                            :breed="entity?._data ?? entity"
                            density="text"
                            lightweight
                            :show-temple-note="false"
                            @open-spell="handleLinkedQuickView"
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
                                class="min-w-0 max-w-[11rem]"
                            />
                        </div>
                        <p
                            v-if="descriptionFull"
                            class="text-xs text-base-content/80 line-clamp-4"
                            :title="descriptionFull"
                        >
                            {{ descriptionFull }}
                        </p>
                    </div>
                </div>
            </div>
        </template>
    </EntityMinimalCard>
</template>
