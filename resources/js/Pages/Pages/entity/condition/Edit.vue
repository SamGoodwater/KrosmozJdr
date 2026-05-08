<script setup>
/**
 * Page d'édition d'un état (Condition).
 *
 * @description
 * Utilise le formulaire générique descriptor-driven pour garder la même source de vérité que le quick edit.
 *
 * @example
 * <EntityEditForm entity-type="condition" :entity="condition" />
 */
import { computed } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { Condition } from "@/Models/Entity/Condition";
import { getConditionFieldDescriptors } from "@/Entities/condition/condition-descriptors";
import { createFieldsConfigFromDescriptors } from "@/Utils/entity/descriptor-form";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import EntityEditForm from "@/Pages/Organismes/entity/EntityEditForm.vue";

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    condition: {
        type: Object,
        required: true,
    },
});

const condition = computed(() => {
    const raw = props.condition || page.props.condition || {};
    return raw instanceof Condition ? raw : new Condition(raw);
});

const fieldsConfig = computed(() => {
    const ctx = { meta: { capabilities: { updateAny: true } } };
    return createFieldsConfigFromDescriptors(getConditionFieldDescriptors(ctx), ctx);
});

setPageTitle(`Modifier l'état : ${condition.value.name || "Sans nom"}`);
</script>

<template>
    <Head :title="`Modifier l'état : ${condition?.name || 'Sans nom'}`" />

    <Container class="space-y-6 pb-32 md:pb-36">
        <EntityEditForm
            :entity="condition"
            entity-type="condition"
            :fields-config="fieldsConfig"
            redirect-after-update="edit"
        />
    </Container>
</template>
