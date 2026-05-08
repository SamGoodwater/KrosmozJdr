<script setup>
/**
 * Page d’édition d’une capacité — formulaire structuré par sections.
 *
 * @props {Object} capability - Données CapabilityResource
 */
import { computed } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { Capability } from "@/Models/Entity/Capability";
import CapabilityEditFormContent from "@/Pages/Organismes/entity/CapabilityEditFormContent.vue";
import ConditionsEditor from "@/Pages/Organismes/entity/ConditionsEditor.vue";
import Container from "@/Pages/Atoms/data-display/Container.vue";

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    capability: {
        type: Object,
        required: true,
    },
    availableConditions: {
        type: Array,
        default: () => [],
    },
});

const capability = computed(() => {
    const raw = props.capability || page.props.capability || {};
    return raw instanceof Capability ? raw : new Capability(raw);
});

setPageTitle(`Modifier la capacité : ${capability.value.name || "Sans nom"}`);
</script>

<template>
    <Head :title="`Modifier la capacité : ${capability?.name || 'Sans nom'}`" />

    <Container class="space-y-6 pb-32 md:pb-36">
        <CapabilityEditFormContent :capability="capability" redirect-after-update="edit" />

        <ConditionsEditor
            v-if="capability.id"
            :relations="capability.conditions || []"
            :available-items="availableConditions"
            :entity-id="capability.id"
            route-name="entities.capabilities.updateConditions"
            route-param-name="capability"
            title="États appliqués"
            help="États que cette capacité peut appliquer. La description de la capacité précise leur interaction avec les créatures."
        />
    </Container>
</template>
