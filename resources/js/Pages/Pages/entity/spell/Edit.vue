<script setup>
/**
 * Page d’édition d’un sort — formulaire structuré par sections (généralités, gameplay, PO, résolution, admin).
 *
 * @props {Object} spell - Données du sort à éditer
 */
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Spell } from '@/Models/Entity/Spell';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import SpellEffectsUnifiedSection from '@/Pages/Organismes/entity/SpellEffectsUnifiedSection.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Route from '@/Pages/Atoms/action/Route.vue';
import {
    buildSpellFormFieldsConfig,
    SPELL_FORM_FIELD_SECTIONS_EDIT,
    mergeSpellTypesFieldIntoSpellFormConfig,
} from '@/Entities/spell/spell-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    spell: {
        type: Object,
        required: true,
    },
    availableBreeds: {
        type: Array,
        default: () => [],
    },
    availableSpellTypes: {
        type: Array,
        default: () => [],
    },
    availableEffects: { type: Array, default: () => [] },
    effectEntityType: { type: String, default: 'spell' },
    effectFormOptions: { type: Object, default: () => ({}) },
    spellEffectGroups: { type: Array, default: () => [] },
});

const fieldsConfig = computed(() =>
    mergeSpellTypesFieldIntoSpellFormConfig(
        buildSpellFormFieldsConfig({ includeReadonlyMeta: true }),
        props.availableSpellTypes || [],
    ),
);
const fieldSections = SPELL_FORM_FIELD_SECTIONS_EDIT;

const spell = computed(() => {
    const spellData = props.spell || page.props.spell || {};
    return new Spell(spellData);
});

setPageTitle(`Modifier le sort : ${spell.value.name || 'Nouveau sort'}`);
</script>

<template>
    <Head :title="`Modifier le sort : ${spell?.name || 'Nouveau sort'}`" />

    <Container class="space-y-8 pb-10">
        <Route route="entities.spells.index">
            <Btn color="neutral" variant="ghost" size="sm" class="gap-2">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Retour à la liste
            </Btn>
        </Route>

        <EntityEditForm
            :entity="spell"
            entity-type="spell"
            :fields-config="fieldsConfig"
            :is-updating="true"
            :hidden-field-keys="['dofus_version']"
            :field-sections="fieldSections"
            :show-state-toolbar="false"
            :show-access-levels-in-footer="false"
            characteristics-group="spell"
        />

        <EntityRelationsManager
            :relations="spell.breeds || []"
            :available-items="availableBreeds"
            :entity-id="spell.id"
            entity-type="spells"
            relation-type="breeds"
            relation-name="Classes pouvant utiliser ce sort"
            :config="{
                displayFields: ['name', 'description'],
                searchFields: ['name', 'description'],
                itemLabel: 'breed',
                itemLabelPlural: 'breeds',
            }"
        />

        <SpellEffectsUnifiedSection
            :available-effects="availableEffects"
            :effect-form-options="effectFormOptions"
            :spell-effect-groups="spellEffectGroups"
            :entity-type="effectEntityType"
            :entity-id="spell.id"
        />
    </Container>
</template>
