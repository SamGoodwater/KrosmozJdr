<script setup>
/**
 * Page d’édition dense d’un PNJ : identité + kit de jeu en collapses.
 */
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Npc } from '@/Models/Entity/Npc';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import EntityLanguagesEditor from '@/Pages/Organismes/entity/EntityLanguagesEditor.vue';
import CreatureTraitsEditor from '@/Pages/Organismes/entity/CreatureTraitsEditor.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Collapse from '@/Pages/Atoms/data-display/Collapse.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityListBackButton from '@/Pages/Atoms/action/EntityListBackButton.vue';
import {
    buildNpcFormFieldsConfig,
    NPC_FORM_FIELD_SECTIONS_EDIT,
} from '@/Entities/npc/npc-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    npc: {
        type: Object,
        required: true,
    },
    availablePanoplies: {
        type: Array,
        default: () => [],
    },
    availableScenarios: {
        type: Array,
        default: () => [],
    },
    availableCampaigns: {
        type: Array,
        default: () => [],
    },
    availableSpells: {
        type: Array,
        default: () => [],
    },
    availableItems: {
        type: Array,
        default: () => [],
    },
    availableLanguages: {
        type: Array,
        default: () => [],
    },
    availableCreatureTraits: {
        type: Array,
        default: () => [],
    },
    availableBreeds: {
        type: Array,
        default: () => [],
    },
    availableSpecializations: {
        type: Array,
        default: () => [],
    },
});

const fieldsConfig = computed(() =>
    buildNpcFormFieldsConfig({
        breeds: props.availableBreeds || [],
        specializations: props.availableSpecializations || [],
    }),
);

const fieldSections = NPC_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';

const npc = computed(() => {
    const npcData = props.npc || page.props.npc || {};
    return npcData instanceof Npc ? npcData : new Npc(npcData);
});

const npcName = computed(() => npc.value.creature?.name || npc.value.name || 'Nouveau PNJ');

const creatureSpells = computed(
    () => npc.value.creature?.spells ?? npc.value._data?.creature?.spells ?? [],
);
const creatureItems = computed(
    () => npc.value.creature?.items ?? npc.value._data?.creature?.items ?? [],
);

setPageTitle(`Modifier le PNJ : ${npcName.value}`);

function goToShow() {
    const id = npc.value?.id;
    if (!id) return;
    router.visit(route('entities.npcs.show', { npc: id }));
}
</script>

<template>
    <Head :title="`Modifier le PNJ : ${npcName}`" />

    <Container class="space-y-4">
        <div
            class="sticky top-0 z-20 px-3 py-1 bg-glass-3xl backdrop-blur-md border-glass-b-md sm:px-4"
            style="--bg-color: var(--color-base-100)"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-md font-bold text-base-content sm:text-lg">
                        {{ npcName }}
                    </h1>
                    <p class="text-xs text-base-content/60">
                        Édition · ID {{ npc.id }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <EntityListBackButton route-name="entities.npcs.index" />
                    <Btn
                        color="neutral"
                        variant="outline"
                        size="xs"
                        type="button"
                        class="gap-1.5"
                        @click="goToShow"
                    >
                        <i class="fa-solid fa-book-open" aria-hidden="true"></i>
                        Fiche
                    </Btn>
                </div>
            </div>
        </div>

        <EntityEditForm
            :entity="npc"
            entity-type="npc"
            :fields-config="fieldsConfig"
            :is-updating="true"
            :field-sections="fieldSections"
            :show-state-toolbar="true"
            :show-access-levels-in-footer="false"
            layout-profile="dense"
            :fixed-footer-actions="true"
            :fixed-footer-inset-class="fixedFooterInsetClass"
            :compact-access-levels="true"
        />

        <Collapse arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Langues &amp; traits</template>
            <template #content>
                <div class="space-y-4">
                    <EntityLanguagesEditor
                        v-if="npc.id"
                        entity-type="npc"
                        :relations="npc.languages || []"
                        :available-items="availableLanguages"
                        :entity-id="npc.id"
                    />
                    <CreatureTraitsEditor
                        v-if="npc.id"
                        :relations="npc.creature?.creatureTraits || []"
                        :available-items="availableCreatureTraits"
                        :entity-id="npc.id"
                        route-name="entities.npcs.updateCreatureTraits"
                        route-param-name="npc"
                        title="Traits du PNJ"
                        help="Traits innés du PNJ, attachés à sa créature."
                    />
                </div>
            </template>
        </Collapse>

        <Collapse arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Sorts</template>
            <template #content>
                <div class="space-y-4">
                    <EntityRelationsManager
                        :relations="creatureSpells"
                        :available-items="availableSpells"
                        :entity-id="npc.id"
                        entity-type="npcs"
                        relation-type="spells"
                        relation-name="Sorts du PNJ"
                        :config="{
                            displayFields: ['name', 'description', 'level'],
                            searchFields: ['name', 'description'],
                            routeName: 'entities.npcs.updateSpells',
                            itemLabel: 'sort',
                            itemLabelPlural: 'sorts',
                            relatedEntityType: 'spells',
                            searchApiEntityType: 'spells',
                        }"
                    />
                    <p v-if="npc.breedId" class="text-xs text-base-content/60">
                        La liste proposée est préfiltrée sur la classe du PNJ ; la recherche catalogue
                        permet d’ajouter d’autres sorts.
                    </p>
                </div>
            </template>
        </Collapse>

        <Collapse arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Équipements</template>
            <template #content>
                <div class="space-y-4">
                    <EntityRelationsManager
                        :relations="creatureItems"
                        :available-items="availableItems"
                        :entity-id="npc.id"
                        entity-type="npcs"
                        relation-type="items"
                        relation-name="Équipement porté"
                        :config="{
                            displayFields: ['name', 'description', 'level'],
                            searchFields: ['name', 'description'],
                            routeName: 'entities.npcs.updateItems',
                            itemLabel: 'objet',
                            itemLabelPlural: 'objets',
                            relatedEntityType: 'items',
                            searchApiEntityType: 'items',
                        }"
                    />
                    <p class="text-xs text-base-content/60">
                        Un objet par emplacement (deux anneaux). Les types hors équipement sont refusés.
                    </p>
                </div>
            </template>
        </Collapse>

        <Collapse arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Panoplies</template>
            <template #content>
                <EntityRelationsManager
                    :relations="npc.panoplies || []"
                    :available-items="availablePanoplies"
                    :entity-id="npc.id"
                    entity-type="npcs"
                    relation-type="panoplies"
                    relation-name="Panoplies du PNJ"
                    :config="{
                        displayFields: ['name', 'description'],
                        searchFields: ['name', 'description'],
                        itemLabel: 'panoplie',
                        itemLabelPlural: 'panoplies',
                        searchApiEntityType: 'panoplies',
                    }"
                />
            </template>
        </Collapse>

        <Collapse arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Scénarios &amp; campagnes</template>
            <template #content>
                <div class="space-y-4">
                    <EntityRelationsManager
                        :relations="npc.scenarios || []"
                        :available-items="availableScenarios"
                        :entity-id="npc.id"
                        entity-type="npcs"
                        relation-type="scenarios"
                        relation-name="Scénarios du PNJ"
                        :config="{
                            displayFields: ['name', 'description'],
                            searchFields: ['name', 'description'],
                            itemLabel: 'scénario',
                            itemLabelPlural: 'scénarios',
                            searchApiEntityType: 'scenarios',
                        }"
                    />

                    <EntityRelationsManager
                        :relations="npc.campaigns || []"
                        :available-items="availableCampaigns"
                        :entity-id="npc.id"
                        entity-type="npcs"
                        relation-type="campaigns"
                        relation-name="Campagnes du PNJ"
                        :config="{
                            displayFields: ['name', 'description'],
                            searchFields: ['name', 'description'],
                            itemLabel: 'campagne',
                            itemLabelPlural: 'campagnes',
                            searchApiEntityType: 'campaigns',
                        }"
                    />
                </div>
            </template>
        </Collapse>
    </Container>
</template>
