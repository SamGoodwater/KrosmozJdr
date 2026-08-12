<script setup>
/**
 * Monster Edit Page
 *
 * @description
 * Page d'édition d'un monstre via le formulaire d'entité générique.
 * Les blocs relationnels sont regroupés dans des Collapse pour alléger la lecture.
 *
 * @props {Object} monster - Données du monstre à éditer
 */
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Monster } from '@/Models/Entity/Monster';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import EntityLanguagesEditor from '@/Pages/Organismes/entity/EntityLanguagesEditor.vue';
import CreatureTraitsEditor from '@/Pages/Organismes/entity/CreatureTraitsEditor.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Collapse from '@/Pages/Atoms/data-display/Collapse.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Route from '@/Pages/Atoms/action/Route.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    monster: {
        type: Object,
        required: true
    },
    availableScenarios: {
        type: Array,
        default: () => []
    },
    availableCampaigns: {
        type: Array,
        default: () => []
    },
    availableSpells: {
        type: Array,
        default: () => []
    },
    availableLanguages: {
        type: Array,
        default: () => []
    },
    availableCreatureTraits: {
        type: Array,
        default: () => []
    }
});

const fieldsConfig = {
    size: {
        type: 'number',
        label: 'Taille',
        required: false,
        showInCompact: true
    },
    is_boss: {
        type: 'checkbox',
        label: 'Boss',
        required: false,
        showInCompact: true
    },
    boss_pa: {
        type: 'number',
        label: 'PA Boss',
        required: false,
        showInCompact: false
    }
};

const monster = computed(() => {
    const monsterData = props.monster || page.props.monster || {};
    return new Monster(monsterData);
});

const monsterName = computed(() => {
    return monster.value.creature?.name || 'Nouveau monstre';
});

setPageTitle(`Modifier le monstre : ${monsterName.value}`);
</script>

<template>
    <Head :title="`Modifier le monstre : ${monsterName}`" />

    <Container class="space-y-6">
        <Route route="entities.monsters.index">
            <Btn color="neutral" variant="ghost" size="sm" class="gap-2">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Retour à la liste
            </Btn>
        </Route>

        <div class="rounded-box bg-base-200 p-4">
            <p class="text-sm text-base-content/70">
                <strong>Note :</strong> Le nom et les statistiques (totaux / bonus contextuels) sont
                portés par la créature associée. Voir
                <code class="text-xs">docs/features/characteristics/COMPUTED_VALUES.md</code>.
            </p>
            <p v-if="monster.creature" class="mt-2">
                <strong>Créature associée :</strong> {{ monster.creature.name }}
            </p>
        </div>

        <EntityEditForm
            :entity="monster"
            entity-type="monster"
            :fields-config="fieldsConfig"
            :is-updating="true"
            :fixed-footer-actions="true"
        />

        <Collapse arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Scénarios</template>
            <template #content>
                <EntityRelationsManager
                    :relations="monster.scenarios || []"
                    :available-items="availableScenarios"
                    :entity-id="monster.id"
                    entity-type="monsters"
                    relation-type="scenarios"
                    relation-name="Scénarios du monstre"
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
                    :relations="monster.campaigns || []"
                    :available-items="availableCampaigns"
                    :entity-id="monster.id"
                    entity-type="monsters"
                    relation-type="campaigns"
                    relation-name="Campagnes du monstre"
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

        <Collapse arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Sorts d'invocation</template>
            <template #content>
                <EntityRelationsManager
                    :relations="monster.spellInvocations || []"
                    :available-items="availableSpells"
                    :entity-id="monster.id"
                    entity-type="monsters"
                    relation-type="spellInvocations"
                    relation-name="Sorts d'invocation du monstre"
                    :config="{
                        displayFields: ['name', 'description', 'level'],
                        searchFields: ['name', 'description'],
                        routeName: 'entities.monsters.updateSpellInvocations',
                        itemLabel: 'sort',
                        itemLabelPlural: 'sorts',
                        relatedEntityType: 'spells',
                        searchApiEntityType: 'spells',
                    }"
                />
            </template>
        </Collapse>

        <Collapse arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Langues &amp; traits</template>
            <template #content>
                <div class="space-y-4">
                    <EntityLanguagesEditor
                        v-if="monster.id"
                        entity-type="monster"
                        :relations="monster.languages || []"
                        :available-items="availableLanguages"
                        :entity-id="monster.id"
                    />

                    <CreatureTraitsEditor
                        v-if="monster.id"
                        :relations="monster.creature?.creatureTraits || monster.creatureTraits || []"
                        :available-items="availableCreatureTraits"
                        :entity-id="monster.id"
                        route-name="entities.monsters.updateCreatureTraits"
                        route-param-name="monster"
                        title="Traits du monstre"
                        help="Traits innés du monstre. Ces traits sont attachés directement à sa créature et n'ont pas de niveau d'activation."
                    />
                </div>
            </template>
        </Collapse>
    </Container>
</template>
