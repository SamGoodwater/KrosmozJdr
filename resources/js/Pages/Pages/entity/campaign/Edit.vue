<script setup>
/**
 * Campaign Edit Page
 *
 * @description
 * Édition dense d’une campagne : grille fieldSections + relations en collapses.
 */
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Campaign } from '@/Models/Entity/Campaign';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Collapse from '@/Pages/Atoms/data-display/Collapse.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityListBackButton from '@/Pages/Atoms/action/EntityListBackButton.vue';
import {
    buildCampaignFormFieldsConfig,
    CAMPAIGN_FORM_FIELD_SECTIONS_EDIT,
} from '@/Entities/campaign/campaign-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    campaign: {
        type: Object,
        required: true,
    },
    availableUsers: {
        type: Array,
        default: () => [],
    },
    availableScenarios: {
        type: Array,
        default: () => [],
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
    buildCampaignFormFieldsConfig({ includeReadonlyMeta: true }),
);
const fieldSections = CAMPAIGN_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';

const campaign = computed(() => {
    const campaignData = props.campaign || page.props.campaign || {};
    return new Campaign(campaignData);
});

setPageTitle(`Modifier la campagne : ${campaign.value.name || 'Nouvelle campagne'}`);

function goToShow() {
    const id = campaign.value?.id;
    if (!id) return;
    router.visit(route('entities.campaigns.show', { campaign: id }));
}
</script>

<template>
    <Head :title="`Modifier la campagne : ${campaign?.name || 'Nouvelle campagne'}`" />

    <Container class="space-y-4">
        <div
            class="sticky top-0 z-20 px-3 py-1 bg-glass-3xl backdrop-blur-md border-glass-b-md sm:px-4"
            style="--bg-color: var(--color-base-100)"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-md font-bold text-base-content sm:text-lg">
                        {{ campaign.name || 'Campagne sans nom' }}
                    </h1>
                    <p class="text-xs text-base-content/60">
                        Édition · ID {{ campaign.id }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <EntityListBackButton route-name="entities.campaigns.index" />
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
            :entity="campaign"
            entity-type="campaign"
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
            <template #title>Participants &amp; scénarios</template>
            <template #content>
                <div class="space-y-4">
                    <EntityRelationsManager
                        :relations="campaign.users || []"
                        :available-items="availableUsers"
                        :entity-id="campaign.id"
                        entity-type="campaigns"
                        relation-type="users"
                        relation-name="Utilisateurs de la campagne"
                        :config="{
                            displayFields: ['name', 'email'],
                            searchFields: ['name', 'email'],
                            itemLabel: 'utilisateur',
                            itemLabelPlural: 'utilisateurs'
                        }"
                    />

                    <EntityRelationsManager
                        :relations="campaign.scenarios || []"
                        :available-items="availableScenarios"
                        :entity-id="campaign.id"
                        entity-type="campaigns"
                        relation-type="scenarios"
                        relation-name="Scénarios de la campagne"
                        :config="{
                            displayFields: ['name', 'description'],
                            searchFields: ['name', 'description'],
                            itemLabel: 'scénario',
                            itemLabelPlural: 'scénarios'
                        }"
                    />
                </div>
            </template>
        </Collapse>

        <Collapse arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Contenu lié</template>
            <template #content>
                <div class="space-y-4">
                    <EntityRelationsManager
                        :relations="campaign.items || []"
                        :available-items="availableItems"
                        :entity-id="campaign.id"
                        entity-type="campaigns"
                        relation-type="items"
                        relation-name="Objets de la campagne"
                        :config="{
                            displayFields: ['name', 'description', 'level'],
                            searchFields: ['name', 'description'],
                            itemLabel: 'objet',
                            itemLabelPlural: 'objets'
                        }"
                    />

                    <EntityRelationsManager
                        :relations="campaign.consumables || []"
                        :available-items="availableConsumables"
                        :entity-id="campaign.id"
                        entity-type="campaigns"
                        relation-type="consumables"
                        relation-name="Consommables de la campagne"
                        :config="{
                            displayFields: ['name', 'description', 'level'],
                            searchFields: ['name', 'description'],
                            itemLabel: 'consommable',
                            itemLabelPlural: 'consommables'
                        }"
                    />

                    <EntityRelationsManager
                        :relations="campaign.resources || []"
                        :available-items="availableResources"
                        :entity-id="campaign.id"
                        entity-type="campaigns"
                        relation-type="resources"
                        relation-name="Ressources de la campagne"
                        :config="{
                            displayFields: ['name', 'description', 'level'],
                            searchFields: ['name', 'description'],
                            itemLabel: 'ressource',
                            itemLabelPlural: 'ressources'
                        }"
                    />

                    <EntityRelationsManager
                        :relations="campaign.spells || []"
                        :available-items="availableSpells"
                        :entity-id="campaign.id"
                        entity-type="campaigns"
                        relation-type="spells"
                        relation-name="Sorts de la campagne"
                        :config="{
                            displayFields: ['name', 'description', 'level'],
                            searchFields: ['name', 'description'],
                            itemLabel: 'sort',
                            itemLabelPlural: 'sorts'
                        }"
                    />

                    <EntityRelationsManager
                        :relations="campaign.panoplies || []"
                        :available-items="availablePanoplies"
                        :entity-id="campaign.id"
                        entity-type="campaigns"
                        relation-type="panoplies"
                        relation-name="Panoplies de la campagne"
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
