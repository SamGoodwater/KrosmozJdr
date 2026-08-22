<script setup>
/**
 * Fiche lecture d’une panoplie (vue large).
 *
 * @description
 * Page Inertia utilisée par `entities.panoplies.show` (Agrandir, Ctrl+clic).
 *
 * @props {Object} panoply - Payload PanoplyResource
 */
import { computed } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { Panoply } from "@/Models/Entity/Panoply";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import EntityViewFullWrapper from "@/Pages/Molecules/entity/shared/EntityViewFullWrapper.vue";
import PanoplyViewFull from "@/Pages/Molecules/entity/panoply/PanoplyViewFull.vue";

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    panoply: {
        type: Object,
        required: true,
    },
});

const panoply = computed(() => {
    const raw = props.panoply || page.props.panoply || {};
    return raw instanceof Panoply ? raw : new Panoply(raw);
});

setPageTitle(`Panoplie : ${panoply.value.name || "-"}`);
</script>

<template>
    <Head :title="`Panoplie : ${panoply?.name || '-'}`" />

    <Container class="space-y-6 pb-8">
        <EntityViewFullWrapper :show-back-button="true" back-route="entities.panoplies.index">
            <PanoplyViewFull
                :panoply="panoply"
                :show-actions="true"
            />
        </EntityViewFullWrapper>
    </Container>
</template>
