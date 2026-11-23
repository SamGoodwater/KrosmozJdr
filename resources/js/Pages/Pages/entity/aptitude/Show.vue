<script setup>
/**
 * Page d'affichage d'un trait de créature.
 *
 * @description
 * Rend la vue large avec les actions en contexte page.
 *
 * @example
 * <AptitudeViewFull :aptitude="aptitude" />
 */
import { computed } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { Aptitude } from "@/Models/Entity/Aptitude";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import EntityViewFullWrapper from "@/Pages/Molecules/entity/shared/EntityViewFullWrapper.vue";
import AptitudeViewFull from "@/Pages/Molecules/entity/aptitude/AptitudeViewFull.vue";

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    aptitude: {
        type: Object,
        required: true,
    },
});

const aptitude = computed(() => {
    const raw = props.aptitude || page.props.aptitude || {};
    return raw instanceof Aptitude ? raw : new Aptitude(raw);
});

setPageTitle(`Aptitude : ${aptitude.value.name || "-"}`);
</script>

<template>
    <Head :title="`Aptitude : ${aptitude?.name || '-'}`" />

    <Container class="space-y-6 pb-8">
        <EntityViewFullWrapper :show-back-button="true" back-route="entities.aptitudes.index">
            <AptitudeViewFull :aptitude="aptitude" :show-actions="true" />
        </EntityViewFullWrapper>
    </Container>
</template>
