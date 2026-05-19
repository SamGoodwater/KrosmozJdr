<script setup>
/**
 * Page d'affichage d'un trait de créature.
 *
 * @description
 * Rend la vue large avec les actions en contexte page.
 *
 * @example
 * <CreatureTraitViewFull :creature-trait="creatureTrait" />
 */
import { computed } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { CreatureTrait } from "@/Models/Entity/CreatureTrait";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import EntityViewFullWrapper from "@/Pages/Molecules/entity/shared/EntityViewFullWrapper.vue";
import CreatureTraitViewFull from "@/Pages/Molecules/entity/creature-trait/CreatureTraitViewFull.vue";

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

setPageTitle(`Trait : ${creatureTrait.value.name || "-"}`);
</script>

<template>
    <Head :title="`Trait : ${creatureTrait?.name || '-'}`" />

    <Container class="space-y-6 pb-8">
        <EntityViewFullWrapper :show-back-button="true" back-route="entities.creature-traits.index">
            <CreatureTraitViewFull :creature-trait="creatureTrait" :show-actions="true" />
        </EntityViewFullWrapper>
    </Container>
</template>
