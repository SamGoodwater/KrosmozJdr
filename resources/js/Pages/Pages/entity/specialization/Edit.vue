<script setup>
import { computed } from "vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { Specialization } from "@/Models/Entity/Specialization";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import EntityEditForm from "@/Pages/Organismes/entity/EntityEditForm.vue";
import CreatureTraitsEditor from "@/Pages/Organismes/entity/CreatureTraitsEditor.vue";
import EntityRelationsManager from "@/Pages/Organismes/entity/EntityRelationsManager.vue";
import { getSpecializationFieldDescriptors } from "@/Entities/specialization/specialization-descriptors";
import { createFieldsConfigFromDescriptors } from "@/Utils/entity/descriptor-form";

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

setPageTitle(`Modifier la spécialisation : ${specialization.value.name || "-"}`);

const fieldsConfig = computed(() => {
    const ctx = { capabilities: { updateAny: true, createAny: false }, meta: { capabilities: { updateAny: true } } };
    return createFieldsConfigFromDescriptors(getSpecializationFieldDescriptors(ctx), ctx);
});

const confirmDelete = () => {
    const id = specialization.value?.id;
    if (!id) return;
    const ok = window.confirm("Supprimer cette spécialisation ? Elle sera placée en corbeille.");
    if (!ok) return;
    router.delete(route("entities.specializations.delete", { specialization: id }));
};
</script>

<template>
    <Head :title="`Modifier : ${specialization?.name || 'Spécialisation'}`" />

    <Container class="space-y-6 pb-28 md:pb-32">
        <Route route="entities.specializations.index">
            <Btn color="neutral" variant="ghost" size="sm" class="gap-2">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Retour à la liste
            </Btn>
        </Route>

        <EntityEditForm
            :entity="specialization"
            entity-type="specialization"
            :fields-config="fieldsConfig"
            :is-updating="true"
            :fixed-footer-actions="true"
            :footer-secondary-actions="false"
            :compact-access-levels="true"
        />

        <EntityRelationsManager
            v-if="specialization.id"
            :relations="specialization.spells || []"
            :available-items="availableSpells"
            :entity-id="specialization.id"
            entity-type="specializations"
            relation-type="spells"
            relation-name="Sorts"
            :config="{ itemLabel: 'sort', itemLabelPlural: 'sorts', displayFields: ['name'], searchFields: ['name'], routeName: 'entities.specializations.updateSpells', relatedEntityType: 'spells', pivotFields: ['level'] }"
        />

        <EntityRelationsManager
            v-if="specialization.id"
            :relations="specialization.capabilities || []"
            :available-items="availableCapabilities"
            :entity-id="specialization.id"
            entity-type="specializations"
            relation-type="capabilities"
            relation-name="Capacités"
            :config="{ itemLabel: 'capacité', itemLabelPlural: 'capacités', displayFields: ['name'], searchFields: ['name'], routeName: 'entities.specializations.updateCapabilities', relatedEntityType: 'capabilities', pivotFields: ['level'] }"
        />

        <CreatureTraitsEditor
            v-if="specialization.id"
            :relations="specialization.creatureTraits || []"
            :available-items="availableCreatureTraits"
            :entity-id="specialization.id"
            route-name="entities.specializations.updateCreatureTraits"
            route-param-name="specialization"
            title="Traits de spécialisation"
            help="Traits permanents gagnés via cette spécialisation. Le niveau indique quand le trait devient actif."
            with-level
        />

        <EntityRelationsManager
            v-if="specialization.id"
            :relations="specialization.consumables || []"
            :available-items="availableConsumables"
            :entity-id="specialization.id"
            entity-type="specializations"
            relation-type="consumables"
            relation-name="Consommables"
            :config="{ itemLabel: 'consommable', itemLabelPlural: 'consommables', displayFields: ['name'], searchFields: ['name'], routeName: 'entities.specializations.updateConsumables', relatedEntityType: 'consumables', pivotFields: ['level', 'quantity'] }"
        />

        <EntityRelationsManager
            v-if="specialization.id"
            :relations="specialization.resources || []"
            :available-items="availableResources"
            :entity-id="specialization.id"
            entity-type="specializations"
            relation-type="resources"
            relation-name="Ressources"
            :config="{ itemLabel: 'ressource', itemLabelPlural: 'ressources', displayFields: ['name'], searchFields: ['name'], routeName: 'entities.specializations.updateResources', relatedEntityType: 'resources', pivotFields: ['level', 'quantity'] }"
        />

        <EntityRelationsManager
            v-if="specialization.id"
            :relations="specialization.items || []"
            :available-items="availableItems"
            :entity-id="specialization.id"
            entity-type="specializations"
            relation-type="items"
            relation-name="Items"
            :config="{ itemLabel: 'item', itemLabelPlural: 'items', displayFields: ['name'], searchFields: ['name'], routeName: 'entities.specializations.updateItems', relatedEntityType: 'items', pivotFields: ['level', 'quantity'] }"
        />

        <EntityRelationsManager
            v-if="specialization.id"
            :relations="specialization.sections || []"
            :available-items="availableSections"
            :entity-id="specialization.id"
            entity-type="specializations"
            relation-type="sections"
            relation-name="Sections"
            :config="{ itemLabel: 'section', itemLabelPlural: 'sections', displayFields: ['title', 'slug'], searchFields: ['title', 'slug'], routeName: 'entities.specializations.updateSections', relatedEntityType: 'sections', pivotFields: ['level'] }"
        />

        <div v-if="specialization?.can?.delete" class="flex justify-end pt-4 border-t border-base-300">
            <Btn color="error" variant="ghost" size="sm" @click="confirmDelete">
                <i class="fa-solid fa-trash mr-2" aria-hidden="true"></i>
                Supprimer la spécialisation
            </Btn>
        </div>
    </Container>
</template>
