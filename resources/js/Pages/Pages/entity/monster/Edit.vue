<script setup>
/**
 * Monster Edit Page
 *
 * @description
 * Édition dense d’un monstre : coquille dense + sous-managers créature en collapses.
 *
 * @props {Object} monster - Données du monstre à éditer
 */
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Monster } from '@/Models/Entity/Monster';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import EntityLanguagesEditor from '@/Pages/Organismes/entity/EntityLanguagesEditor.vue';
import CreatureTraitsEditor from '@/Pages/Organismes/entity/CreatureTraitsEditor.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Collapse from '@/Pages/Atoms/data-display/Collapse.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityListBackButton from '@/Pages/Atoms/action/EntityListBackButton.vue';
import {
    buildMonsterFormFieldsConfig,
    MONSTER_FORM_FIELD_SECTIONS_EDIT,
} from '@/Entities/monster/monster-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    monster: {
        type: Object,
        required: true,
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
    availableLanguages: {
        type: Array,
        default: () => [],
    },
    availableCreatureTraits: {
        type: Array,
        default: () => [],
    },
});

const fieldsConfig = computed(() => buildMonsterFormFieldsConfig());
const fieldSections = MONSTER_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';

const monster = computed(() => {
    const monsterData = props.monster || page.props.monster || {};
    return new Monster(monsterData);
});

const monsterName = computed(() => {
    return monster.value.creature?.name || 'Nouveau monstre';
});

setPageTitle(`Modifier le monstre : ${monsterName.value}`);

function goToShow() {
    const id = monster.value?.id;
    if (!id) return;
    router.visit(route('entities.monsters.show', { monster: id }));
}
</script>

<template>
    <Head :title="`Modifier le monstre : ${monsterName}`" />

    <Container class="space-y-4">
        <div
            class="sticky top-0 z-20 px-3 py-1 bg-glass-3xl backdrop-blur-md border-glass-b-md sm:px-4"
            style="--bg-color: var(--color-base-100)"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-md font-bold text-base-content sm:text-lg">
                        {{ monsterName }}
                    </h1>
                    <p class="text-xs text-base-content/60">
                        Édition · ID {{ monster.id }}
                        <span v-if="monster.creature">
                            · Créature : {{ monster.creature.name }}
                        </span>
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <EntityListBackButton route-name="entities.monsters.index" />
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

        <p class="text-xs text-base-content/60 px-1">
            Nom et statistiques (totaux / bonus) sont portés par la créature associée.
        </p>

        <EntityEditForm
            :entity="monster"
            entity-type="monster"
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
            <template #title>Scénarios &amp; campagnes</template>
            <template #content>
                <div class="space-y-4">
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
                </div>
            </template>
        </Collapse>
    </Container>
</template>
