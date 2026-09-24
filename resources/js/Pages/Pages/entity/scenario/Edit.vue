<script setup>
/**
 * Scenario Edit Page
 *
 * @description
 * Édition dense d’un scénario : grille fieldSections + relations en collapses.
 *
 * @props {Object} scenario - Données du scénario à éditer
 */
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Scenario } from '@/Models/Entity/Scenario';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Collapse from '@/Pages/Atoms/data-display/Collapse.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityListBackButton from '@/Pages/Atoms/action/EntityListBackButton.vue';
import {
    buildScenarioFormFieldsConfig,
    SCENARIO_FORM_FIELD_SECTIONS_EDIT,
} from '@/Entities/scenario/scenario-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    scenario: {
        type: Object,
        required: true,
    },
    availableItems: {
        type: Array,
        default: () => [],
    },
    availableConsumables: {
        type: Array,
        default: () => [],
    },
    availableResources: {
        type: Array,
        default: () => [],
    },
    availableSpells: {
        type: Array,
        default: () => [],
    },
    availablePanoplies: {
        type: Array,
        default: () => [],
    },
});

const fieldsConfig = computed(() =>
    buildScenarioFormFieldsConfig({ includeReadonlyMeta: true }),
);
const fieldSections = SCENARIO_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';

const scenario = computed(() => {
    const scenarioData = props.scenario || page.props.scenario || {};
    return new Scenario(scenarioData);
});

setPageTitle(`Modifier le scénario : ${scenario.value.name || 'Nouveau scénario'}`);

function goToShow() {
    const id = scenario.value?.id;
    if (!id) return;
    router.visit(route('entities.scenarios.show', { scenario: id }));
}
</script>

<template>
    <Head :title="`Modifier le scénario : ${scenario?.name || 'Nouveau scénario'}`" />

    <Container class="space-y-4">
        <div
            class="sticky top-0 z-20 px-3 py-1 bg-glass-3xl backdrop-blur-md border-glass-b-md sm:px-4"
            style="--bg-color: var(--color-base-100)"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-md font-bold text-base-content sm:text-lg">
                        {{ scenario.name || 'Scénario sans nom' }}
                    </h1>
                    <p class="text-xs text-base-content/60">
                        Édition · ID {{ scenario.id }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <EntityListBackButton route-name="entities.scenarios.index" />
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
            :entity="scenario"
            entity-type="scenario"
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
            <template #title>Contenu lié</template>
            <template #content>
                <div class="space-y-4">
                    <EntityRelationsManager
                        :relations="scenario.items || []"
                        :available-items="availableItems"
                        :entity-id="scenario.id"
                        entity-type="scenarios"
                        relation-type="items"
                        relation-name="Objets du scénario"
                        :config="{
                            displayFields: ['name', 'description', 'level'],
                            searchFields: ['name', 'description'],
                            itemLabel: 'objet',
                            itemLabelPlural: 'objets'
                        }"
                    />

                    <EntityRelationsManager
                        :relations="scenario.consumables || []"
                        :available-items="availableConsumables"
                        :entity-id="scenario.id"
                        entity-type="scenarios"
                        relation-type="consumables"
                        relation-name="Consommables du scénario"
                        :config="{
                            displayFields: ['name', 'description', 'level'],
                            searchFields: ['name', 'description'],
                            itemLabel: 'consommable',
                            itemLabelPlural: 'consommables'
                        }"
                    />

                    <EntityRelationsManager
                        :relations="scenario.resources || []"
                        :available-items="availableResources"
                        :entity-id="scenario.id"
                        entity-type="scenarios"
                        relation-type="resources"
                        relation-name="Ressources du scénario"
                        :config="{
                            displayFields: ['name', 'description', 'level'],
                            searchFields: ['name', 'description'],
                            itemLabel: 'ressource',
                            itemLabelPlural: 'ressources'
                        }"
                    />

                    <EntityRelationsManager
                        :relations="scenario.spells || []"
                        :available-items="availableSpells"
                        :entity-id="scenario.id"
                        entity-type="scenarios"
                        relation-type="spells"
                        relation-name="Sorts du scénario"
                        :config="{
                            displayFields: ['name', 'description', 'level'],
                            searchFields: ['name', 'description'],
                            itemLabel: 'sort',
                            itemLabelPlural: 'sorts'
                        }"
                    />

                    <EntityRelationsManager
                        :relations="scenario.panoplies || []"
                        :available-items="availablePanoplies"
                        :entity-id="scenario.id"
                        entity-type="scenarios"
                        relation-type="panoplies"
                        relation-name="Panoplies du scénario"
                        :config="{
                            displayFields: ['name', 'description'],
                            searchFields: ['name', 'description'],
                            itemLabel: 'panoplie',
                            itemLabelPlural: 'panoplies'
                        }"
                    />
                </div>
            </template>
        </Collapse>
    </Container>
</template>
