<script setup>
/**
 * Page d’édition d’une classe (Breed).
 *
 * @props {Object} breed - BreedResource
 * @props {Array} availableSpells - Sorts disponibles pour la liaison pivot
 */
import { computed } from "vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { Breed } from "@/Models/Entity/Breed";
import EntityEditForm from "@/Pages/Organismes/entity/EntityEditForm.vue";
import BreedSpellSlotsEditor from "@/Pages/Organismes/entity/BreedSpellSlotsEditor.vue";
import BreedCapabilitiesEditor from "@/Pages/Organismes/entity/BreedCapabilitiesEditor.vue";
import CreatureTraitsEditor from "@/Pages/Organismes/entity/CreatureTraitsEditor.vue";
import EntityLanguagesEditor from "@/Pages/Organismes/entity/EntityLanguagesEditor.vue";
import EntityRelationsManager from "@/Pages/Organismes/entity/EntityRelationsManager.vue";
import BreedElementOrientationsEditor from "@/Pages/Organismes/entity/BreedElementOrientationsEditor.vue";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import { getBreedFieldDescriptors } from "@/Entities/breed/breed-descriptors";
import { createFieldsConfigFromDescriptors } from "@/Utils/entity/descriptor-form";

const page = usePage();
const { setPageTitle } = usePageTitle();
const { canUpdateAny } = usePermissions();
const canModify = computed(() => canUpdateAny("breeds"));

const props = defineProps({
    breed: {
        type: Object,
        required: true,
    },
    availableSpells: {
        type: Array,
        default: () => [],
    },
    breedOrientationKeys: {
        type: Array,
        default: () => [],
    },
    availableCapabilities: {
        type: Array,
        default: () => [],
    },
    availableCreatureTraits: {
        type: Array,
        default: () => [],
    },
    availableLanguages: {
        type: Array,
        default: () => [],
    },
    availableSections: {
        type: Array,
        default: () => [],
    },
});

const breed = computed(() => {
    const raw = props.breed || page.props.breed || {};
    return raw instanceof Breed ? raw : new Breed(raw);
});

const orientationKeyOptions = computed(
    () => props.breedOrientationKeys?.length ? props.breedOrientationKeys : page.props.breedOrientationKeys || []
);

const fieldsConfig = computed(() => {
    const ctx = {
        capabilities: { updateAny: canModify.value, createAny: false },
        meta: { capabilities: { updateAny: canModify.value } },
    };
    return createFieldsConfigFromDescriptors(getBreedFieldDescriptors(ctx), ctx);
});

setPageTitle(`Modifier la classe : ${breed.value.name || "-"}`);

const confirmDelete = () => {
    const id = breed.value?.id;
    if (!id) return;
    const ok = window.confirm(
        "Supprimer cette classe ? Elle sera placée en corbeille (récupération possible côté admin)."
    );
    if (!ok) return;
    router.delete(route("entities.breeds.delete", { breed: id }));
};
</script>

<template>
    <Head :title="`Modifier : ${breed?.name || 'Classe'}`" />

    <Container class="space-y-6 pb-28 md:pb-32">
        <Route route="entities.breeds.index">
            <Btn color="neutral" variant="ghost" size="sm" class="gap-2">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Retour à la liste
            </Btn>
        </Route>

        <EntityEditForm
            :entity="breed"
            entity-type="breed"
            :fields-config="fieldsConfig"
            :is-updating="true"
            :fixed-footer-actions="true"
            :footer-secondary-actions="false"
            :compact-access-levels="true"
        />

        <BreedElementOrientationsEditor
            v-if="breed.id"
            :breed-id="breed.id"
            :initial-map="breed.elementOrientations"
            :orientation-keys="orientationKeyOptions"
        />

        <BreedSpellSlotsEditor
            v-if="breed.id"
            :relations="breed.spells || []"
            :available-items="availableSpells"
            :entity-id="breed.id"
        />

        <BreedCapabilitiesEditor
            v-if="breed.id"
            :relations="breed.capabilities || []"
            :available-items="availableCapabilities"
            :entity-id="breed.id"
        />

        <CreatureTraitsEditor
            v-if="breed.id"
            :relations="breed.creatureTraits || []"
            :available-items="availableCreatureTraits"
            :entity-id="breed.id"
            route-name="entities.breeds.updateCreatureTraits"
            route-param-name="breed"
            title="Traits de classe"
            help="Traits permanents gagnés par les personnages de cette classe. Le niveau indique quand le trait devient actif."
            with-level
        />

        <EntityLanguagesEditor
            v-if="breed.id"
            entity-type="breed"
            :relations="breed.languages || []"
            :available-items="availableLanguages"
            :entity-id="breed.id"
        />

        <EntityRelationsManager
            v-if="breed.id"
            :relations="breed.sections || []"
            :available-items="availableSections"
            :entity-id="breed.id"
            entity-type="breeds"
            relation-type="sections"
            relation-name="Sections"
            :config="{ itemLabel: 'section', itemLabelPlural: 'sections', displayFields: ['title', 'slug'], searchFields: ['title', 'slug'], routeName: 'entities.breeds.updateSections', relatedEntityType: 'sections', pivotFields: ['level'] }"
        />

        <div v-if="breed?.can?.delete" class="flex justify-end pt-4 border-t border-base-300">
            <Btn color="error" variant="ghost" size="sm" @click="confirmDelete">
                <i class="fa-solid fa-trash mr-2" aria-hidden="true"></i>
                Supprimer la classe
            </Btn>
        </div>
    </Container>
</template>
