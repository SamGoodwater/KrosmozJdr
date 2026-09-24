<script setup>
/**
 * Page d’édition dense d’une classe (Breed).
 *
 * @props {Object} breed - BreedResource
 * @props {Array} availableSpells - Sorts disponibles pour la liaison pivot
 */
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { Breed } from '@/Models/Entity/Breed';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import BreedSpellSlotsEditor from '@/Pages/Organismes/entity/BreedSpellSlotsEditor.vue';
import BreedCapabilitiesEditor from '@/Pages/Organismes/entity/BreedCapabilitiesEditor.vue';
import CreatureTraitsEditor from '@/Pages/Organismes/entity/CreatureTraitsEditor.vue';
import EntityLanguagesEditor from '@/Pages/Organismes/entity/EntityLanguagesEditor.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import BreedElementOrientationsEditor from '@/Pages/Organismes/entity/BreedElementOrientationsEditor.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Collapse from '@/Pages/Atoms/data-display/Collapse.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityListBackButton from '@/Pages/Atoms/action/EntityListBackButton.vue';
import { getBreedFieldDescriptors } from '@/Entities/breed/breed-descriptors';
import { createFieldsConfigFromDescriptors } from '@/Utils/entity/descriptor-form';
import { BREED_FORM_FIELD_SECTIONS_EDIT } from '@/Entities/breed/breed-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();
const { canUpdateAny } = usePermissions();
const canModify = computed(() => canUpdateAny('breeds'));

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
    () =>
        props.breedOrientationKeys?.length
            ? props.breedOrientationKeys
            : page.props.breedOrientationKeys || [],
);

const fieldsConfig = computed(() => {
    const ctx = {
        capabilities: { updateAny: canModify.value, createAny: false },
        meta: { capabilities: { updateAny: canModify.value } },
    };
    return createFieldsConfigFromDescriptors(getBreedFieldDescriptors(ctx), ctx);
});

const fieldSections = BREED_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';

setPageTitle(`Modifier la classe : ${breed.value.name || '-'}`);

function goToShow() {
    const id = breed.value?.id;
    if (!id) return;
    router.visit(route('entities.breeds.show', { breed: id }));
}

const confirmDelete = () => {
    const id = breed.value?.id;
    if (!id) return;
    const ok = window.confirm(
        'Supprimer cette classe ? Elle sera placée en corbeille (récupération possible côté admin).',
    );
    if (!ok) return;
    router.delete(route('entities.breeds.delete', { breed: id }));
};
</script>

<template>
    <Head :title="`Modifier : ${breed?.name || 'Classe'}`" />

    <Container class="space-y-4 pb-28 md:pb-32">
        <div
            class="sticky top-0 z-20 px-3 py-1 bg-glass-3xl backdrop-blur-md border-glass-b-md sm:px-4"
            style="--bg-color: var(--color-base-100)"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-md font-bold text-base-content sm:text-lg">
                        {{ breed.name || 'Classe sans nom' }}
                    </h1>
                    <p class="text-xs text-base-content/60">
                        Édition · ID {{ breed.id }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <EntityListBackButton route-name="entities.breeds.index" />
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
            :entity="breed"
            entity-type="breed"
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

        <Collapse v-if="breed.id" arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Orientations élémentaires</template>
            <template #content>
                <BreedElementOrientationsEditor
                    :breed-id="breed.id"
                    :initial-map="breed.elementOrientations"
                    :orientation-keys="orientationKeyOptions"
                />
            </template>
        </Collapse>

        <Collapse v-if="breed.id" arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Sorts &amp; capacités</template>
            <template #content>
                <div class="space-y-4">
                    <BreedSpellSlotsEditor
                        :relations="breed.spells || []"
                        :available-items="availableSpells"
                        :entity-id="breed.id"
                    />
                    <BreedCapabilitiesEditor
                        :relations="breed.capabilities || []"
                        :available-items="availableCapabilities"
                        :entity-id="breed.id"
                    />
                </div>
            </template>
        </Collapse>

        <Collapse v-if="breed.id" arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Traits &amp; langues</template>
            <template #content>
                <div class="space-y-4">
                    <CreatureTraitsEditor
                        :relations="breed.creatureTraits || []"
                        :available-items="availableCreatureTraits"
                        :entity-id="breed.id"
                        route-name="entities.breeds.updateCreatureTraits"
                        route-param-name="breed"
                        title="Traits de classe"
                        help="Traits permanents gagnés par les personnages de cette classe. Le niveau indique quand le trait devient actif."
                        with-level
                        search-api-entity-type="creature-traits"
                    />
                    <EntityLanguagesEditor
                        entity-type="breed"
                        :relations="breed.languages || []"
                        :available-items="availableLanguages"
                        :entity-id="breed.id"
                    />
                </div>
            </template>
        </Collapse>

        <Collapse v-if="breed.id" arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Sections</template>
            <template #content>
                <EntityRelationsManager
                    :relations="breed.sections || []"
                    :available-items="availableSections"
                    :entity-id="breed.id"
                    entity-type="breeds"
                    relation-type="sections"
                    relation-name="Sections"
                    :config="{
                        itemLabel: 'section',
                        itemLabelPlural: 'sections',
                        displayFields: ['title', 'slug'],
                        searchFields: ['title', 'slug'],
                        routeName: 'entities.breeds.updateSections',
                        relatedEntityType: 'sections',
                        pivotFields: ['level'],
                    }"
                />
            </template>
        </Collapse>

        <div v-if="breed?.can?.delete" class="flex justify-end pt-4 border-t border-base-300">
            <Btn color="error" variant="ghost" size="sm" @click="confirmDelete">
                <i class="fa-solid fa-trash mr-2" aria-hidden="true"></i>
                Supprimer la classe
            </Btn>
        </div>
    </Container>
</template>
