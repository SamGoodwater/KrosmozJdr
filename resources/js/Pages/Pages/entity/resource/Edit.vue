<script setup>
/**
 * Resource Edit Page
 *
 * @description
 * Édition dense d’une ressource : grille fieldSections + toolbar sticky (Liste / Fiche).
 *
 * @props {Object|null} resource - Données de la ressource (nullable en création via page /create)
 */
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Resource } from '@/Models/Entity/Resource';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import ObjectEffectsManager from '@/Pages/Organismes/entity/ObjectEffectsManager.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Collapse from '@/Pages/Atoms/data-display/Collapse.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityListBackButton from '@/Pages/Atoms/action/EntityListBackButton.vue';
import { getResourceFieldDescriptors } from '@/Entities/resource/resource-descriptors';
import { createFieldsConfigFromDescriptors } from '@/Utils/entity/descriptor-form';
import { RESOURCE_FORM_FIELD_SECTIONS_EDIT } from '@/Entities/resource/resource-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    resource: {
        type: Object,
        default: null,
    },
    resourceTypes: {
        type: Array,
        default: () => [],
    },
    availableShops: {
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
    availableResourcesForRecipe: {
        type: Array,
        default: () => [],
    },
    objectEffects: { type: Array, default: () => [] },
    objectEffectCharacteristics: { type: Array, default: () => [] },
    objectEffectMonsters: { type: Array, default: () => [] },
});

const ctx = computed(() => ({
    resourceTypes: props.resourceTypes,
    capabilities: page.props.auth?.user?.can || {},
    meta: {
        resourceTypes: props.resourceTypes,
        capabilities: page.props.auth?.user?.can || {},
    },
}));

const fieldsConfig = computed(() => {
    const descriptors = getResourceFieldDescriptors(ctx.value);
    return createFieldsConfigFromDescriptors(descriptors, ctx.value);
});

const fieldSections = RESOURCE_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';

const resource = computed(() => {
    const resourceData = props.resource || page.props.resource || {};
    return new Resource(resourceData || {});
});

setPageTitle(`Modifier la ressource : ${resource.value.name || 'Nouvelle ressource'}`);

function goToShow() {
    const id = resource.value?.id;
    if (!id) return;
    router.visit(route('entities.resources.show', { resource: id }));
}
</script>

<template>
    <Head :title="`Modifier la ressource : ${resource?.name || 'Nouvelle ressource'}`" />

    <Container class="space-y-4">
        <div
            class="sticky top-0 z-20 px-3 py-1 bg-glass-3xl backdrop-blur-md border-glass-b-md sm:px-4"
            style="--bg-color: var(--color-base-100)"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-md font-bold text-base-content sm:text-lg">
                        {{ resource.name || 'Ressource sans nom' }}
                    </h1>
                    <p class="text-xs text-base-content/60">
                        Édition · ID {{ resource.id || '—' }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <EntityListBackButton route-name="entities.resources.index" />
                    <Btn
                        v-if="resource.id"
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
            :entity="resource"
            entity-type="resource"
            :fields-config="fieldsConfig"
            :is-updating="!!resource?.id"
            :field-sections="fieldSections"
            :show-state-toolbar="true"
            :show-access-levels-in-footer="false"
            layout-profile="dense"
            :fixed-footer-actions="true"
            :fixed-footer-inset-class="fixedFooterInsetClass"
            :compact-access-levels="true"
        />

        <Collapse v-if="resource?.id" arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Effets d’objet</template>
            <template #content>
                <ObjectEffectsManager
                    :object-effects="objectEffects"
                    :object-effect-characteristics="objectEffectCharacteristics"
                    :object-effect-monsters="objectEffectMonsters"
                    entity-type="resource"
                    :entity-id="resource.id"
                />
            </template>
        </Collapse>

        <Collapse v-if="resource?.id" arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Recette (ingrédients)</template>
            <template #content>
                <EntityRelationsManager
                    :relations="resource.recipeIngredients || []"
                    :available-items="availableResourcesForRecipe"
                    :entity-id="resource.id"
                    entity-type="resources"
                    relation-type="recipe"
                    relation-name="Recette (ingrédients pour fabriquer cette ressource)"
                    :config="{
                        displayFields: ['name', 'description', 'level'],
                        searchFields: ['name', 'description'],
                        pivotFields: ['quantity'],
                        itemLabel: 'ressource',
                        itemLabelPlural: 'ressources'
                    }"
                />
            </template>
        </Collapse>

        <Collapse arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Hôtels de vente</template>
            <template #content>
                <EntityRelationsManager
                    :relations="resource.shops || []"
                    :available-items="availableShops"
                    :entity-id="resource.id"
                    entity-type="resources"
                    relation-type="shops"
                    relation-name="hotels de vente associées"
                    :config="{
                        displayFields: ['name', 'description'],
                        searchFields: ['name', 'description'],
                        pivotFields: ['quantity', 'price', 'comment'],
                        itemLabel: 'hotel de vente',
                        itemLabelPlural: 'hotels de vente'
                    }"
                />
            </template>
        </Collapse>

        <Collapse arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Scénarios &amp; campagnes</template>
            <template #content>
                <div class="space-y-4">
                    <EntityRelationsManager
                        :relations="resource.scenarios || []"
                        :available-items="availableScenarios"
                        :entity-id="resource.id"
                        entity-type="resources"
                        relation-type="scenarios"
                        relation-name="Scénarios associés"
                        :config="{
                            displayFields: ['name', 'description'],
                            searchFields: ['name', 'description'],
                            itemLabel: 'scénario',
                            itemLabelPlural: 'scénarios'
                        }"
                    />

                    <EntityRelationsManager
                        :relations="resource.campaigns || []"
                        :available-items="availableCampaigns"
                        :entity-id="resource.id"
                        entity-type="resources"
                        relation-type="campaigns"
                        relation-name="Campagnes associées"
                        :config="{
                            displayFields: ['name', 'description'],
                            searchFields: ['name', 'description'],
                            itemLabel: 'campagne',
                            itemLabelPlural: 'campagnes'
                        }"
                    />
                </div>
            </template>
        </Collapse>
    </Container>
</template>
