<script setup>
/**
 * Page d'édition d'un trait de créature.
 *
 * @description
 * Utilise le formulaire générique descriptor-driven pour garder la même source de vérité que le quick edit.
 *
 * @example
 * <EntityEditForm entity-type="creature-trait" :entity="creatureTrait" />
 */
import { computed } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { CreatureTrait } from "@/Models/Entity/CreatureTrait";
import { getCreatureTraitFieldDescriptors } from "@/Entities/creature-trait/creature-trait-descriptors";
import { createFieldsConfigFromDescriptors } from "@/Utils/entity/descriptor-form";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import EntityEditForm from "@/Pages/Organismes/entity/EntityEditForm.vue";

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    creatureTrait: {
        type: Object,
        required: true,
    },
});

const creatureTrait = computed(() => {
    const raw = props.creatureTrait || page.props.creatureTrait || {};
    return raw instanceof CreatureTrait ? raw : new CreatureTrait(raw);
});

const fieldsConfig = computed(() => {
    const ctx = { meta: { capabilities: { updateAny: true } } };
    return createFieldsConfigFromDescriptors(getCreatureTraitFieldDescriptors(ctx), ctx);
});

setPageTitle(`Modifier le trait : ${creatureTrait.value.name || "Sans nom"}`);
</script>

<template>
    <Head :title="`Modifier le trait : ${creatureTrait?.name || 'Sans nom'}`" />

    <Container class="space-y-6 pb-32 md:pb-36">
        <EntityEditForm
            :entity="creatureTrait"
            entity-type="creature-trait"
            :fields-config="fieldsConfig"
            redirect-after-update="edit"
        />
    </Container>
</template>
