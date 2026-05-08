<script setup>
/**
 * Page d'édition d'un type de ressource.
 *
 * @description
 * Utilise le formulaire générique descriptor-driven pour aligner page et quick edit.
 *
 * @example
 * <EntityEditForm entity-type="resource-type" :entity="resourceType" />
 */
import { computed } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { ResourceType } from "@/Models/Entity/ResourceType";
import { getResourceTypeFieldDescriptors } from "@/Entities/resource-type/resource-type-descriptors";
import { createFieldsConfigFromDescriptors } from "@/Utils/entity/descriptor-form";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import EntityEditForm from "@/Pages/Organismes/entity/EntityEditForm.vue";

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    resourceType: {
        type: Object,
        required: true,
    },
});

const resourceType = computed(() => {
    const raw = props.resourceType || page.props.resourceType || {};
    return raw instanceof ResourceType ? raw : new ResourceType(raw);
});

const fieldsConfig = computed(() => {
    const ctx = { meta: { capabilities: { updateAny: true } } };
    return createFieldsConfigFromDescriptors(getResourceTypeFieldDescriptors(ctx), ctx);
});

setPageTitle(`Modifier le type de ressource : ${resourceType.value.name || "Sans nom"}`);
</script>

<template>
    <Head :title="`Modifier le type de ressource : ${resourceType?.name || 'Sans nom'}`" />

    <Container class="space-y-6 pb-32 md:pb-36">
        <EntityEditForm
            :entity="resourceType"
            entity-type="resource-type"
            :fields-config="fieldsConfig"
            route-name-base="entities.resource-types"
            route-param-key="resourceType"
            redirect-after-update="edit"
        />
    </Container>
</template>
