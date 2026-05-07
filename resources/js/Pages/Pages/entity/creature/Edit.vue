<script setup>
import { computed } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { Creature } from "@/Models/Entity/Creature";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import CreatureTraitsEditor from "@/Pages/Organismes/entity/CreatureTraitsEditor.vue";

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

setPageTitle(`Modifier la créature : ${creature.value.name || "-"}`);
</script>

<template>
    <Head :title="`Modifier la créature : ${creature?.name || 'Créature'}`" />

    <Container class="space-y-6 pb-28 md:pb-32">
        <Route route="entities.creatures.index">
            <Btn color="neutral" variant="ghost" size="sm" class="gap-2">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Retour à la liste
            </Btn>
        </Route>

        <div class="rounded-box border border-base-300 bg-base-100/40 p-4">
            <h1 class="text-xl font-semibold">{{ creature.name || "Créature" }}</h1>
            <p class="text-sm text-base-content/70 mt-1">
                Les traits ajoutés ici sont innés à la créature et n'ont pas de niveau d'activation.
            </p>
        </div>

        <CreatureTraitsEditor
            v-if="creature.id"
            :relations="creature.creatureTraits || []"
            :available-items="availableCreatureTraits"
            :entity-id="creature.id"
            route-name="entities.creatures.updateCreatureTraits"
            route-param-name="creature"
            title="Traits de créature"
            help="Traits permanents directement attachés à cette créature."
        />
    </Container>
</template>
