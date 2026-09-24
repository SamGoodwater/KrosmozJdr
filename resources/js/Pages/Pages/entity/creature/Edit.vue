<script setup>
/**
 * Page d’édition légère d’une créature (traits innés).
 * Pas de formulaire EntityEditForm : identité affichée + éditeur de traits.
 */
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { Creature } from '@/Models/Entity/Creature';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Collapse from '@/Pages/Atoms/data-display/Collapse.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityListBackButton from '@/Pages/Atoms/action/EntityListBackButton.vue';
import CreatureTraitsEditor from '@/Pages/Organismes/entity/CreatureTraitsEditor.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    creature: { type: Object, required: true },
    availableCreatureTraits: { type: Array, default: () => [] },
});

const creature = computed(() => {
    const raw = props.creature || page.props.creature || {};
    return raw instanceof Creature ? raw : new Creature(raw);
});

setPageTitle(`Modifier la créature : ${creature.value.name || '-'}`);

function goToShow() {
    const id = creature.value?.id;
    if (!id) return;
    router.visit(route('entities.creatures.show', { creature: id }));
}
</script>

<template>
    <Head :title="`Modifier la créature : ${creature?.name || 'Créature'}`" />

    <Container class="space-y-4 pb-28 md:pb-32">
        <div
            class="sticky top-0 z-20 px-3 py-1 bg-glass-3xl backdrop-blur-md border-glass-b-md sm:px-4"
            style="--bg-color: var(--color-base-100)"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-md font-bold text-base-content sm:text-lg">
                        {{ creature.name || 'Créature sans nom' }}
                    </h1>
                    <p class="text-xs text-base-content/60">
                        Édition · ID {{ creature.id }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <EntityListBackButton route-name="entities.creatures.index" />
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
            Les traits ajoutés ici sont innés à la créature et n’ont pas de niveau d’activation.
        </p>

        <Collapse v-if="creature.id" arrow bg-off="bg-base-100" class="border border-base-300">
            <template #title>Traits de créature</template>
            <template #content>
                <CreatureTraitsEditor
                    :relations="creature.creatureTraits || []"
                    :available-items="availableCreatureTraits"
                    :entity-id="creature.id"
                    route-name="entities.creatures.updateCreatureTraits"
                    route-param-name="creature"
                    title="Traits de créature"
                    help="Traits permanents directement attachés à cette créature."
                />
            </template>
        </Collapse>
    </Container>
</template>
