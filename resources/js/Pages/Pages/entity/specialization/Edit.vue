<script setup>
/**
 * Page d’édition dense d’une spécialisation.
 */
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Specialization } from '@/Models/Entity/Specialization';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Collapse from '@/Pages/Atoms/data-display/Collapse.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityListBackButton from '@/Pages/Atoms/action/EntityListBackButton.vue';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import CreatureTraitsEditor from '@/Pages/Organismes/entity/CreatureTraitsEditor.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import { getSpecializationFieldDescriptors } from '@/Entities/specialization/specialization-descriptors';
import { createFieldsConfigFromDescriptors } from '@/Utils/entity/descriptor-form';
import { SPECIALIZATION_FORM_FIELD_SECTIONS_EDIT } from '@/Entities/specialization/specialization-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    specialization: { type: Object, required: true },
    availableSpells: { type: Array, default: () => [] },
    availableCapabilities: { type: Array, default: () => [] },
    availableCreatureTraits: { type: Array, default: () => [] },
    availableConsumables: { type: Array, default: () => [] },
    availableResources: { type: Array, default: () => [] },
    availableItems: { type: Array, default: () => [] },
    availableSections: { type: Array, default: () => [] },
});

const specialization = computed(() => {
    const raw = props.specialization || page.props.specialization || {};
    return raw instanceof Specialization ? raw : new Specialization(raw);
});

setPageTitle(`Modifier la spécialisation : ${specialization.value.name || '-'}`);

const fieldsConfig = computed(() => {
    const ctx = {
        capabilities: { updateAny: true, createAny: false },
        meta: { capabilities: { updateAny: true } },
    };
    return createFieldsConfigFromDescriptors(getSpecializationFieldDescriptors(ctx), ctx);
});

const fieldSections = SPECIALIZATION_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';

function goToShow() {
    const id = specialization.value?.id;
    if (!id) return;
    router.visit(route('entities.specializations.show', { specialization: id }));
}

const confirmDelete = () => {
    const id = specialization.value?.id;
    if (!id) return;
    const ok = window.confirm('Supprimer cette spécialisation ? Elle sera placée en corbeille.');
    if (!ok) return;
    router.delete(route('entities.specializations.delete', { specialization: id }));
};
</script>

<template>
    <Head :title="`Modifier : ${specialization?.name || 'Spécialisation'}`" />

    <Container class="space-y-4 pb-28 md:pb-32">
        <div
            class="sticky top-0 z-20 px-3 py-1 bg-glass-3xl backdrop-blur-md border-glass-b-md sm:px-4"
            style="--bg-color: var(--color-base-100)"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-md font-bold text-base-content sm:text-lg">
                        {{ specialization.name || 'Spécialisation sans nom' }}
                    </h1>
                    <p class="text-xs text-base-content/60">
                        Édition · ID {{ specialization.id }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <EntityListBackButton route-name="entities.specializations.index" />
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
            :entity="specialization"
            entity-type="specialization"
            :fields-config="fieldsConfig"
            :is-updating="true"
            :field-sections="fieldSections"
            :show-state-toolbar="true"
            :show-access-levels-in-footer="false"
            layout-profile="dense"
            :fixed-footer-actions="true"
            :fixed-footer-inset-class="fixedFooterInsetClass"
            :footer-secondary-actions="false"
            :compact-access-levels="true"
        />

        <Collapse v-if="specialization.id" arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Sorts &amp; capacités</template>
            <template #content>
                <div class="space-y-4">
                    <EntityRelationsManager
                        :relations="specialization.spells || []"
                        :available-items="availableSpells"
                        :entity-id="specialization.id"
                        entity-type="specializations"
                        relation-type="spells"
                        relation-name="Sorts"
                        :config="{
                            itemLabel: 'sort',
                            itemLabelPlural: 'sorts',
                            displayFields: ['name'],
                            searchFields: ['name'],
                            routeName: 'entities.specializations.updateSpells',
                            relatedEntityType: 'spells',
                            searchApiEntityType: 'spells',
                            pivotFields: ['level'],
                        }"
                    />

                    <EntityRelationsManager
                        :relations="specialization.capabilities || []"
                        :available-items="availableCapabilities"
                        :entity-id="specialization.id"
                        entity-type="specializations"
                        relation-type="capabilities"
                        relation-name="Capacités"
                        :config="{
                            itemLabel: 'capacité',
                            itemLabelPlural: 'capacités',
                            displayFields: ['name'],
                            searchFields: ['name'],
                            routeName: 'entities.specializations.updateCapabilities',
                            relatedEntityType: 'capabilities',
                            searchApiEntityType: 'capabilities',
                            pivotFields: ['level'],
                        }"
                    />
                </div>
            </template>
        </Collapse>

        <Collapse v-if="specialization.id" arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Traits</template>
            <template #content>
                <CreatureTraitsEditor
                    :relations="specialization.creatureTraits || []"
                    :available-items="availableCreatureTraits"
                    :entity-id="specialization.id"
                    route-name="entities.specializations.updateCreatureTraits"
                    route-param-name="specialization"
                    title="Traits de spécialisation"
                    help="Traits permanents gagnés via cette spécialisation. Le niveau indique quand le trait devient actif."
                    with-level
                    search-api-entity-type="creature-traits"
                />
            </template>
        </Collapse>

        <Collapse v-if="specialization.id" arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Objets, conso &amp; ressources</template>
            <template #content>
                <div class="space-y-4">
                    <EntityRelationsManager
                        :relations="specialization.consumables || []"
                        :available-items="availableConsumables"
                        :entity-id="specialization.id"
                        entity-type="specializations"
                        relation-type="consumables"
                        relation-name="Consommables"
                        :config="{
                            itemLabel: 'consommable',
                            itemLabelPlural: 'consommables',
                            displayFields: ['name'],
                            searchFields: ['name'],
                            routeName: 'entities.specializations.updateConsumables',
                            relatedEntityType: 'consumables',
                            searchApiEntityType: 'consumables',
                            pivotFields: ['level', 'quantity'],
                        }"
                    />

                    <EntityRelationsManager
                        :relations="specialization.resources || []"
                        :available-items="availableResources"
                        :entity-id="specialization.id"
                        entity-type="specializations"
                        relation-type="resources"
                        relation-name="Ressources"
                        :config="{
                            itemLabel: 'ressource',
                            itemLabelPlural: 'ressources',
                            displayFields: ['name'],
                            searchFields: ['name'],
                            routeName: 'entities.specializations.updateResources',
                            relatedEntityType: 'resources',
                            searchApiEntityType: 'resources',
                            pivotFields: ['level', 'quantity'],
                        }"
                    />

                    <EntityRelationsManager
                        :relations="specialization.items || []"
                        :available-items="availableItems"
                        :entity-id="specialization.id"
                        entity-type="specializations"
                        relation-type="items"
                        relation-name="Items"
                        :config="{
                            itemLabel: 'item',
                            itemLabelPlural: 'items',
                            displayFields: ['name'],
                            searchFields: ['name'],
                            routeName: 'entities.specializations.updateItems',
                            relatedEntityType: 'items',
                            searchApiEntityType: 'items',
                            pivotFields: ['level', 'quantity'],
                        }"
                    />
                </div>
            </template>
        </Collapse>

        <Collapse v-if="specialization.id" arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Sections</template>
            <template #content>
                <EntityRelationsManager
                    :relations="specialization.sections || []"
                    :available-items="availableSections"
                    :entity-id="specialization.id"
                    entity-type="specializations"
                    relation-type="sections"
                    relation-name="Sections"
                    :config="{
                        itemLabel: 'section',
                        itemLabelPlural: 'sections',
                        displayFields: ['title', 'slug'],
                        searchFields: ['title', 'slug'],
                        routeName: 'entities.specializations.updateSections',
                        relatedEntityType: 'sections',
                        pivotFields: ['level'],
                    }"
                />
            </template>
        </Collapse>

        <div
            v-if="specialization?.can?.delete"
            class="flex justify-end pt-4 border-t border-base-300"
        >
            <Btn color="error" variant="ghost" size="sm" @click="confirmDelete">
                <i class="fa-solid fa-trash mr-2" aria-hidden="true"></i>
                Supprimer la spécialisation
            </Btn>
        </div>
    </Container>
</template>
