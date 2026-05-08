<script setup>
/**
 * Fiche lecture d'un état (Condition).
 *
 * @description
 * Page Inertia utilisée par `entities.conditions.show` pour l'ouverture en page complète.
 *
 * @example
 * <ConditionViewLarge :condition="condition" />
 */
import { computed } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { Condition } from "@/Models/Entity/Condition";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import EntityViewLargeWrapper from "@/Pages/Molecules/entity/shared/EntityViewLargeWrapper.vue";
import ConditionViewLarge from "@/Pages/Molecules/entity/condition/ConditionViewLarge.vue";

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

setPageTitle(`État : ${condition.value.name || "-"}`);
</script>

<template>
    <Head :title="`État : ${condition?.name || '-'}`" />

    <Container class="space-y-6 pb-8">
        <EntityViewLargeWrapper :show-back-button="true" back-route="entities.conditions.index">
            <ConditionViewLarge
                :condition="condition"
                :show-actions="true"
            />
        </EntityViewLargeWrapper>
    </Container>
</template>
