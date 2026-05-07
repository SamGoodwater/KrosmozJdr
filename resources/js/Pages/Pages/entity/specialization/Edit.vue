<script setup>
import { computed } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { Specialization } from "@/Models/Entity/Specialization";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Route from "@/Pages/Atoms/action/Route.vue";
import CreatureTraitsEditor from "@/Pages/Organismes/entity/CreatureTraitsEditor.vue";

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    specialization: { type: Object, required: true },
    availableCreatureTraits: { type: Array, default: () => [] },
});

const specialization = computed(() => {
    const raw = props.specialization || page.props.specialization || {};
    return raw instanceof Specialization ? raw : new Specialization(raw);
});

setPageTitle(`Modifier la spécialisation : ${specialization.value.name || "-"}`);
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

        <div class="rounded-box border border-base-300 bg-base-100/40 p-4">
            <h1 class="text-xl font-semibold">{{ specialization.name || "Spécialisation" }}</h1>
            <p class="text-sm text-base-content/70 mt-1">
                Les traits de spécialisation s'activent au niveau indiqué.
            </p>
        </div>

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
    </Container>
</template>
