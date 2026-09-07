<script setup>
/**
 * Page d’édition d’un PNJ : identité Creature + coquille narrative + kit de jeu.
 */
import { computed } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { Npc } from "@/Models/Entity/Npc";
import { getEntityStateOptions } from "@/Utils/Entity/SharedConstants.js";
import EntityEditForm from "@/Pages/Organismes/entity/EntityEditForm.vue";
import EntityRelationsManager from "@/Pages/Organismes/entity/EntityRelationsManager.vue";
import EntityLanguagesEditor from "@/Pages/Organismes/entity/EntityLanguagesEditor.vue";
import CreatureTraitsEditor from "@/Pages/Organismes/entity/CreatureTraitsEditor.vue";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import Collapse from "@/Pages/Atoms/data-display/Collapse.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";

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

const SIZE_OPTIONS = [
    { value: 0, label: "Minuscule" },
    { value: 1, label: "Petit" },
    { value: 2, label: "Moyen" },
    { value: 3, label: "Grand" },
    { value: 4, label: "Colossal" },
    { value: 5, label: "Gigantesque" },
];

const ROLE_OPTIONS = [
    { value: "", label: "—" },
    { value: "social", label: "Social" },
    { value: "merchant", label: "Marchand" },
    { value: "guard", label: "Garde" },
    { value: "ally", label: "Allié" },
    { value: "enemy", label: "Ennemi" },
    { value: "other", label: "Autre" },
];

const HOSTILITY_OPTIONS = [
    { value: 0, label: "Amical" },
    { value: 1, label: "Curieux" },
    { value: 2, label: "Neutre" },
    { value: 3, label: "Hostile" },
    { value: 4, label: "Agressif" },
];

const fieldsConfig = computed(() => ({
    name: { type: "text", label: "Nom", required: true, showInCompact: true },
    location: { type: "text", label: "Lieu", required: false, showInCompact: true },
    level: { type: "text", label: "Niveau", required: false, showInCompact: true },
    hostility: {
        type: "select",
        label: "Hostilité",
        required: false,
        showInCompact: true,
        options: HOSTILITY_OPTIONS,
    },
    npc_role: {
        type: "select",
        label: "Rôle",
        required: false,
        showInCompact: true,
        options: ROLE_OPTIONS,
    },
    size: {
        type: "select",
        label: "Taille",
        required: false,
        showInCompact: true,
        options: SIZE_OPTIONS,
    },
    age: { type: "text", label: "Âge", required: false, showInCompact: true },
    breed_id: {
        type: "select",
        label: "Classe",
        required: false,
        showInCompact: true,
        options: [
            { value: "", label: "—" },
            ...props.availableBreeds.map((b) => ({ value: b.id, label: b.name })),
        ],
    },
    specialization_id: {
        type: "select",
        label: "Spécialisation",
        required: false,
        showInCompact: false,
        options: [
            { value: "", label: "—" },
            ...props.availableSpecializations.map((s) => ({ value: s.id, label: s.name })),
        ],
    },
    story: { type: "textarea", label: "Histoire", required: false, showInCompact: false },
    historical: { type: "textarea", label: "Historique", required: false, showInCompact: false },
    description: { type: "textarea", label: "Description", required: false, showInCompact: false },
    state: {
        type: "select",
        label: "État",
        required: false,
        showInCompact: true,
        options: getEntityStateOptions(),
        defaultValue: "draft",
    },
}));

const npc = computed(() => {
    const npcData = props.npc || page.props.npc || {};
    return npcData instanceof Npc ? npcData : new Npc(npcData);
});

const npcName = computed(() => npc.value.creature?.name || npc.value.name || "Nouveau PNJ");

const creatureSpells = computed(
    () => npc.value.creature?.spells ?? npc.value._data?.creature?.spells ?? [],
);
const creatureItems = computed(
    () => npc.value.creature?.items ?? npc.value._data?.creature?.items ?? [],
);

setPageTitle(`Modifier le PNJ : ${npcName.value}`);
</script>

<template>
    <Head :title="`Modifier le PNJ : ${npcName}`" />

    <Container class="space-y-6">
        <Route route="entities.npcs.index">
            <Btn color="neutral" variant="ghost" size="sm" class="gap-2">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Retour à la liste
            </Btn>
        </Route>

        <EntityEditForm
            :entity="npc"
            entity-type="npc"
            :fields-config="fieldsConfig"
            :is-updating="true"
            :fixed-footer-actions="true"
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
                <p v-if="npc.breedId" class="mt-2 text-xs text-base-content/60">
                    La liste proposée est préfiltrée sur la classe du PNJ ; la recherche catalogue
                    permet d’ajouter d’autres sorts.
                </p>
            </template>
        </Collapse>

        <Collapse arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Équipements</template>
            <template #content>
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
                <p class="mt-2 text-xs text-base-content/60">
                    Un objet par emplacement (deux anneaux). Les types hors équipement sont refusés.
                </p>
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
            <template #title>Scénarios</template>
            <template #content>
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
            </template>
        </Collapse>

        <Collapse arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Campagnes</template>
            <template #content>
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
            </template>
        </Collapse>
    </Container>
</template>
